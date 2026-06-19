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

    /**
     * Many-to-Many relationship with MstFunction.
     */
    public function functions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MstFunction::class, 'trs_map_actor_function', 'actor_id', 'function_id')
            ->withTimestamps('created_at', 'updated-at');
    }

    /**
     * One-to-Many relationship with TrsMapActorFunction.
     */
    public function mapActorFunctions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrsMapActorFunction::class, 'actor_id');
    }

    /**
     * Many-to-Many relationship with TrsOrganization via trs_map_actor_organization.
     */
    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TrsOrganization::class, 'trs_map_actor_organization', 'actor', 'organization')
            ->withTimestamps();
    }

    /**
     * One-to-Many relationship with TrsMapActorOrganization.
     */
    public function mapActorOrganizations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrsMapActorOrganization::class, 'actor');
    }
}
