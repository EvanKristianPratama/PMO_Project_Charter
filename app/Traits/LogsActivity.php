<?php

namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Support\Arr;

/**
 * Tambahkan trait ini ke Eloquent model yang ingin di-audit.
 *
 * Kolom yang dikecualikan dari diff (tidak dicatat perubahannya):
 *   protected array $auditExclude = ['updated_at', 'password'];
 *
 * Kolom yang HANYA dicatat (jika diset, kolom lain diabaikan):
 *   protected array $auditInclude = ['name', 'status'];
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (self $model): void {
            ActivityLogService::log(
                event: 'created',
                description: 'Menambah data '.class_basename($model).': '.self::resolveLabel($model),
                subject: $model,
                properties: ['new' => $model->toArray()],
            );
        });

        static::updated(function (self $model): void {
            $dirty = $model->getDirty();
            $dirty = self::filterAuditFields($model, $dirty);

            if (empty($dirty)) {
                return;
            }

            $old = Arr::only($model->getOriginal(), array_keys($dirty));

            ActivityLogService::log(
                event: 'updated',
                description: 'Mengubah data '.class_basename($model).': '.self::resolveLabel($model),
                subject: $model,
                properties: ['old' => $old, 'new' => $dirty],
            );
        });

        static::deleted(function (self $model): void {
            ActivityLogService::log(
                event: 'deleted',
                description: 'Menghapus data '.class_basename($model).': '.self::resolveLabel($model),
                subject: $model,
                properties: ['old' => $model->toArray()],
            );
        });
    }

    private static function resolveLabel(self $model): string
    {
        foreach (['name', 'nama', 'title', 'judul', 'kode', 'code', 'email'] as $attr) {
            if (isset($model->$attr) && $model->$attr) {
                return (string) $model->$attr;
            }
        }

        return class_basename(get_class($model)).' #'.$model->getKey();
    }

    private static function filterAuditFields(self $model, array $dirty): array
    {
        // Selalu kecualikan kolom teknis
        $defaultExclude = ['updated_at', 'created_at', 'remember_token', 'password'];
        $exclude = array_merge($defaultExclude, $model->auditExclude ?? []);
        $dirty = Arr::except($dirty, $exclude);

        // Jika auditInclude didefinisikan, hanya ambil kolom tersebut
        if (! empty($model->auditInclude ?? [])) {
            $dirty = Arr::only($dirty, $model->auditInclude);
        }

        return $dirty;
    }
}
