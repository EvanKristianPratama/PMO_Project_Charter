<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsFunctionKpi extends Model
{
    protected $table = 'trs_function_kpi';

    public $incrementing = false;

    protected $fillable = [
        'function_id',
        'kpi_id',
    ];

    /**
     * Relasi ke MstFunction.
     */
    public function function(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'function_id');
    }

    /**
     * Relasi ke MstKpi.
     */
    public function kpi(): BelongsTo
    {
        return $this->belongsTo(MstKpi::class, 'kpi_id');
    }
}
