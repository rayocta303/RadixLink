<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\BandwidthProfile;
use App\Models\Tenant\HotspotProfile;
use App\Models\Tenant\IpPool;
use App\Models\Tenant\Nas;
use App\Models\Tenant\PppoeProfile;
use App\Models\Tenant\ServicePlan;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServicePlanController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.services.index', [
                'services' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $services = ServicePlan::orderBy('name')->get();
        return view('tenant.services.index', compact('services'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $routers = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        $pppoeProfiles = PppoeProfile::where('is_active', true)->orderBy('name')->get();
        $hotspotProfiles = HotspotProfile::where('is_active', true)->orderBy('name')->get();

        return view('tenant.services.create', compact(
            'routers',
            'ipPools',
            'bandwidths',
            'pppoeProfiles',
            'hotspotProfiles'
        ));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:tenant.service_plans,code',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:hotspot,pppoe,dhcp,hybrid',
            'price' => 'required|numeric|min:0',
            'validity' => 'required|integer|min:1',
            'validity_unit' => 'required|in:minutes,hours,days,months',
            'bandwidth_down' => 'required|string|max:20',
            'bandwidth_up' => 'required|string|max:20',
            'quota_bytes' => 'nullable|integer|min:0',
            'has_fup' => 'boolean',
            'fup_bandwidth_down' => 'nullable|string|max:20',
            'fup_bandwidth_up' => 'nullable|string|max:20',
            'fup_threshold_bytes' => 'nullable|integer|min:0',
            'can_share' => 'boolean',
            'max_devices' => 'nullable|integer|min:1|max:100',
            'simultaneous_use' => 'nullable|integer|min:1|max:100',
            'is_active' => 'boolean',
            'router_name' => 'nullable|string|max:100',
            'pool' => 'nullable|string|max:100',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'pppoe_profile_id' => 'nullable|exists:tenant.pppoe_profiles,id',
            'hotspot_profile_id' => 'nullable|exists:tenant.hotspot_profiles,id',
            'prepaid' => 'boolean',
            'enabled' => 'boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'SVC-' . strtoupper(Str::random(8));
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['has_fup'] = $request->boolean('has_fup', false);
        $validated['can_share'] = $request->boolean('can_share', false);
        $validated['prepaid'] = $request->boolean('prepaid', true);
        $validated['enabled'] = $request->boolean('enabled', true);
        $validated['max_devices'] = $validated['max_devices'] ?? 1;
        $validated['simultaneous_use'] = $validated['simultaneous_use'] ?? 1;

        if ($request->filled('quota_gb') && $request->quota_gb > 0) {
            $validated['quota_bytes'] = $request->quota_gb * 1073741824;
        }

        ServicePlan::create($validated);

        return redirect()->route('tenant.services.index')
            ->with('success', 'Paket layanan berhasil ditambahkan.');
    }

    public function show($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $service = ServicePlan::withCount('customers')->findOrFail($id);
        return view('tenant.services.show', compact('service'));
    }

    public function edit($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $service = ServicePlan::findOrFail($id);
        $routers = Nas::where('is_active', true)->orderBy('name')->get();
        $ipPools = IpPool::where('is_active', true)->orderBy('name')->get();
        $bandwidths = BandwidthProfile::where('is_active', true)->orderBy('name')->get();
        $pppoeProfiles = PppoeProfile::where('is_active', true)->orderBy('name')->get();
        $hotspotProfiles = HotspotProfile::where('is_active', true)->orderBy('name')->get();

        return view('tenant.services.edit', compact(
            'service',
            'routers',
            'ipPools',
            'bandwidths',
            'pppoeProfiles',
            'hotspotProfiles'
        ));
    }

    public function update(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $service = ServicePlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:tenant.service_plans,code,' . $id,
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:hotspot,pppoe,dhcp,hybrid',
            'price' => 'required|numeric|min:0',
            'validity' => 'required|integer|min:1',
            'validity_unit' => 'required|in:minutes,hours,days,months',
            'bandwidth_down' => 'required|string|max:20',
            'bandwidth_up' => 'required|string|max:20',
            'quota_bytes' => 'nullable|integer|min:0',
            'has_fup' => 'boolean',
            'fup_bandwidth_down' => 'nullable|string|max:20',
            'fup_bandwidth_up' => 'nullable|string|max:20',
            'fup_threshold_bytes' => 'nullable|integer|min:0',
            'can_share' => 'boolean',
            'max_devices' => 'nullable|integer|min:1|max:100',
            'simultaneous_use' => 'nullable|integer|min:1|max:100',
            'is_active' => 'boolean',
            'router_name' => 'nullable|string|max:100',
            'pool' => 'nullable|string|max:100',
            'bandwidth_id' => 'nullable|exists:tenant.bandwidth_profiles,id',
            'ip_pool_id' => 'nullable|exists:tenant.ip_pools,id',
            'pppoe_profile_id' => 'nullable|exists:tenant.pppoe_profiles,id',
            'hotspot_profile_id' => 'nullable|exists:tenant.hotspot_profiles,id',
            'prepaid' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_fup'] = $request->boolean('has_fup', false);
        $validated['can_share'] = $request->boolean('can_share', false);
        $validated['prepaid'] = $request->boolean('prepaid', true);
        $validated['enabled'] = $request->boolean('enabled', true);

        if ($request->filled('quota_gb') && $request->quota_gb > 0) {
            $validated['quota_bytes'] = $request->quota_gb * 1073741824;
        } elseif ($request->quota_gb == 0) {
            $validated['quota_bytes'] = null;
        }

        $service->update($validated);

        return redirect()->route('tenant.services.index')
            ->with('success', 'Paket layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.services.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $service = ServicePlan::findOrFail($id);
        
        if ($service->customers()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus paket yang masih memiliki pelanggan.');
        }

        $service->delete();

        return redirect()->route('tenant.services.index')
            ->with('success', 'Paket layanan berhasil dihapus.');
    }
}
