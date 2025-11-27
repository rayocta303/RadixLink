<?php

namespace App\Models\Tenant;

class VoucherBatch extends TenantModel
{
    protected $fillable = [
        'batch_id',
        'service_plan_id',
        'quantity',
        'used_count',
        'prefix',
        'code_length',
        'code_type',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            $batch->batch_id = 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function servicePlan()
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'batch_id', 'batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->quantity === 0) return 0;
        return round(($this->used_count / $this->quantity) * 100, 2);
    }
}
