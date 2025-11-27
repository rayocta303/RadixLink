<?php

namespace App\Models\Tenant;

class ResellerTransaction extends TenantModel
{
    protected $fillable = [
        'reseller_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta' => 'array',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
