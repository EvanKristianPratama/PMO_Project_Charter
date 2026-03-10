<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'event',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Label singkat untuk event (untuk tampilan UI) */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'login'   => 'Login',
            'logout'  => 'Logout',
            'created' => 'Tambah Data',
            'updated' => 'Ubah Data',
            'deleted' => 'Hapus Data',
            default   => ucfirst($this->event),
        };
    }

    /** Warna badge event */
    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            'login'   => 'emerald',
            'logout'  => 'slate',
            'created' => 'blue',
            'updated' => 'amber',
            'deleted' => 'rose',
            default   => 'slate',
        };
    }

    /** Nama pendek subject_type (tanpa namespace) */
    public function getSubjectClassAttribute(): ?string
    {
        if (! $this->subject_type) {
            return null;
        }

        return class_basename($this->subject_type);
    }
}
