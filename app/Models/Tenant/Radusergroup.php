<?php

namespace App\Models\Tenant;

class Radusergroup extends TenantModel
{
    protected $table = 'radusergroup';
    
    protected $fillable = [
        'username',
        'groupname',
        'priority',
    ];

    public static function assignGroup(string $username, string $groupname, int $priority = 1): self
    {
        return self::updateOrCreate(
            ['username' => $username, 'groupname' => $groupname],
            ['priority' => $priority]
        );
    }

    public static function removeFromGroup(string $username, string $groupname): int
    {
        return self::where('username', $username)
            ->where('groupname', $groupname)
            ->delete();
    }

    public static function removeUser(string $username): int
    {
        return self::where('username', $username)->delete();
    }
}
