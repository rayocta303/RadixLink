<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'phone',
        'address',
        'coordinates',
        'service_plan_id',
        'service_type',
        'status',
        'registered_at',
        'expires_at',
        'suspended_at',
        'suspend_reason',
        'mac_address',
        'balance',
        'auto_renew',
        'pppoe_password',
        'static_ip',
        'meta',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'pppoe_password',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'suspended_at' => 'datetime',
        'balance' => 'decimal:2',
        'auto_renew' => 'boolean',
        'meta' => 'array',
        'password' => 'hashed',
    ];

    public function servicePlan()
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'used_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
