<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'logo',
        'subdomain',
        'custom_domain',
        'subscription_plan',
        'subscription_expires_at',
        'is_active',
        'is_suspended',
        'suspend_reason',
        'max_routers',
        'max_users',
        'max_vouchers',
        'max_online_users',
        'settings',
        'data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
        'data' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'company_name',
            'address',
            'logo',
            'subdomain',
            'custom_domain',
            'subscription_plan',
            'subscription_expires_at',
            'is_active',
            'is_suspended',
            'suspend_reason',
            'max_routers',
            'max_users',
            'max_vouchers',
            'max_online_users',
            'settings',
        ];
    }

    public function subscription()
    {
        return $this->hasOne(TenantSubscription::class, 'tenant_id', 'id')->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class, 'tenant_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(PlatformTicket::class, 'tenant_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(PlatformInvoice::class, 'tenant_id', 'id');
    }

    public function isExpired(): bool
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active && !$this->is_suspended && !$this->isExpired();
    }
}
