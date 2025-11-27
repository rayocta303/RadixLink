<?php

namespace App\Models\Tenant;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class TenantUser extends Authenticatable
{
    protected $connection = 'tenant';
    protected $table = 'users';
    
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
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
