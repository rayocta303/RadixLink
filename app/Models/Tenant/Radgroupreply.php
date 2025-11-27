<?php

namespace App\Models\Tenant;

class Radgroupreply extends TenantModel
{
    protected $table = 'radgroupreply';
    
    protected $fillable = [
        'groupname',
        'attribute',
        'op',
        'value',
    ];

    public static function setGroupBandwidth(string $groupname, string $downloadRate, string $uploadRate): self
    {
        $mikrotikRate = $uploadRate . '/' . $downloadRate;
        
        return self::updateOrCreate(
            ['groupname' => $groupname, 'attribute' => 'Mikrotik-Rate-Limit'],
            ['op' => '=', 'value' => $mikrotikRate]
        );
    }
}
