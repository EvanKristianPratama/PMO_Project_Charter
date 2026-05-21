<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstActor extends Model
{
    protected $table = 'mst_actor';

    protected $fillable = [
        'name',
    ];

    /**
     * One-to-Many relationship with TrsMapActorSop.
     */
    public function mapActorSops()
    {
        return $this->hasMany(TrsMapActorSop::class, 'actor_id');
    }
}
