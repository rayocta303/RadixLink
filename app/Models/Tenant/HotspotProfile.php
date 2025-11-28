<?php

namespace App\Models\Tenant;

class HotspotProfile extends TenantModel
{
    protected $fillable = [
        'name',
        'profile_name',
        'nas_id',
        'ip_pool_id',
        'bandwidth_id',
        'shared_users',
        'session_timeout',
        'idle_timeout',
        'keepalive_timeout',
        'status_autorefresh',
        'transparent_proxy',
        'mac_cookie_timeout',
        'parent_queue',
        'address_list',
        'incoming_filter',
        'outgoing_filter',
        'is_active',
        'description',
        'mikrotik_options',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'transparent_proxy' => 'boolean',
        'shared_users' => 'integer',
        'session_timeout' => 'integer',
        'idle_timeout' => 'integer',
        'keepalive_timeout' => 'integer',
        'mikrotik_options' => 'array',
    ];

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function ipPool()
    {
        return $this->belongsTo(IpPool::class, 'ip_pool_id');
    }

    public function bandwidth()
    {
        return $this->belongsTo(BandwidthProfile::class, 'bandwidth_id');
    }

    public function servicePlans()
    {
        return $this->hasMany(ServicePlan::class, 'hotspot_profile_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'hotspot_profile_id');
    }

    public function hotspotServers()
    {
        return $this->hasMany(HotspotServer::class, 'hotspot_profile_id');
    }

    public function getSessionTimeoutTextAttribute(): ?string
    {
        if (!$this->session_timeout) {
            return null;
        }
        
        if ($this->session_timeout >= 86400) {
            $days = floor($this->session_timeout / 86400);
            return "{$days} hari";
        }
        if ($this->session_timeout >= 3600) {
            $hours = floor($this->session_timeout / 3600);
            return "{$hours} jam";
        }
        if ($this->session_timeout >= 60) {
            $minutes = floor($this->session_timeout / 60);
            return "{$minutes} menit";
        }
        
        return "{$this->session_timeout} detik";
    }

    public function getActiveCustomersCount(): int
    {
        return $this->customers()->where('status', 'active')->count();
    }

    public function toMikrotikCommand(): string
    {
        $cmd = "/ip hotspot user profile add name=\"{$this->profile_name}\"";
        
        if ($this->ipPool) {
            $cmd .= " address-pool={$this->ipPool->pool_name}";
        }
        
        if ($this->bandwidth) {
            $cmd .= " rate-limit={$this->bandwidth->getMikrotikRateLimit()}";
        }
        
        if ($this->shared_users) {
            $cmd .= " shared-users={$this->shared_users}";
        }
        
        if ($this->session_timeout) {
            $cmd .= " session-timeout={$this->session_timeout}s";
        }
        
        if ($this->idle_timeout) {
            $cmd .= " idle-timeout={$this->idle_timeout}s";
        }
        
        if ($this->keepalive_timeout) {
            $cmd .= " keepalive-timeout={$this->keepalive_timeout}s";
        }
        
        if ($this->status_autorefresh) {
            $cmd .= " status-autorefresh={$this->status_autorefresh}";
        }
        
        if ($this->transparent_proxy) {
            $cmd .= " transparent-proxy=yes";
        }
        
        if ($this->parent_queue) {
            $cmd .= " parent-queue=\"{$this->parent_queue}\"";
        }
        
        if ($this->address_list) {
            $cmd .= " address-list=\"{$this->address_list}\"";
        }
        
        return $cmd;
    }
}
