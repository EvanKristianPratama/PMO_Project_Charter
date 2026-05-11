<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ActivityLogService
{
    /**
     * Catat aktivitas ke database.
     */
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $actor = null
    ): void {
        try {
            /** @var User|null $user */
            $user = $actor ?? Auth::user();

            $key = $subject?->getKey();
            if (is_array($key)) {
                $key = implode('-', $key);
            }

            ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'event' => $event,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $key,
                'subject_label' => $subject ? static::resolveSubjectLabel($subject) : null,
                'description' => $description,
                'properties' => $properties,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (Throwable) {
            // Jangan biarkan kegagalan logging menghentikan proses utama
        }
    }

    /** Login */
    public static function login(User $user): void
    {
        static::log(
            event: 'login',
            description: "User {$user->name} ({$user->email}) berhasil login.",
            actor: $user
        );
    }

    /** Logout */
    public static function logout(User $user): void
    {
        static::log(
            event: 'logout',
            description: "User {$user->name} ({$user->email}) logout.",
            actor: $user
        );
    }

    /** Ambil label human-readable dari model */
    private static function resolveSubjectLabel(Model $subject): string
    {
        // Coba berbagai atribut umum untuk label
        foreach (['name', 'nama', 'title', 'judul', 'kode', 'code', 'email'] as $attr) {
            if (isset($subject->$attr) && $subject->$attr) {
                return (string) $subject->$attr;
            }
        }

        $key = $subject->getKey();
        if (is_array($key)) {
            $key = implode('-', $key);
        }

        return class_basename(get_class($subject)).' #'.$key;
    }
}
