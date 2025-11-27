<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEntityAttribute()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $modelClass = $this->entity_type;
        if (class_exists($modelClass)) {
            return $modelClass::find($this->entity_id);
        }

        return null;
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created', 'create' => 'green',
            'updated', 'update' => 'blue',
            'deleted', 'delete' => 'red',
            'login' => 'indigo',
            'logout' => 'gray',
            'suspended', 'suspend' => 'orange',
            'activated', 'activate' => 'emerald',
            default => 'gray',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created', 'create' => 'Dibuat',
            'updated', 'update' => 'Diubah',
            'deleted', 'delete' => 'Dihapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'suspended', 'suspend' => 'Disuspend',
            'activated', 'activate' => 'Diaktifkan',
            default => ucfirst($this->action),
        };
    }

    public function getEntityTypeLabelAttribute(): string
    {
        if (!$this->entity_type) {
            return '-';
        }

        $className = class_basename($this->entity_type);
        
        return match ($className) {
            'Tenant' => 'Tenant',
            'User' => 'User',
            'TenantSubscription' => 'Subscription',
            'PlatformInvoice' => 'Invoice',
            'PlatformTicket' => 'Ticket',
            'SubscriptionPlan' => 'Paket Langganan',
            default => $className,
        };
    }

    public static function getActionTypes(): array
    {
        return [
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'suspended' => 'Disuspend',
            'activated' => 'Diaktifkan',
        ];
    }

    public static function getEntityTypes(): array
    {
        return [
            'App\\Models\\Tenant' => 'Tenant',
            'App\\Models\\User' => 'User',
            'App\\Models\\TenantSubscription' => 'Subscription',
            'App\\Models\\PlatformInvoice' => 'Invoice',
            'App\\Models\\PlatformTicket' => 'Ticket',
            'App\\Models\\SubscriptionPlan' => 'Paket Langganan',
        ];
    }

    public function scopeFilterByUser($query, $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query;
    }

    public function scopeFilterByAction($query, $action)
    {
        if ($action) {
            return $query->where('action', $action);
        }
        return $query;
    }

    public function scopeFilterByEntityType($query, $entityType)
    {
        if ($entityType) {
            return $query->where('entity_type', $entityType);
        }
        return $query;
    }

    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }
}
