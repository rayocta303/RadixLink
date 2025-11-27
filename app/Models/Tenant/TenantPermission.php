<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TenantPermission extends Model
{
    protected $connection = 'tenant';
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            TenantRole::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            TenantUser::class,
            'model_has_permissions',
            'permission_id',
            'model_id'
        )->where('model_type', TenantUser::class);
    }

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    public static function findOrCreate(string $name, string $guardName = 'tenant'): self
    {
        $permission = static::where('name', $name)->where('guard_name', $guardName)->first();
        
        if (!$permission) {
            $permission = static::create([
                'name' => $name,
                'guard_name' => $guardName,
            ]);
        }
        
        return $permission;
    }
}
