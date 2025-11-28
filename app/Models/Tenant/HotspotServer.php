<?php

namespace App\Models\Tenant;

class HotspotServer extends TenantModel
{
    protected $fillable = [
        'name',
        'nas_id',
        'interface',
        'address_pool',
        'ip_pool_id',
        'hotspot_profile_id',
        'login_by',
        'http_cookie_lifetime',
        'split_user_domain',
        'https',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'https' => 'boolean',
    ];

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function ipPool()
    {
        return $this->belongsTo(IpPool::class, 'ip_pool_id');
    }

    public function hotspotProfile()
    {
        return $this->belongsTo(HotspotProfile::class, 'hotspot_profile_id');
    }

    public function getLoginMethodsAttribute(): array
    {
        return explode(',', $this->login_by ?? 'cookie,http-chap');
    }

    public function toMikrotikCommand(): string
    {
        $cmd = "/ip hotspot add name=\"{$this->name}\" interface={$this->interface}";
        
        if ($this->ipPool) {
            $cmd .= " address-pool={$this->ipPool->pool_name}";
        } elseif ($this->address_pool) {
            $cmd .= " address-pool={$this->address_pool}";
        }
        
        if ($this->hotspotProfile) {
            $cmd .= " profile={$this->hotspotProfile->profile_name}";
        }
        
        if ($this->login_by) {
            $cmd .= " login-by={$this->login_by}";
        }
        
        if ($this->http_cookie_lifetime) {
            $cmd .= " http-cookie-lifetime={$this->http_cookie_lifetime}";
        }
        
        if ($this->https) {
            $cmd .= " https=yes";
        }
        
        if (!$this->is_active) {
            $cmd .= " disabled=yes";
        }
        
        return $cmd;
    }
}
