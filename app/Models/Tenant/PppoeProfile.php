<?php

namespace App\Models\Tenant;

class PppoeProfile extends TenantModel
{
    protected $fillable = [
        'name',
        'profile_name',
        'nas_id',
        'ip_pool_id',
        'bandwidth_id',
        'local_address',
        'remote_address',
        'dns_server',
        'wins_server',
        'session_timeout',
        'idle_timeout',
        'only_one',
        'parent_queue',
        'address_list',
        'is_active',
        'description',
        'mikrotik_options',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'only_one' => 'boolean',
        'session_timeout' => 'integer',
        'idle_timeout' => 'integer',
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
        return $this->hasMany(ServicePlan::class, 'pppoe_profile_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'pppoe_profile_id');
    }

    public function pppoeServers()
    {
        return $this->hasMany(PppoeServer::class, 'pppoe_profile_id');
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
        $cmd = "/ppp profile add name=\"{$this->profile_name}\"";
        
        if ($this->ipPool) {
            $cmd .= " remote-address={$this->ipPool->pool_name}";
        } elseif ($this->remote_address) {
            $cmd .= " remote-address={$this->remote_address}";
        }
        
        if ($this->local_address) {
            $cmd .= " local-address={$this->local_address}";
        }
        
        if ($this->bandwidth) {
            $cmd .= " rate-limit={$this->bandwidth->getMikrotikRateLimit()}";
        }
        
        if ($this->dns_server) {
            $cmd .= " dns-server={$this->dns_server}";
        }
        
        if ($this->only_one) {
            $cmd .= " only-one=yes";
        }
        
        if ($this->session_timeout) {
            $cmd .= " session-timeout={$this->session_timeout}s";
        }
        
        if ($this->idle_timeout) {
            $cmd .= " idle-timeout={$this->idle_timeout}s";
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
