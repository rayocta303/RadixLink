<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\BandwidthProfile;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BandwidthController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.bandwidth.index', [
                'profiles' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }
        
        $profiles = BandwidthProfile::orderBy('name')->get();
        return view('tenant.bandwidth.index', compact('profiles'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        return view('tenant.bandwidth.create');
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bw' => 'required|string|max:64|unique:tenant.bandwidth_profiles,name_bw',
            'rate_up' => 'required|string|max:20',
            'rate_down' => 'required|string|max:20',
            'burst_limit_up' => 'nullable|string|max:20',
            'burst_limit_down' => 'nullable|string|max:20',
            'burst_threshold_up' => 'nullable|string|max:20',
            'burst_threshold_down' => 'nullable|string|max:20',
            'burst_time_up' => 'nullable|string|max:10',
            'burst_time_down' => 'nullable|string|max:10',
            'priority' => 'nullable|integer|min:1|max:8',
            'limit_at_up' => 'nullable|string|max:20',
            'limit_at_down' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $request->input('priority', 8);
        
        BandwidthProfile::create($validated);

        return redirect()->route('tenant.bandwidth.index')
            ->with('success', 'Profil Bandwidth berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = BandwidthProfile::with(['servicePlans', 'pppoeProfiles', 'hotspotProfiles'])->findOrFail($id);
        return view('tenant.bandwidth.show', compact('profile'));
    }

    public function edit(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = BandwidthProfile::findOrFail($id);
        return view('tenant.bandwidth.edit', compact('profile'));
    }

    public function update(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = BandwidthProfile::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bw' => ['required', 'string', 'max:64', Rule::unique('tenant.bandwidth_profiles')->ignore($profile->id)],
            'rate_up' => 'required|string|max:20',
            'rate_down' => 'required|string|max:20',
            'burst_limit_up' => 'nullable|string|max:20',
            'burst_limit_down' => 'nullable|string|max:20',
            'burst_threshold_up' => 'nullable|string|max:20',
            'burst_threshold_down' => 'nullable|string|max:20',
            'burst_time_up' => 'nullable|string|max:10',
            'burst_time_down' => 'nullable|string|max:10',
            'priority' => 'nullable|integer|min:1|max:8',
            'limit_at_up' => 'nullable|string|max:20',
            'limit_at_down' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        
        $profile->update($validated);

        return redirect()->route('tenant.bandwidth.index')
            ->with('success', 'Profil Bandwidth berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $profile = BandwidthProfile::findOrFail($id);
        
        if ($profile->servicePlans()->count() > 0) {
            return redirect()->route('tenant.bandwidth.index')
                ->with('error', 'Profil Bandwidth tidak dapat dihapus karena masih digunakan oleh paket layanan.');
        }
        
        $profile->delete();

        return redirect()->route('tenant.bandwidth.index')
            ->with('success', 'Profil Bandwidth berhasil dihapus.');
    }
}
