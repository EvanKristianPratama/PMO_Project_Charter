<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstStrategicHouse extends Model
{
    protected $table = 'mst_strategic_house';

    protected $fillable = [
        'source',
        'vision',
        'mission',
        'additional_info'
    ];

    public function strategicHouseGoal(): HasMany
    {
        return $this->hasMany(Goal::class, 'pillar');
    }
}
