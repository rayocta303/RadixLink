<?php

namespace App\Models\Tenant;

class Invoice extends TenantModel
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'service_plan_id',
        'type',
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
        'created_by',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function servicePlan()
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
