<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'phone',
        'address',
        'balance',
        'commission_rate',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(ResellerTransaction::class);
    }

    public function addBalance(float $amount, string $type = 'topup', string $description = null): void
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);

        $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);
    }

    public function deductBalance(float $amount, string $type = 'sale', string $description = null): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);

        $this->transactions()->create([
            'type' => $type,
            'amount' => -$amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);

        return true;
    }
}
