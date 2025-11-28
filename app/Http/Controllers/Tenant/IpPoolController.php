<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\IpPool;
use App\Models\Tenant\Nas;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IpPoolController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.ip-pools.index', [
                'pools' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }
        
        $pools = IpPool::with('nas')->orderBy('name')->get();
        return view('tenant.ip-pools.index', compact('pools'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        return view('tenant.ip-pools.create', compact('nasList'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pool_name' => 'required|string|max:64|unique:tenant.ip_pools,pool_name',
            'range_start' => 'required|ip',
            'range_end' => 'required|ip',
            'next_pool' => 'nullable|string|max:64',
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'type' => 'required|in:hotspot,pppoe,both',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        $pool = IpPool::create($validated);
        $pool->updateTotalIps();

        return redirect()->route('tenant.ip-pools.index')
            ->with('success', 'IP Pool berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $pool = IpPool::with(['nas', 'pppoeProfiles', 'hotspotProfiles'])->findOrFail($id);
        return view('tenant.ip-pools.show', compact('pool'));
    }

    public function edit(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $pool = IpPool::findOrFail($id);
        $nasList = Nas::where('is_active', true)->orderBy('name')->get();
        return view('tenant.ip-pools.edit', compact('pool', 'nasList'));
    }

    public function update(Request $request, int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $pool = IpPool::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pool_name' => ['required', 'string', 'max:64', Rule::unique('tenant.ip_pools')->ignore($pool->id)],
            'range_start' => 'required|ip',
            'range_end' => 'required|ip',
            'next_pool' => 'nullable|string|max:64',
            'nas_id' => 'nullable|exists:tenant.nas,id',
            'type' => 'required|in:hotspot,pppoe,both',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        
        $pool->update($validated);
        $pool->updateTotalIps();

        return redirect()->route('tenant.ip-pools.index')
            ->with('success', 'IP Pool berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }
        
        $pool = IpPool::findOrFail($id);
        
        if ($pool->pppoeProfiles()->count() > 0 || $pool->hotspotProfiles()->count() > 0) {
            return redirect()->route('tenant.ip-pools.index')
                ->with('error', 'IP Pool tidak dapat dihapus karena masih digunakan oleh profil.');
        }
        
        $pool->delete();

        return redirect()->route('tenant.ip-pools.index')
            ->with('success', 'IP Pool berhasil dihapus.');
    }
}
