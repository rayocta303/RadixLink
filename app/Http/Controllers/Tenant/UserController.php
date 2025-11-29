<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantUser;
use App\Models\Tenant\TenantRole;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected function checkConnection()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return false;
        }
        return true;
    }

    protected function connectionErrorRedirect()
    {
        return redirect()->route('tenant.users.index')
            ->with('error', 'Database tenant belum dikonfigurasi.');
    }

    public function index()
    {
        if (!$this->checkConnection()) {
            return view('tenant.users.index', [
                'users' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi. Silakan hubungi administrator.',
            ]);
        }
        
        $users = TenantUser::with('roles')->get();
        return view('tenant.users.index', compact('users'));
    }

    public function create()
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $roles = TenantRole::all();
        return view('tenant.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|string',
        ]);

        $user = TenantUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('tenant.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $user = TenantUser::with('roles')->findOrFail($id);
        return view('tenant.users.show', compact('user'));
    }

    public function edit(int $id)
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $user = TenantUser::with('roles')->findOrFail($id);
        $roles = TenantRole::all();
        return view('tenant.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $user = TenantUser::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->is_active = $validated['is_active'] ?? true;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('tenant.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (!$this->checkConnection()) {
            return $this->connectionErrorRedirect();
        }
        
        $user = TenantUser::findOrFail($id);
        $tenantUser = session('tenant_user');
        
        if ($user->id === $tenantUser?->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('owner')) {
            return back()->with('error', 'Tidak dapat menghapus user dengan role Owner.');
        }

        $user->delete();
        return redirect()->route('tenant.users.index')->with('success', 'User berhasil dihapus.');
    }
}
