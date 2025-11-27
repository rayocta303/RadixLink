<?php

namespace App\Models\Tenant;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TenantUser extends Authenticatable
{
    protected $connection = 'tenant';
    protected $table = 'users';
    
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            TenantRole::class,
            'model_has_roles',
            'model_id',
            'role_id'
        )->where('model_type', self::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            TenantPermission::class,
            'model_has_permissions',
            'model_id',
            'permission_id'
        )->where('model_type', self::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->roles()->where('name', $roles)->exists();
        }
        
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }
        
        $roleIds = $this->roles()->pluck('id');
        return DB::connection('tenant')
            ->table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_has_permissions.role_id', $roleIds)
            ->where('permissions.name', $permission)
            ->exists();
    }

    public function assignRole(string|array $roles): self
    {
        $roleNames = is_array($roles) ? $roles : [$roles];
        
        foreach ($roleNames as $roleName) {
            $role = TenantRole::where('name', $roleName)->first();
            if ($role && !$this->hasRole($roleName)) {
                DB::connection('tenant')->table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_type' => self::class,
                    'model_id' => $this->id,
                ]);
            }
        }
        
        return $this;
    }

    public function removeRole(string $roleName): self
    {
        $role = TenantRole::where('name', $roleName)->first();
        if ($role) {
            DB::connection('tenant')->table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', self::class)
                ->where('model_id', $this->id)
                ->delete();
        }
        
        return $this;
    }

    public function syncRoles(array $roleNames): self
    {
        DB::connection('tenant')->table('model_has_roles')
            ->where('model_type', self::class)
            ->where('model_id', $this->id)
            ->delete();
            
        return $this->assignRole($roleNames);
    }

    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    public function reseller()
    {
        return $this->hasOne(Reseller::class, 'user_id');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTechnician(): bool
    {
        return $this->hasRole('technician');
    }

    public function isCashier(): bool
    {
        return $this->hasRole('cashier');
    }

    public function isReseller(): bool
    {
        return $this->hasRole('reseller');
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function isInvestor(): bool
    {
        return $this->hasRole('investor');
    }

    public function hasTenantRole(string|array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasAnyTenantRole(array $roles): bool
    {
        return $this->hasAnyRole($roles);
    }

    public function canManageCustomers(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'reseller']);
    }

    public function canManageVouchers(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'cashier', 'reseller']);
    }

    public function canManageNas(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'technician']);
    }

    public function canManageServices(): bool
    {
        return $this->hasAnyRole(['owner', 'admin']);
    }

    public function canManageInvoices(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    public function canViewReports(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'cashier', 'investor']);
    }

    public function canViewFinancialReports(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'investor']);
    }

    public function canManageUsers(): bool
    {
        return $this->hasAnyRole(['owner', 'admin']);
    }

    public function canManageSettings(): bool
    {
        return $this->hasAnyRole(['owner', 'admin']);
    }

    public function canResetCustomerAccounts(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'technician', 'support']);
    }

    public function canManageBalance(): bool
    {
        return $this->hasAnyRole(['owner', 'admin', 'cashier', 'reseller']);
    }

    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}
