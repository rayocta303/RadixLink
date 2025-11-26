<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'username',
        'password',
        'service_plan_id',
        'status',
        'type',
        'max_usage',
        'used_count',
        'price',
        'batch_id',
        'generated_at',
        'activated_at',
        'expires_at',
        'first_used_at',
        'used_by',
        'used_mac',
        'generated_by',
        'sold_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'generated_at' => 'datetime',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'first_used_at' => 'datetime',
    ];

    public function servicePlan()
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'used_by');
    }

    public function batch()
    {
        return $this->belongsTo(VoucherBatch::class, 'batch_id', 'batch_id');
    }

    public function isUsable(): bool
    {
        return $this->status === 'unused' && 
               ($this->expires_at === null || $this->expires_at->isFuture()) &&
               $this->used_count < $this->max_usage;
    }

    public function markAsUsed(Customer $customer, string $mac = null): void
    {
        $this->update([
            'status' => $this->type === 'single' ? 'used' : ($this->used_count + 1 >= $this->max_usage ? 'used' : 'unused'),
            'used_count' => $this->used_count + 1,
            'used_by' => $customer->id,
            'used_mac' => $mac,
            'first_used_at' => $this->first_used_at ?? now(),
            'activated_at' => $this->activated_at ?? now(),
        ]);
    }

    public static function generateCode(int $length = 8, string $type = 'alphanumeric', string $prefix = ''): string
    {
        $characters = match($type) {
            'numeric' => '0123456789',
            'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            default => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        };

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $prefix . $code;
    }
}
