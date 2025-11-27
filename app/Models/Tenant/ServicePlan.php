<?php

namespace App\Models\Tenant;

class ServicePlan extends TenantModel
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
        $unitLabels = [
            'minutes' => 'menit',
            'hours' => 'jam',
            'days' => 'hari',
            'months' => 'bulan',
        ];
        $unit = $unitLabels[$this->validity_unit ?? 'days'] ?? 'hari';
        return $this->validity . ' ' . $unit;
    }

    public function getBandwidthTextAttribute(): string
    {
        return ($this->bandwidth_down ?? '-') . '/' . ($this->bandwidth_up ?? '-');
    }

    public function getQuotaGbAttribute(): ?float
    {
        if (!$this->quota_bytes) {
            return null;
        }
        return round($this->quota_bytes / 1073741824, 2);
    }

    public function getQuotaTextAttribute(): string
    {
        if (!$this->quota_bytes) {
            return 'Unlimited';
        }
        $gb = $this->quota_gb;
        if ($gb >= 1) {
            return $gb . ' GB';
        }
        $mb = round($this->quota_bytes / 1048576, 2);
        return $mb . ' MB';
    }

    public function getValidityInSecondsAttribute(): int
    {
        $multipliers = [
            'minutes' => 60,
            'hours' => 3600,
            'days' => 86400,
            'months' => 2592000,
        ];
        return $this->validity * ($multipliers[$this->validity_unit ?? 'days'] ?? 86400);
    }
}
