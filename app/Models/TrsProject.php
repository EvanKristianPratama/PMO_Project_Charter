<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrsProject extends Model
{
    use SoftDeletes;

    protected $table = 'trs_projects';

    protected $fillable = [
        'code',
        'name',
        'owner_id',
        'owner_name',
        'status',
        'metadata',
        'tipe_inisiative',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function charter(): HasOne
    {
        return $this->hasOne(ProjectCharter::class, 'project_id')->latestOfMany('id');
    }

    public function charters(): HasMany
    {
        return $this->hasMany(ProjectCharter::class, 'project_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'project_id');
    }

    public function statusRef(): BelongsTo
    {
        return $this->belongsTo(InitiativeStatus::class, 'status');
    }

    public function pcStatusImplementations(): HasMany
    {
        return $this->hasMany(PcStatusImplementation::class, 'project_id')
            ->orderBy('date', 'desc')
            ->orderBy('time_start', 'desc')
            ->orderBy('id', 'desc');
    }

    public function latestPcStatusImplementation(): HasOne
    {
        return $this->hasOne(PcStatusImplementation::class, 'project_id')->latestOfMany('id');
    }

    public function mappedInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(MstInitiative::class, 'trs_pc_initiative', 'pc_id', 'initiative_id');
    }
}