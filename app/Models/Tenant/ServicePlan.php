<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ServicePlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'price',
        'validity',
        'validity_unit',
        'bandwidth_up',
        'bandwidth_down',
        'quota_bytes',
        'has_fup',
        'fup_bandwidth_up',
        'fup_bandwidth_down',
        'fup_threshold_bytes',
        'can_share',
        'max_devices',
        'simultaneous_use',
        'is_active',
        'radius_attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_fup' => 'boolean',
        'can_share' => 'boolean',
        'is_active' => 'boolean',
        'radius_attributes' => 'array',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function getValidityTextAttribute(): string
    {
        return $this->validity . ' ' . ucfirst($this->validity_unit);
    }

    public function getBandwidthTextAttribute(): string
    {
        return $this->bandwidth_up . '/' . $this->bandwidth_down;
    }
}
