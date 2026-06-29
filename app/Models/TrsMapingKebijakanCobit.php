<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapingKebijakanCobit extends Model
{
    protected $table = 'trs_maping_kebijakan_cobit';

    protected $fillable = [
        'objective_id',
        'cobit_domain',
        'cobit_objective',
        'description',
    ];

    /**
     * Get the objective that owns the mapping.
     */
    public function objective(): BelongsTo
    {
        return $this->belongsTo(MstObjective::class, 'objective_id', 'objective_id');
    }
}
