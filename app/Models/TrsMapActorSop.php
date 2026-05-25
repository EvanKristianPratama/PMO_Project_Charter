<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsMapActorSop extends Model
{
    protected $table = 'trs_map_actor_sop';

    protected $fillable = [
        'actor_id',
        'sop_id',
    ];

    /**
     * Belongs to an actor.
     */
    public function actor()
    {
        return $this->belongsTo(MstActor::class, 'actor_id');
    }

    /**
     * Belongs to a SOP.
     */
    public function sop()
    {
        return $this->belongsTo(MstSop::class, 'sop_id');
    }
}
