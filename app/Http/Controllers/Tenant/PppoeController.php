<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PppoeProfile;
use App\Models\Tenant\PppoeServer;
use App\Models\Tenant\Nas;
use App\Models\Tenant\IpPool;
use App\Models\Tenant\BandwidthProfile;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PppoeController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.pppoe.index', [
                'profiles' => collect(),
                'servers' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }
        
        $profiles = PppoeProfile::with(['nas', 'ipPool', 'bandwidth'])->orderBy('name')->get();
        $servers = PppoeServer::with(['nas', 'pppoeProfile'])->orderBy('name')->get();
        
        return view('tenant.pppoe.index', compact('profiles', 'servers'));
    }

    public function createProfile()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['pppoe', 'both'])->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.pppoe.create-profile', compact('nasList', 'ipPools', 'bandwidths'));
    }

    public function storeProfile(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_name' => 'required|string|max:64|unique:tenant.pppoe_profiles,profile_name',
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'local_address' => 'nullable|ip',
            'remote_address' => 'nullable|string|max:64',
            'dns_server' => 'nullable|string|max:128',
            'wins_server' => 'nullable|string|max:128',
            'session_timeout' => 'nullable|integer|min:0',
            'idle_timeout' => 'nullable|integer|min:0',
            'only_one' => 'boolean',
            'parent_queue' => 'nullable|string|max:64',
            'address_list' => 'nullable|string|max:64',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['only_one'] = $request->boolean('only_one', true);
        
        PppoeProfile::create($validated);

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Profil PPPoE berhasil ditambahkan.');
    }

    public function editProfile(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = PppoeProfile::findOrFail($id);
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->whereIn('type', ['pppoe', 'both'])->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.pppoe.edit-profile', compact('profile', 'nasList', 'ipPools', 'bandwidths'));
    }

    public function updateProfile(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = PppoeProfile::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_name' => ['required', 'string', 'max:64', Rule::unique('tenant.pppoe_profiles')->ignore($profile->id)],
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'local_address' => 'nullable|ip',
            'remote_address' => 'nullable|string|max:64',
            'dns_server' => 'nullable|string|max:128',
            'wins_server' => 'nullable|string|max:128',
            'session_timeout' => 'nullable|integer|min:0',
            'idle_timeout' => 'nullable|integer|min:0',
            'only_one' => 'boolean',
            'parent_queue' => 'nullable|string|max:64',
            'address_list' => 'nullable|string|max:64',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['only_one'] = $request->boolean('only_one');
        
        $profile->update($validated);

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Profil PPPoE berhasil diperbarui.');
    }

    public function destroyProfile(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = PppoeProfile::findOrFail($id);
        
        if ($profile->customers()->count() > 0) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Profil PPPoE tidak dapat dihapus karena masih digunakan oleh pelanggan.');
        }
        
        $profile->delete();

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Profil PPPoE berhasil dihapus.');
    }

    public function createServer()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $profiles = PppoeProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.pppoe.create-server', compact('nasList', 'profiles'));
    }

    public function storeServer(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nas_id' => 'required|exists:tenant.nas,id',
            'service_name' => 'required|string|max:64',
            'interface' => 'required|string|max:64',
            'max_mtu' => 'nullable|integer|min:68|max:65535',
            'max_mru' => 'nullable|integer|min:68|max:65535',
            'max_sessions' => 'nullable|integer|min:0',
            'pppoe_profile_id' => 'nullable|exists:tenant.pppoe_profiles,id',
            'authentication' => 'nullable|string|max:128',
            'keepalive' => 'boolean',
            'one_session_per_host' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['keepalive'] = $request->boolean('keepalive', true);
        $validated['one_session_per_host'] = $request->boolean('one_session_per_host');
        $validated['max_mtu'] = $request->input('max_mtu', 1480);
        $validated['max_mru'] = $request->input('max_mru', 1480);
        
        PppoeServer::create($validated);

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Server PPPoE berhasil ditambahkan.');
    }

    public function editServer(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = PppoeServer::findOrFail($id);
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        $profiles = PppoeProfile::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.pppoe.edit-server', compact('server', 'nasList', 'profiles'));
    }

    public function updateServer(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = PppoeServer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nas_id' => 'required|exists:tenant.nas,id',
            'service_name' => 'required|string|max:64',
            'interface' => 'required|string|max:64',
            'max_mtu' => 'nullable|integer|min:68|max:65535',
            'max_mru' => 'nullable|integer|min:68|max:65535',
            'max_sessions' => 'nullable|integer|min:0',
            'pppoe_profile_id' => 'nullable|exists:tenant.pppoe_profiles,id',
            'authentication' => 'nullable|string|max:128',
            'keepalive' => 'boolean',
            'one_session_per_host' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['keepalive'] = $request->boolean('keepalive');
        $validated['one_session_per_host'] = $request->boolean('one_session_per_host');
        
        $server->update($validated);

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Server PPPoE berhasil diperbarui.');
    }

    public function destroyServer(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.pppoe.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $server = PppoeServer::findOrFail($id);
        $server->delete();

        return redirect()->route('tenant.pppoe.index')
            ->with('success', 'Server PPPoE berhasil dihapus.');
    }

    public function generateScript(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'error' => 'Database tenant belum dikonfigurasi.',
            ], 400);
        }
        
        $profile = PppoeProfile::with(['nas', 'ipPool', 'bandwidth'])->findOrFail($id);
        $script = $profile->toMikrotikCommand();
        
        return response()->json([
            'success' => true,
            'script' => $script,
        ]);
    }
}
