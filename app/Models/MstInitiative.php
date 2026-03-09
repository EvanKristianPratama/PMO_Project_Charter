<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstInitiative extends Model
{
    protected $table = 'mst_initiative';

    protected $fillable = [
        'coe_id',
        'tipe_initiative',
        'business_unit',
        'project_id',
        'code',
        'name',
        'description',
        'status',
        'source',
    ];

    public function coe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'coe_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'business_unit');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            TrsOrganization::class,
            'trs_bu_collaboration',
            'initiative_id',
            'ogranitiation_id',
        )->withTimestamps();
    }

    public function initiativeRelationsRow(): HasMany
    {
        return $this->hasMany(MstInitiativeRelation::class, 'initiative_code_row');
    }

    public function initiativeRelationsColumn(): HasMany
    {
        return $this->hasMany(MstInitiativeRelation::class, 'initiative_code_column');
    }

    public function pcInitiatives(): HasMany
    {
        return $this->hasMany(PcInitiative::class, 'initiative_id');
    }

    /**
     * Status history entries (trs_status_mstinitiative).
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(StatusMstInitiative::class, 'initiative_id');
    }

    /**
     * The latest status entry.
     */
    public function latestStatus(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StatusMstInitiative::class, 'initiative_id')->latestOfMany();
    }

    public function mappedProjects(): BelongsToMany
    {
        return $this->belongsToMany(TrsProject::class, 'trs_pc_initiative', 'initiative_id', 'pc_id');
    }

    public function sourceData(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'source');
    }

    public function mapSc(): HasMany
    {
        return $this->hasMany(TrsMapSc::class, 'initiative_id');
    }
}
