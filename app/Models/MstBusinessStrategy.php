<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class MstBusinessStrategy extends Model
{
    protected $table = 'mst_business_strategy';

    protected $fillable = [
        'goal_id',
        'code',
        'strategy',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    public function mapBusinessStrategies(): HasMany
    {
        return $this->hasMany(TrsMapBusinessStrategy::class, 'strategy_id');
    }

    public function misiBumn(): BelongsTo
    {
        return $this->belongsTo(MstMisiBumn::class, 'misi_id');
    }
}
