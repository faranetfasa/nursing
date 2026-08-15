<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Writes the audit trail for sensitive operations. Every service that mutates
 * sensitive data is expected to call this instead of logging ad hoc.
 */
class AuditService
{
    public function log(string $event, array $attributes = []): ?AuditLog
    {
        if (! config('core.audit.enabled', true)) {
            return null;
        }

        $request = request();
        $user = Auth::user();

        $payload = array_merge([
            'user_id' => $attributes['user_id'] ?? $user?->getAuthIdentifier(),
            'organization_id' => $attributes['organization_id'] ?? ($user->organization_id ?? null),
            'module' => $attributes['module'] ?? 'Core',
            'event' => $event,
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => substr((string) $request?->userAgent(), 0, 1000),
        ], $attributes);

        try {
            if (! Schema::hasTable('audit_logs')) {
                return null;
            }

            return AuditLog::query()->create($payload);
        } catch (\Throwable $exception) {
            Log::warning('Unable to persist audit log', [
                'event' => $event,
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function logModel(string $event, Model $model, array $attributes = []): ?AuditLog
    {
        return $this->log($event, array_merge([
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
        ], $attributes));
    }

    public function logCreated(Model $model, ?string $module = null): ?AuditLog
    {
        return $this->logModel('created', $model, [
            'module' => $module ?? 'Core',
            'new_values' => $this->sanitize($model->getAttributes()),
        ]);
    }

    public function logUpdated(Model $model, array $original, ?string $module = null): ?AuditLog
    {
        return $this->logModel('updated', $model, [
            'module' => $module ?? 'Core',
            'old_values' => $this->sanitize(array_intersect_key($original, $model->getChanges())),
            'new_values' => $this->sanitize($model->getChanges()),
        ]);
    }

    public function logDeleted(Model $model, ?string $module = null): ?AuditLog
    {
        return $this->logModel('deleted', $model, [
            'module' => $module ?? 'Core',
            'old_values' => $this->sanitize($model->getAttributes()),
        ]);
    }

    /** Removes credentials and other secrets before they reach the audit trail. */
    private function sanitize(array $values): array
    {
        foreach (config('core.audit.hidden_attributes', []) as $hidden) {
            unset($values[$hidden]);
        }

        return $values;
    }
}
