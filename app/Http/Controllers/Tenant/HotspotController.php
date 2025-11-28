<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\HotspotProfile;
use App\Models\Tenant\HotspotServer;
use App\Models\Tenant\Nas;
use App\Models\Tenant\IpPool;
use App\Models\Tenant\BandwidthProfile;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HotspotController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.hotspot.index', [
                'profiles' => collect(),
                'servers' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }
        
        $profiles = HotspotProfile::with(['nas', 'ipPool', 'bandwidth'])->orderBy('name')->get();
        $servers = HotspotServer::with(['nas', 'hotspotProfile', 'ipPool'])->orderBy('name')->get();
        
        return view('tenant.hotspot.index', compact('profiles', 'servers'));
    }

    public function createProfile()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['hotspot', 'both'])->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.hotspot.create-profile', compact('nasList', 'ipPools', 'bandwidths'));
    }

    public function storeProfile(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_name' => 'required|string|max:64|unique:tenant.hotspot_profiles,profile_name',
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'shared_users' => 'nullable|integer|min:1',
            'session_timeout' => 'nullable|integer|min:0',
            'idle_timeout' => 'nullable|integer|min:0',
            'keepalive_timeout' => 'nullable|integer|min:0',
            'status_autorefresh' => 'nullable|string|max:10',
            'transparent_proxy' => 'boolean',
            'mac_cookie_timeout' => 'nullable|string|max:10',
            'parent_queue' => 'nullable|string|max:64',
            'address_list' => 'nullable|string|max:64',
            'incoming_filter' => 'nullable|string|max:64',
            'outgoing_filter' => 'nullable|string|max:64',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['transparent_proxy'] = $request->boolean('transparent_proxy');
        $validated['shared_users'] = $request->input('shared_users', 1);
        
        HotspotProfile::create($validated);

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Profil Hotspot berhasil ditambahkan.');
    }

    public function editProfile(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = HotspotProfile::findOrFail($id);
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['hotspot', 'both'])->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.hotspot.edit-profile', compact('profile', 'nasList', 'ipPools', 'bandwidths'));
    }

    public function updateProfile(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = HotspotProfile::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_name' => ['required', 'string', 'max:64', Rule::unique('tenant.hotspot_profiles')->ignore($profile->id)],
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'shared_users' => 'nullable|integer|min:1',
            'session_timeout' => 'nullable|integer|min:0',
            'idle_timeout' => 'nullable|integer|min:0',
            'keepalive_timeout' => 'nullable|integer|min:0',
            'status_autorefresh' => 'nullable|string|max:10',
            'transparent_proxy' => 'boolean',
            'mac_cookie_timeout' => 'nullable|string|max:10',
            'parent_queue' => 'nullable|string|max:64',
            'address_list' => 'nullable|string|max:64',
            'incoming_filter' => 'nullable|string|max:64',
            'outgoing_filter' => 'nullable|string|max:64',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['transparent_proxy'] = $request->boolean('transparent_proxy');
        
        $profile->update($validated);

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Profil Hotspot berhasil diperbarui.');
    }

    public function destroyProfile(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = HotspotProfile::findOrFail($id);
        
        if ($profile->customers()->count() > 0) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Profil Hotspot tidak dapat dihapus karena masih digunakan oleh pelanggan.');
        }
        
        $profile->delete();

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Profil Hotspot berhasil dihapus.');
    }

    public function createServer()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $profiles = HotspotProfile::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['hotspot', 'both'])->orderBy('name')->get();
        
        return view('tenant.hotspot.create-server', compact('nasList', 'profiles', 'ipPools'));
    }

    public function storeServer(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nas_id' => 'required|exists:tenant.nas,id',
            'interface' => 'required|string|max:64',
            'address_pool' => 'nullable|string|max:64',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'hotspot_profile_id' => 'nullable|exists:tenant.hotspot_profiles,id',
            'login_by' => 'nullable|string|max:128',
            'http_cookie_lifetime' => 'nullable|string|max:10',
            'split_user_domain' => 'nullable|string|max:64',
            'https' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['https'] = $request->boolean('https');
        $validated['login_by'] = $request->input('login_by', 'cookie,http-chap,http-pap');
        $validated['http_cookie_lifetime'] = $request->input('http_cookie_lifetime', '3d');
        
        HotspotServer::create($validated);

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Server Hotspot berhasil ditambahkan.');
    }

    public function editServer(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = HotspotServer::findOrFail($id);
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $profiles = HotspotProfile::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['hotspot', 'both'])->orderBy('name')->get();
        
        return view('tenant.hotspot.edit-server', compact('server', 'nasList', 'profiles', 'ipPools'));
    }

    public function updateServer(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = HotspotServer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nas_id' => 'required|exists:tenant.nas,id',
            'interface' => 'required|string|max:64',
            'address_pool' => 'nullable|string|max:64',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'hotspot_profile_id' => 'nullable|exists:tenant.hotspot_profiles,id',
            'login_by' => 'nullable|string|max:128',
            'http_cookie_lifetime' => 'nullable|string|max:10',
            'split_user_domain' => 'nullable|string|max:64',
            'https' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['https'] = $request->boolean('https');
        
        $server->update($validated);

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Server Hotspot berhasil diperbarui.');
    }

    public function destroyServer(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.hotspot.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = HotspotServer::findOrFail($id);
        $server->delete();

        return redirect()->route('tenant.hotspot.index')
            ->with('success', 'Server Hotspot berhasil dihapus.');
    }

    public function generateScript(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'error' => 'Database tenant belum dikonfigurasi.',
            ], 400);
        }
        
        $profile = HotspotProfile::with(['nas', 'ipPool', 'bandwidth'])->findOrFail($id);
        $script = $profile->toMikrotikCommand();
        
        return response()->json([
            'success' => true,
            'script' => $script,
        ]);
    }
}
