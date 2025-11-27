<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TenantRole extends Model
{
    protected $connection = 'tenant';
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            TenantPermission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            TenantUser::class,
            'model_has_roles',
            'role_id',
            'model_id'
        )->where('model_type', TenantUser::class);
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermissionTo(string|array $permissions): self
    {
        $permissionNames = is_array($permissions) ? $permissions : [$permissions];
        
        foreach ($permissionNames as $permissionName) {
            $permission = TenantPermission::where('name', $permissionName)->first();
            if ($permission && !$this->hasPermissionTo($permissionName)) {
                $this->permissions()->attach($permission->id);
            }
        }
        
        return $this;
    }

    public function revokePermissionTo(string $permissionName): self
    {
        $permission = TenantPermission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
        
        return $this;
    }

    public function syncPermissions(array $permissionNames): self
    {
        $permissionIds = TenantPermission::whereIn('name', $permissionNames)->pluck('id');
        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
