<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MstResponsible extends Model
{
    protected $table = 'mst_responsible';

    protected $fillable = [
        'responsible',
    ];

    /**
     * Relasi ke MstObjective (Kebijakan Khusus Bab 2)
     */
    public function objectives(): BelongsToMany
    {
        return $this->belongsToMany(
            MstObjective::class,
            'trs_responsible_objective',
            'responsible_id',
            'objective_id'
        )->withTimestamps();
    }
}
