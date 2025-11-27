<?php

namespace App\Models\Tenant;

class Nas extends TenantModel
{
    protected $table = 'nas';

    protected $fillable = [
        'name',
        'shortname',
        'nasname',
        'ports',
        'secret',
        'server',
        'community',
        'description',
        'type',
        'api_username',
        'api_password',
        'api_port',
        'winbox_port',
        'use_ssl',
        'is_active',
        'last_seen',
        'info',
        'location_name',
        'longitude',
        'latitude',
        'coverage',
        'status',
        'vpn_enabled',
        'vpn_secret',
        'vpn_port',
        'vpn_type',
        'vpn_server',
        'vpn_username',
        'vpn_password',
        'vpn_local_address',
        'vpn_remote_address',
    ];

    protected $casts = [
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'vpn_enabled' => 'boolean',
        'last_seen' => 'datetime',
        'info' => 'array',
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
        'coverage' => 'integer',
        'winbox_port' => 'integer',
        'vpn_port' => 'integer',
    ];

    protected $hidden = [
        'api_password',
        'secret',
        'vpn_password',
        'vpn_secret',
    ];

    public function isOnline(): bool
    {
        return $this->last_seen && $this->last_seen->diffInMinutes(now()) < 5;
    }

    public function hasLocation(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getCoordinatesAttribute(): ?string
    {
        if ($this->hasLocation()) {
            return "{$this->latitude},{$this->longitude}";
        }
        return null;
    }

    public function isVpnEnabled(): bool
    {
        return $this->vpn_enabled && $this->vpn_type;
    }
}
