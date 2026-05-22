<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstActor extends Model
{
    protected $table = 'mst_actor';

    protected $fillable = [
        'name',
        'organization_id',
    ];

    /**
     * Relasi ke TrsOrganization
     */
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }

    /**
     * One-to-Many relationship with TrsMapActorSop.
     */
    public function mapActorSops()
    {
        return $this->hasMany(TrsMapActorSop::class, 'actor_id');
    }
}
