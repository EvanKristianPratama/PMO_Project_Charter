<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProjectCharter;

class Project extends Model
{
    use SoftDeletes;

    protected $table = 'trs_projects';

    protected $fillable = ['code', 'name', 'owner_id', 'owner_name', 'status', 'metadata'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /* ── Relationships ─────────────────────────────── */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function charter(): HasOne
    {
        return $this->hasOne(ProjectCharter::class)->latestOfMany();
    }

    public function charters(): HasMany
    {
        return $this->hasMany(ProjectCharter::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('order');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'trs_program_project');
    }

    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(Goal::class, 'trs_goal_project');
    }

    /* ── Scopes ────────────────────────────────────── */

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
