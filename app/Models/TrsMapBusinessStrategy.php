<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapBusinessStrategy extends Model
{
    protected $table = 'trs_map_business_strategy';

    protected $fillable = [
        'initiative_id',
        'strategy_id',
    ];

    public function initiative()
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    public function strategy()
    {
        return $this->belongsTo(MstBusinessStrategy::class, 'strategy_id');
    }
}
