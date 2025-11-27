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
        'use_ssl',
        'is_active',
        'last_seen',
        'info',
    ];

    protected $casts = [
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
        'info' => 'array',
    ];

    protected $hidden = [
        'api_password',
        'secret',
    ];

    public function isOnline(): bool
    {
        return $this->last_seen && $this->last_seen->diffInMinutes(now()) < 5;
    }
}
