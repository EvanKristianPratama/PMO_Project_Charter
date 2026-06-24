<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstKpi extends Model
{
    protected $table = 'mst_kpi';

    protected $fillable = [
        'deskripsi',
        'company_id',
    ];

    /**
     * Relasi ke MstCompany.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
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
