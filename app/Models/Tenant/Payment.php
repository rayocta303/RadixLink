<?php

namespace App\Models\Tenant;

class Payment extends TenantModel
{
    protected $fillable = [
        'payment_id',
        'invoice_id',
        'customer_id',
        'amount',
        'payment_method',
        'payment_channel',
        'status',
        'external_id',
        'payment_url',
        'payment_data',
        'paid_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->payment_id = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' || $this->status === 'processing';
    }
}
