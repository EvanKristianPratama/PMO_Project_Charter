<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsInitiativeRelationPosition extends Model
{
    protected $table = 'trs_initiative_relation_position';

    protected $fillable = [
        'initiative_id',
        'x',
        'y',
        'is_locked',
    ];

    protected $casts = [
        'x' => 'float',
        'y' => 'float',
        'is_locked' => 'boolean',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
