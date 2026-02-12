<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    public const APP_ROLE_ADMIN = 'admin';
    public const APP_ROLE_USER = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'status',
        'app_role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ── Status Helpers ───────────────────────────── */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function primaryRoleName(): string
    {
        return $this->getRoleNames()->first() ?? 'Viewer';
    }

    public static function appRoles(): array
    {
        return [self::APP_ROLE_ADMIN, self::APP_ROLE_USER];
    }

    public function appRole(): string
    {
        $role = strtolower(trim((string) $this->app_role));

        return $role === self::APP_ROLE_ADMIN ? self::APP_ROLE_ADMIN : self::APP_ROLE_USER;
    }

    public function isAdminUser(): bool
    {
        return $this->appRole() === self::APP_ROLE_ADMIN;
    }

    /* ── Scopes ────────────────────────────────────── */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithAppRole($query, string $appRole)
    {
        return $query->whereRaw('LOWER(app_role) = ?', [strtolower($appRole)]);
    }

    public function accessAudits(): HasMany
    {
        return $this->hasMany(UserAccessAudit::class);
    }

    /* ── Accessors ─────────────────────────────────── */

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials;
    }
}
