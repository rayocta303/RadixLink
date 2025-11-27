<?php

namespace App\Traits;

use App\Models\Tenant\ActivityLog;
use App\Services\TenantDatabaseManager;
use Illuminate\Database\Eloquent\Model;

trait TenantLogsActivity
{
    protected function logTenantActivity(
        string $action,
        string $description,
        ?Model $entity = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?ActivityLog {
        if (!TenantDatabaseManager::isConnected()) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function logTenantCreated(Model $entity, string $description): ?ActivityLog
    {
        return $this->logTenantActivity(
            'created',
            $description,
            $entity,
            null,
            $entity->toArray()
        );
    }

    protected function logTenantUpdated(Model $entity, string $description, array $oldValues = []): ?ActivityLog
    {
        $changedAttributes = [];
        foreach ($entity->getDirty() as $key => $value) {
            $changedAttributes[$key] = $value;
        }

        return $this->logTenantActivity(
            'updated',
            $description,
            $entity,
            $oldValues ?: $entity->getOriginal(),
            $changedAttributes ?: $entity->toArray()
        );
    }

    protected function logTenantDeleted(Model $entity, string $description): ?ActivityLog
    {
        return $this->logTenantActivity(
            'deleted',
            $description,
            $entity,
            $entity->toArray(),
            null
        );
    }

    protected function logTenantLogin(string $description = 'User berhasil login'): ?ActivityLog
    {
        return $this->logTenantActivity('login', $description);
    }

    protected function logTenantLogout(string $description = 'User berhasil logout'): ?ActivityLog
    {
        return $this->logTenantActivity('logout', $description);
    }

    protected function logTenantSuspended(Model $entity, string $description): ?ActivityLog
    {
        return $this->logTenantActivity(
            'suspended',
            $description,
            $entity,
            null,
            ['status' => 'suspended']
        );
    }

    protected function logTenantActivated(Model $entity, string $description): ?ActivityLog
    {
        return $this->logTenantActivity(
            'activated',
            $description,
            $entity,
            null,
            ['status' => 'active']
        );
    }

    protected function logTenantPaid(Model $entity, string $description, ?array $paymentData = null): ?ActivityLog
    {
        return $this->logTenantActivity(
            'paid',
            $description,
            $entity,
            null,
            $paymentData
        );
    }

    protected function logTenantGenerated(Model $entity, string $description, ?array $generatedData = null): ?ActivityLog
    {
        return $this->logTenantActivity(
            'generated',
            $description,
            $entity,
            null,
            $generatedData
        );
    }

    protected function logTenantSynced(Model $entity, string $description, ?array $syncData = null): ?ActivityLog
    {
        return $this->logTenantActivity(
            'synced',
            $description,
            $entity,
            null,
            $syncData
        );
    }

    protected function logTenantCustomAction(
        string $action,
        string $description,
        ?Model $entity = null,
        ?array $data = null
    ): ?ActivityLog {
        return $this->logTenantActivity(
            $action,
            $description,
            $entity,
            null,
            $data
        );
    }
}
