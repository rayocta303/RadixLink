<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantUser;
use App\Models\Tenant\TenantRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = TenantUser::with('roles')->get();
        return view('tenant.users.index', compact('users'));
    }

    public function create()
    {
        $roles = TenantRole::all();
        return view('tenant.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
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

    public function show(TenantUser $user)
    {
        $user->load('roles');
        return view('tenant.users.show', compact('user'));
    }

    public function edit(TenantUser $user)
    {
        $user->load('roles');
        $roles = TenantRole::all();
        return view('tenant.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, TenantUser $user)
    {
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

    public function destroy(TenantUser $user)
    {
        $tenantUser = session('tenant_user');
        
        if ($user->id === $tenantUser->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('owner')) {
            return back()->with('error', 'Tidak dapat menghapus user dengan role Owner.');
        }

        $user->delete();
        return redirect()->route('tenant.users.index')->with('success', 'User berhasil dihapus.');
    }
}
