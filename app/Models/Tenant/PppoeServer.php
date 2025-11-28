<?php

namespace App\Models\Tenant;

class PppoeServer extends TenantModel
{
    protected $fillable = [
        'name',
        'nas_id',
        'service_name',
        'interface',
        'max_mtu',
        'max_mru',
        'max_sessions',
        'pppoe_profile_id',
        'authentication',
        'keepalive',
        'one_session_per_host',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'keepalive' => 'boolean',
        'one_session_per_host' => 'boolean',
        'max_mtu' => 'integer',
        'max_mru' => 'integer',
        'max_sessions' => 'integer',
    ];

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function pppoeProfile()
    {
        return $this->belongsTo(PppoeProfile::class, 'pppoe_profile_id');
    }

    public function getAuthMethodsAttribute(): array
    {
        return explode(',', $this->authentication ?? 'pap,chap,mschap1,mschap2');
    }

    public function toMikrotikCommand(): string
    {
        $cmd = "/interface pppoe-server server add service-name=\"{$this->service_name}\" interface={$this->interface}";
        
        if ($this->pppoeProfile) {
            $cmd .= " default-profile={$this->pppoeProfile->profile_name}";
        }
        
        if ($this->max_mtu) {
            $cmd .= " max-mtu={$this->max_mtu}";
        }
        
        if ($this->max_mru) {
            $cmd .= " max-mru={$this->max_mru}";
        }
        
        if ($this->max_sessions > 0) {
            $cmd .= " max-sessions={$this->max_sessions}";
        }
        
        if ($this->authentication) {
            $cmd .= " authentication={$this->authentication}";
        }
        
        if (!$this->keepalive) {
            $cmd .= " keepalive-timeout=disabled";
        }
        
        if ($this->one_session_per_host) {
            $cmd .= " one-session-per-host=yes";
        }
        
        if (!$this->is_active) {
            $cmd .= " disabled=yes";
        }
        
        return $cmd;
    }
}
