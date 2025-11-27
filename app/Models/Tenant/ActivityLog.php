<?php

namespace App\Models\Tenant;

class ActivityLog extends TenantModel
{
    protected $table = 'activity_logs';

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
        return $this->belongsTo(TenantUser::class, 'user_id');
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
            'paid', 'payment' => 'teal',
            'generated', 'generate' => 'purple',
            'synced', 'sync' => 'cyan',
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
            'paid', 'payment' => 'Dibayar',
            'generated', 'generate' => 'Digenerate',
            'synced', 'sync' => 'Disinkronkan',
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
            'Customer' => 'Pelanggan',
            'Voucher' => 'Voucher',
            'Invoice' => 'Invoice',
            'Payment' => 'Pembayaran',
            'NAS', 'Nas' => 'NAS/Router',
            'ServicePlan' => 'Paket Layanan',
            'TenantUser', 'User' => 'Pengguna',
            'TenantRole', 'Role' => 'Role',
            'Settings' => 'Pengaturan',
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
            'paid' => 'Dibayar',
            'generated' => 'Digenerate',
            'synced' => 'Disinkronkan',
        ];
    }

    public static function getEntityTypes(): array
    {
        return [
            'App\\Models\\Tenant\\Customer' => 'Pelanggan',
            'App\\Models\\Tenant\\Voucher' => 'Voucher',
            'App\\Models\\Tenant\\Invoice' => 'Invoice',
            'App\\Models\\Tenant\\Payment' => 'Pembayaran',
            'App\\Models\\Tenant\\Nas' => 'NAS/Router',
            'App\\Models\\Tenant\\ServicePlan' => 'Paket Layanan',
            'App\\Models\\Tenant\\TenantUser' => 'Pengguna',
            'App\\Models\\Tenant\\TenantRole' => 'Role',
            'Settings' => 'Pengaturan',
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
