<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstKpi extends Model
{
    protected $table = 'mst_kpi';

    protected $fillable = [
        'deskripsi',
    ];

    /**
     * Relasi ke MstFunction.
     */
    public function functions(): BelongsToMany
    {
        return $this->belongsToMany(MstFunction::class, 'trs_function_kpi', 'kpi_id', 'function_id');
    }

    /**
     * Relasi ke MstProsesBisnis via trs_probis_kpi.
     */
    public function prosesBisnis(): BelongsToMany
    {
        return $this->belongsToMany(MstProsesBisnis::class, 'trs_probis_kpi', 'kpi_id', 'probis_id');
    }

    /**
     * Relasi ke TrsProbisKpi.
     */
    public function probisKpis(): HasMany
    {
        return $this->hasMany(TrsProbisKpi::class, 'kpi_id');
    }
}
