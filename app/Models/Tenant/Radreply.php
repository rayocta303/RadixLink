<?php

namespace App\Models\Tenant;

class Radreply extends TenantModel
{
    protected $table = 'radreply';
    
    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];

    public static function setBandwidth(string $username, string $downloadRate, string $uploadRate): array
    {
        $mikrotikRate = $uploadRate . '/' . $downloadRate;
        
        return [
            self::updateOrCreate(
                ['username' => $username, 'attribute' => 'Mikrotik-Rate-Limit'],
                ['op' => '=', 'value' => $mikrotikRate]
            ),
        ];
    }

    public static function setFramedIP(string $username, string $ipAddress): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'attribute' => 'Framed-IP-Address'],
            ['op' => '=', 'value' => $ipAddress]
        );
    }

    public static function setFramedPool(string $username, string $poolName): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'attribute' => 'Framed-Pool'],
            ['op' => '=', 'value' => $poolName]
        );
    }

    public static function removeUser(string $username): int
    {
        return self::where('username', $username)->delete();
    }
}
