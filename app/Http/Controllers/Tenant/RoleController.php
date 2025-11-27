<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantRole;
use App\Models\Tenant\TenantPermission;
use App\Models\Tenant\TenantUser;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected array $systemRoles = [
        'owner',
        'admin',
    ];

    protected array $permissionCategories = [
        'customers' => [
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'suspend_customers',
        ],
        'vouchers' => [
            'view_vouchers',
            'create_vouchers',
            'edit_vouchers',
            'delete_vouchers',
            'generate_vouchers',
        ],
        'invoices' => [
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'pay_invoices',
        ],
        'nas' => [
            'view_nas',
            'create_nas',
            'edit_nas',
            'delete_nas',
        ],
        'services' => [
            'view_services',
            'create_services',
            'edit_services',
            'delete_services',
        ],
        'reports' => [
            'view_reports',
            'view_financial_reports',
        ],
        'settings' => [
            'view_settings',
            'edit_settings',
        ],
        'users' => [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ],
    ];

    public function index()
    {
        $roles = TenantRole::where('guard_name', 'tenant')
            ->withCount('users')
            ->with('permissions')
            ->get();

        return view('tenant.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = TenantPermission::where('guard_name', 'tenant')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);

        return view('tenant.roles.create', compact('permissions', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tenant.roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:tenant.permissions,id',
        ]);

        $roleName = strtolower(str_replace(' ', '_', $validated['name']));

        $role = TenantRole::create([
            'name' => $roleName,
            'guard_name' => 'tenant',
            'display_name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = TenantPermission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('tenant.roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function show($id)
    {
        $role = TenantRole::where('guard_name', 'tenant')->findOrFail($id);

        $role->load('permissions');
        
        $users = TenantUser::whereHas('roles', function ($query) use ($role) {
            $query->where('roles.id', $role->id);
        })->paginate(10);

        $permissionGroups = $this->groupPermissions($role->permissions);

        return view('tenant.roles.show', compact('role', 'users', 'permissionGroups'));
    }

    public function edit($id)
    {
        $role = TenantRole::where('guard_name', 'tenant')->findOrFail($id);

        $permissions = TenantPermission::where('guard_name', 'tenant')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('tenant.roles.edit', compact('role', 'permissions', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = TenantRole::where('guard_name', 'tenant')->findOrFail($id);

        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:tenant.permissions,id',
        ]);

        $role->update([
            'description' => $validated['description'] ?? $role->description,
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = TenantPermission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('tenant.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = TenantRole::where('guard_name', 'tenant')->findOrFail($id);

        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', 'Role ' . ucfirst($role->name) . ' adalah role sistem dan tidak dapat dihapus.');
        }

        $usersCount = TenantUser::whereHas('roles', function ($query) use ($role) {
            $query->where('roles.id', $role->id);
        })->count();

        if ($usersCount > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih memiliki ' . $usersCount . ' pengguna.');
        }

        $role->delete();

        return redirect()->route('tenant.roles.index')->with('success', 'Role berhasil dihapus.');
    }

    protected function groupPermissions($permissions): array
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $permissionName = $permission->name;
            $group = 'other';
            
            foreach ($this->permissionCategories as $category => $categoryPermissions) {
                if (in_array($permissionName, $categoryPermissions)) {
                    $group = $category;
                    break;
                }
            }
            
            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }
            
            $groups[$group][] = $permission;
        }

        ksort($groups);
        
        return $groups;
    }
}
