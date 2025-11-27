<?php

namespace App\Models\Tenant;

class Radpostauth extends TenantModel
{
    protected $table = 'radpostauth';
    
    protected $fillable = [
        'username',
        'pass',
        'reply',
        'authdate',
        'class',
    ];

    protected $casts = [
        'authdate' => 'datetime',
    ];

    public function scopeSuccessful($query)
    {
        return $query->where('reply', 'Access-Accept');
    }

    public function scopeRejected($query)
    {
        return $query->where('reply', 'Access-Reject');
    }

    public function scopeByUsername($query, string $username)
    {
        return $query->where('username', $username);
    }

    public function isSuccessful(): bool
    {
        return $this->reply === 'Access-Accept';
    }
}
