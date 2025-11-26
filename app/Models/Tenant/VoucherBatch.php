<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class VoucherBatch extends Model
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

    public function getUsagePercentageAttribute(): float
    {
        if ($this->quantity === 0) return 0;
        return round(($this->used_count / $this->quantity) * 100, 2);
    }
}
