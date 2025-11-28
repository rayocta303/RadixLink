<?php

namespace App\Models\Tenant;

class IpPool extends TenantModel
{
    protected $fillable = [
        'name',
        'pool_name',
        'range_start',
        'range_end',
        'next_pool',
        'nas_id',
        'type',
        'is_active',
        'total_ips',
        'used_ips',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_ips' => 'integer',
        'used_ips' => 'integer',
    ];

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function pppoeProfiles()
    {
        return $this->hasMany(PppoeProfile::class, 'ip_pool_id');
    }

    public function hotspotProfiles()
    {
        return $this->hasMany(HotspotProfile::class, 'ip_pool_id');
    }

    public function servicePlans()
    {
        return $this->hasMany(ServicePlan::class, 'ip_pool_id');
    }

    public function getAvailableIpsAttribute(): int
    {
        return max(0, $this->total_ips - $this->used_ips);
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_ips <= 0) {
            return 0;
        }
        return round(($this->used_ips / $this->total_ips) * 100, 1);
    }

    public function calculateTotalIps(): int
    {
        $start = ip2long($this->range_start);
        $end = ip2long($this->range_end);
        
        if ($start === false || $end === false || $start > $end) {
            return 0;
        }
        
        return $end - $start + 1;
    }

    public function updateTotalIps(): void
    {
        $this->total_ips = $this->calculateTotalIps();
        $this->save();
    }

    public function isAlmostFull(): bool
    {
        return $this->usage_percentage >= 80;
    }

    public function isFull(): bool
    {
        return $this->used_ips >= $this->total_ips;
    }

    public function getRangeTextAttribute(): string
    {
        return "{$this->range_start} - {$this->range_end}";
    }
}
