<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
