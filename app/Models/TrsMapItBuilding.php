<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapItBuilding extends Model
{
    protected $table = 'trs_map_itbuilding';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'primary',
        'secondary',
        'initiative_id',
    ];

    public function primaryCoe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'primary');
    }

    public function secondaryCoe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'secondary');
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
