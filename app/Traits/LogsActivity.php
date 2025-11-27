<?php

namespace App\Traits;

use App\Models\PlatformActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected function logActivity(
        string $action,
        string $description,
        ?Model $entity = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): PlatformActivityLog {
        return PlatformActivityLog::create([
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

    protected function logCreated(Model $entity, string $description): PlatformActivityLog
    {
        return $this->logActivity(
            'created',
            $description,
            $entity,
            null,
            $entity->toArray()
        );
    }

    protected function logUpdated(Model $entity, string $description, array $oldValues = []): PlatformActivityLog
    {
        $changedAttributes = [];
        foreach ($entity->getDirty() as $key => $value) {
            $changedAttributes[$key] = $value;
        }

        return $this->logActivity(
            'updated',
            $description,
            $entity,
            $oldValues ?: $entity->getOriginal(),
            $changedAttributes ?: $entity->toArray()
        );
    }

    protected function logDeleted(Model $entity, string $description): PlatformActivityLog
    {
        return $this->logActivity(
            'deleted',
            $description,
            $entity,
            $entity->toArray(),
            null
        );
    }

    protected function logLogin(string $description = 'User logged in'): PlatformActivityLog
    {
        return $this->logActivity('login', $description);
    }

    protected function logLogout(string $description = 'User logged out'): PlatformActivityLog
    {
        return $this->logActivity('logout', $description);
    }

    protected function logSuspended(Model $entity, string $description): PlatformActivityLog
    {
        return $this->logActivity(
            'suspended',
            $description,
            $entity,
            null,
            ['is_suspended' => true]
        );
    }

    protected function logActivated(Model $entity, string $description): PlatformActivityLog
    {
        return $this->logActivity(
            'activated',
            $description,
            $entity,
            null,
            ['is_active' => true]
        );
    }

    protected function logCustomAction(
        string $action,
        string $description,
        ?Model $entity = null,
        ?array $data = null
    ): PlatformActivityLog {
        return $this->logActivity(
            $action,
            $description,
            $entity,
            null,
            $data
        );
    }
}
