<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstActor extends Model
{
    protected $table = 'mst_actor';

    protected $fillable = [
        'name',
        'organization_id',
        'regulation_id',
    ];

    /**
     * Relasi ke MstRegulation
     */
    public function regulation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    /**
     * Relasi ke MstSop melalui regulation_id
     */
    public function sops(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MstSop::class, 'regulation_id', 'regulation_id');
    }

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
