<?php

namespace App\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            self::recordAudit($model, 'created');
        });

        static::updated(function (Model $model): void {
            self::recordAudit($model, 'updated');
        });

        static::deleted(function (Model $model): void {
            self::recordAudit($model, 'deleted');
        });
    }

    protected static function recordAudit(Model $model, string $action): void
    {
        $fillable = $model->getFillable();

        $onlyFillable = function (?array $values) use ($fillable): ?array {
            if ($values === null) {
                return null;
            }

            return array_intersect_key($values, array_flip($fillable));
        };

        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = $onlyFillable($model->getAttributes());
        } elseif ($action === 'updated') {
            $oldValues = $onlyFillable($model->getOriginal());
            $newValues = $onlyFillable($model->getChanges());
        } elseif ($action === 'deleted') {
            $oldValues = $onlyFillable($model->getOriginal());
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip' => request()->ip() ?? null,
        ]);
    }
}
