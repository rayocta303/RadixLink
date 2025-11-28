<?php

namespace App\Models\Tenant;

class BandwidthProfile extends TenantModel
{
    protected $fillable = [
        'name',
        'name_bw',
        'rate_up',
        'rate_down',
        'burst_limit_up',
        'burst_limit_down',
        'burst_threshold_up',
        'burst_threshold_down',
        'burst_time_up',
        'burst_time_down',
        'priority',
        'limit_at_up',
        'limit_at_down',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function servicePlans()
    {
        return $this->hasMany(ServicePlan::class, 'bandwidth_id');
    }

    public function pppoeProfiles()
    {
        return $this->hasMany(PppoeProfile::class, 'bandwidth_id');
    }

    public function hotspotProfiles()
    {
        return $this->hasMany(HotspotProfile::class, 'bandwidth_id');
    }

    public function getRateTextAttribute(): string
    {
        return "{$this->rate_down}/{$this->rate_up}";
    }

    public function getBurstTextAttribute(): ?string
    {
        if ($this->burst_limit_up || $this->burst_limit_down) {
            return ($this->burst_limit_down ?? '-') . '/' . ($this->burst_limit_up ?? '-');
        }
        return null;
    }

    public function hasBurst(): bool
    {
        return !empty($this->burst_limit_up) || !empty($this->burst_limit_down);
    }

    public function getMikrotikRateLimit(): string
    {
        $parts = [];
        
        $parts[] = $this->rate_up . '/' . $this->rate_down;
        
        if ($this->hasBurst()) {
            $parts[] = ($this->burst_limit_up ?? '0') . '/' . ($this->burst_limit_down ?? '0');
            $parts[] = ($this->burst_threshold_up ?? '0') . '/' . ($this->burst_threshold_down ?? '0');
            $parts[] = ($this->burst_time_up ?? '0') . '/' . ($this->burst_time_down ?? '0');
        }
        
        return implode(' ', $parts);
    }

    public function parseRate(string $rate): array
    {
        $value = preg_replace('/[^0-9]/', '', $rate);
        $unit = preg_replace('/[0-9]/', '', $rate);
        
        return [
            'value' => (int) $value,
            'unit' => strtoupper($unit) ?: 'K',
        ];
    }

    public function getRateInKbps(string $rate): int
    {
        $parsed = $this->parseRate($rate);
        $multipliers = [
            'K' => 1,
            'M' => 1024,
            'G' => 1048576,
        ];
        
        return $parsed['value'] * ($multipliers[$parsed['unit']] ?? 1);
    }
}
