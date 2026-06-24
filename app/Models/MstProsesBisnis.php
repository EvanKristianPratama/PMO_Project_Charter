<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MstProsesBisnis extends Model
{
    protected $table = 'mst_proses_bisnis';

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'deskripsi',
        'order',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id' => 'integer',
        'order' => 'integer',
    ];


    /**
     * Relasi ke parent (self-referential).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstProsesBisnis::class, 'parent_id');
    }

    /**
     * Relasi ke children (self-referential).
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstProsesBisnis::class, 'parent_id');
    }

    /**
     * Relasi ke MstCompany.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    /**
     * Relasi ke MstKpi via trs_probis_kpi.
     */
    public function kpis(): BelongsToMany
    {
        return $this->belongsToMany(MstKpi::class, 'trs_probis_kpi', 'probis_id', 'kpi_id');
    }

    /**
     * Relasi ke TrsProbisKpi.
     */
    public function probisKpis(): HasMany
    {
        return $this->hasMany(TrsProbisKpi::class, 'probis_id');
    }

    /**
     * Relasi ke MstRegulation via trs_probis_regulation.
     */
    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_probis_regulation', 'probis_id', 'regulation_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke TrsProbisRegulation.
     */
    public function probisRegulations(): HasMany
    {
        return $this->hasMany(TrsProbisRegulation::class, 'probis_id');
    }
}
