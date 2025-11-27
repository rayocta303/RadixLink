<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected array $systemRoles = [
        'super_admin',
    ];

    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->withCount(['users' => function ($query) {
                $query->where('user_type', 'platform');
            }])
            ->get();

        return view('platform.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);

        return view('platform.roles.create', compact('permissions', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $roleName = strtolower(str_replace(' ', '_', $validated['name']));
        
        if (!str_starts_with($roleName, 'platform_')) {
            $roleName = 'platform_' . $roleName;
        }

        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('platform.roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function show(Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        $role->load('permissions');
        
        $users = User::where('user_type', 'platform')
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('roles.id', $role->id);
            })
            ->paginate(10);

        $permissionGroups = $this->groupPermissions($role->permissions);

        return view('platform.roles.show', compact('role', 'users', 'permissionGroups'));
    }

    public function edit(Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('platform.roles.edit', compact('role', 'permissions', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('platform.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', 'Role ' . ucfirst(str_replace('_', ' ', $role->name)) . ' adalah role sistem dan tidak dapat dihapus.');
        }

        $usersCount = User::where('user_type', 'platform')
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('roles.id', $role->id);
            })
            ->count();

        if ($usersCount > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih memiliki ' . $usersCount . ' pengguna.');
        }

        $role->delete();

        return redirect()->route('platform.roles.index')->with('success', 'Role berhasil dihapus.');
    }

    protected function groupPermissions($permissions): array
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $group = $parts[0] ?? 'other';
            
            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }
            
            $groups[$group][] = $permission;
        }

        ksort($groups);
        
        return $groups;
    }
}
