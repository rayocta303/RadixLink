<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Nas;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NasController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.nas.index', [
                'nasList' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi. Silakan hubungi administrator.',
            ]);
        }
        
        $nasList = Nas::orderBy('name')->get();
        return view('tenant.nas.index', compact('nasList'));
    }

    public function create()
    {
        return view('tenant.nas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortname' => 'required|string|max:64|unique:tenant.nas,shortname',
            'nasname' => 'required|string|max:128',
            'ports' => 'nullable|integer',
            'secret' => 'required|string|max:60',
            'server' => 'nullable|string|max:64',
            'community' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:mikrotik,unifi,openwrt,cisco,other',
            'api_username' => 'nullable|string|max:64',
            'api_password' => 'nullable|string|max:64',
            'api_port' => 'nullable|integer|min:1|max:65535',
            'use_ssl' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['use_ssl'] = $request->boolean('use_ssl');
        $validated['is_active'] = $request->boolean('is_active', true);

        Nas::create($validated);

        return redirect()->route('tenant.nas.index')
            ->with('success', 'Router/NAS berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $nas = Nas::findOrFail($id);
        return view('tenant.nas.show', compact('nas'));
    }

    public function edit(int $id)
    {
        $nas = Nas::findOrFail($id);
        return view('tenant.nas.edit', compact('nas'));
    }

    public function update(Request $request, int $id)
    {
        $nas = Nas::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortname' => ['required', 'string', 'max:64', Rule::unique('tenant.nas')->ignore($nas->id)],
            'nasname' => 'required|string|max:128',
            'ports' => 'nullable|integer',
            'secret' => 'required|string|max:60',
            'server' => 'nullable|string|max:64',
            'community' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:mikrotik,unifi,openwrt,cisco,other',
            'api_username' => 'nullable|string|max:64',
            'api_password' => 'nullable|string|max:64',
            'api_port' => 'nullable|integer|min:1|max:65535',
            'use_ssl' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['use_ssl'] = $request->boolean('use_ssl');
        $validated['is_active'] = $request->boolean('is_active');

        $nas->update($validated);

        return redirect()->route('tenant.nas.index')
            ->with('success', 'Router/NAS berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $nas = Nas::findOrFail($id);
        $nas->delete();

        return redirect()->route('tenant.nas.index')
            ->with('success', 'Router/NAS berhasil dihapus.');
    }

    public function test(int $id)
    {
        $nas = Nas::findOrFail($id);
        
        $nas->update(['last_seen' => now()]);

        return back()->with('success', 'Koneksi ke ' . $nas->name . ' berhasil!');
    }
}
