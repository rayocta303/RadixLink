<?php

namespace App\Models\Tenant;

class Radgroupcheck extends TenantModel
{
    protected $table = 'radgroupcheck';
    
    protected $fillable = [
        'groupname',
        'attribute',
        'op',
        'value',
    ];
}
