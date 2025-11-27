<?php

namespace App\Models\Tenant;

class Radcheck extends TenantModel
{
    protected $table = 'radcheck';
    
    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];

    public static function setPassword(string $username, string $password, string $authType = 'Cleartext-Password'): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'attribute' => $authType],
            ['op' => ':=', 'value' => $password]
        );
    }

    public static function setExpiration(string $username, string $expirationDate): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'attribute' => 'Expiration'],
            ['op' => ':=', 'value' => $expirationDate]
        );
    }

    public static function setSimultaneousUse(string $username, int $limit): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'attribute' => 'Simultaneous-Use'],
            ['op' => ':=', 'value' => (string) $limit]
        );
    }

    public static function removeUser(string $username): int
    {
        return self::where('username', $username)->delete();
    }
}
