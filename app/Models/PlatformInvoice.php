<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformInvoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
        'items',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }
}
