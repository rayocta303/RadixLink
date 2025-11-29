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
        'bandwidth_id',
        'ip_pool_id',
        'pppoe_profile_id',
        'hotspot_profile_id',
        'router_name',
        'pool',
        'prepaid',
        'enabled',
        'expired_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_fup' => 'boolean',
        'can_share' => 'boolean',
        'is_active' => 'boolean',
        'radius_attributes' => 'array',
        'prepaid' => 'boolean',
        'enabled' => 'boolean',
        'expired_date' => 'date',
        'bandwidth_id' => 'integer',
        'ip_pool_id' => 'integer',
        'pppoe_profile_id' => 'integer',
        'hotspot_profile_id' => 'integer',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function bandwidth()
    {
        return $this->belongsTo(BandwidthProfile::class, 'bandwidth_id');
    }

    public function ipPool()
    {
        return $this->belongsTo(IpPool::class, 'ip_pool_id');
    }

    public function pppoeProfile()
    {
        return $this->belongsTo(PppoeProfile::class, 'pppoe_profile_id');
    }

    public function hotspotProfile()
    {
        return $this->belongsTo(HotspotProfile::class, 'hotspot_profile_id');
    }

    public function router()
    {
        return $this->belongsTo(Nas::class, 'router_name', 'shortname');
    }

    public function getValidityTextAttribute(): string
    {
        $unitLabels = [
            'minutes' => 'menit',
            'hours' => 'jam',
            'days' => 'hari',
            'months' => 'bulan',
            'Mins' => 'menit',
            'Hrs' => 'jam',
            'Days' => 'hari',
            'Months' => 'bulan',
            'Period' => 'periode',
        ];
        $unit = $unitLabels[$this->validity_unit ?? 'days'] ?? 'hari';
        return $this->validity . ' ' . $unit;
    }

    public function getBandwidthTextAttribute(): string
    {
        if ($this->bandwidth) {
            return $this->bandwidth->rate_text;
        }
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
            'Mins' => 60,
            'Hrs' => 3600,
            'Days' => 86400,
            'Months' => 2592000,
            'Period' => 86400,
        ];
        return $this->validity * ($multipliers[$this->validity_unit ?? 'days'] ?? 86400);
    }

    public function isPppoe(): bool
    {
        return strtolower($this->type) === 'pppoe';
    }

    public function isHotspot(): bool
    {
        return strtolower($this->type) === 'hotspot';
    }

    public function isPrepaid(): bool
    {
        return (bool) $this->prepaid;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }

    public function generateMikrotikScript(): string
    {
        if ($this->isPppoe()) {
            return $this->generatePppoeScript();
        }
        return $this->generateHotspotScript();
    }

    protected function generatePppoeScript(): string
    {
        $lines = [];
        $profileName = $this->code ?? $this->name;
        
        $lines[] = "# PPPoE Profile Script for: {$this->name}";
        $lines[] = "/ppp profile";
        
        $cmd = "add name=\"{$profileName}\"";
        
        if ($this->pppoeProfile && $this->pppoeProfile->ipPool) {
            $cmd .= " remote-address={$this->pppoeProfile->ipPool->pool_name}";
        } elseif ($this->ipPool) {
            $cmd .= " remote-address={$this->ipPool->pool_name}";
        } elseif ($this->pool) {
            $cmd .= " remote-address={$this->pool}";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->local_address) {
            $cmd .= " local-address={$this->pppoeProfile->local_address}";
        }
        
        $rateLimit = $this->getMikrotikRateLimit();
        if ($rateLimit) {
            $cmd .= " rate-limit={$rateLimit}";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->dns_server) {
            $cmd .= " dns-server={$this->pppoeProfile->dns_server}";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->only_one) {
            $cmd .= " only-one=yes";
        }
        
        if ($this->validity && $this->validity_unit) {
            $timeout = $this->validity_in_seconds;
            $cmd .= " session-timeout={$timeout}s";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->idle_timeout) {
            $cmd .= " idle-timeout={$this->pppoeProfile->idle_timeout}s";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->parent_queue) {
            $cmd .= " parent-queue=\"{$this->pppoeProfile->parent_queue}\"";
        }
        
        if ($this->pppoeProfile && $this->pppoeProfile->address_list) {
            $cmd .= " address-list=\"{$this->pppoeProfile->address_list}\"";
        }
        
        $lines[] = $cmd;
        
        return implode("\n", $lines);
    }

    protected function generateHotspotScript(): string
    {
        $lines = [];
        $profileName = $this->code ?? $this->name;
        
        $lines[] = "# Hotspot User Profile Script for: {$this->name}";
        $lines[] = "/ip hotspot user profile";
        
        $cmd = "add name=\"{$profileName}\"";
        
        if ($this->hotspotProfile && $this->hotspotProfile->ipPool) {
            $cmd .= " address-pool={$this->hotspotProfile->ipPool->pool_name}";
        } elseif ($this->ipPool) {
            $cmd .= " address-pool={$this->ipPool->pool_name}";
        } elseif ($this->pool) {
            $cmd .= " address-pool={$this->pool}";
        }
        
        $rateLimit = $this->getMikrotikRateLimit();
        if ($rateLimit) {
            $cmd .= " rate-limit={$rateLimit}";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->shared_users) {
            $cmd .= " shared-users={$this->hotspotProfile->shared_users}";
        } elseif ($this->simultaneous_use) {
            $cmd .= " shared-users={$this->simultaneous_use}";
        }
        
        if ($this->validity && $this->validity_unit) {
            $timeout = $this->validity_in_seconds;
            $cmd .= " session-timeout={$timeout}s";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->idle_timeout) {
            $cmd .= " idle-timeout={$this->hotspotProfile->idle_timeout}s";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->keepalive_timeout) {
            $cmd .= " keepalive-timeout={$this->hotspotProfile->keepalive_timeout}s";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->status_autorefresh) {
            $cmd .= " status-autorefresh={$this->hotspotProfile->status_autorefresh}";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->transparent_proxy) {
            $cmd .= " transparent-proxy=yes";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->parent_queue) {
            $cmd .= " parent-queue=\"{$this->hotspotProfile->parent_queue}\"";
        }
        
        if ($this->hotspotProfile && $this->hotspotProfile->address_list) {
            $cmd .= " address-list=\"{$this->hotspotProfile->address_list}\"";
        }
        
        $lines[] = $cmd;
        
        return implode("\n", $lines);
    }

    public function getMikrotikRateLimit(): ?string
    {
        if ($this->bandwidth) {
            return $this->bandwidth->getMikrotikRateLimit();
        }
        
        if ($this->isPppoe() && $this->pppoeProfile && $this->pppoeProfile->bandwidth) {
            return $this->pppoeProfile->bandwidth->getMikrotikRateLimit();
        }
        
        if ($this->isHotspot() && $this->hotspotProfile && $this->hotspotProfile->bandwidth) {
            return $this->hotspotProfile->bandwidth->getMikrotikRateLimit();
        }
        
        if ($this->bandwidth_up && $this->bandwidth_down) {
            return "{$this->bandwidth_up}/{$this->bandwidth_down}";
        }
        
        return null;
    }

    public function generateUserScript(string $username, string $password): string
    {
        if ($this->isPppoe()) {
            return $this->generatePppoeUserScript($username, $password);
        }
        return $this->generateHotspotUserScript($username, $password);
    }

    protected function generatePppoeUserScript(string $username, string $password): string
    {
        $lines = [];
        $profileName = $this->code ?? $this->name;
        
        $lines[] = "# PPPoE Secret for user: {$username}";
        $lines[] = "/ppp secret";
        
        $cmd = "add name=\"{$username}\" password=\"{$password}\" profile=\"{$profileName}\" service=pppoe";
        
        $lines[] = $cmd;
        
        return implode("\n", $lines);
    }

    protected function generateHotspotUserScript(string $username, string $password): string
    {
        $lines = [];
        $profileName = $this->code ?? $this->name;
        
        $lines[] = "# Hotspot User for: {$username}";
        $lines[] = "/ip hotspot user";
        
        $cmd = "add name=\"{$username}\" password=\"{$password}\" profile=\"{$profileName}\"";
        
        if ($this->validity && $this->validity_unit) {
            $limitUptime = $this->formatMikrotikTime($this->validity_in_seconds);
            $cmd .= " limit-uptime={$limitUptime}";
        }
        
        $lines[] = $cmd;
        
        return implode("\n", $lines);
    }

    protected function formatMikrotikTime(int $seconds): string
    {
        if ($seconds >= 86400) {
            $days = floor($seconds / 86400);
            return "{$days}d";
        }
        if ($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            return "{$hours}h";
        }
        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            return "{$minutes}m";
        }
        return "{$seconds}s";
    }

    public function getRouterAttribute(): ?Nas
    {
        if ($this->router_name) {
            return Nas::where('shortname', $this->router_name)
                ->orWhere('name', $this->router_name)
                ->first();
        }
        return null;
    }
}
