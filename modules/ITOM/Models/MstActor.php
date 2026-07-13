<?php

namespace Modules\ITOM\Models;

use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ITOM\Models\MstFunction;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Models\MstSop;
use Modules\ITOM\Models\TrsMapActorFunction;
use Modules\ITOM\Models\TrsMapActorOrganization;
use Modules\ITOM\Models\TrsMapActorSop;

class MstActor extends Model
{
    protected $table = 'mst_actor';

    protected $fillable = [
        'name',
        'organization_id',
        'regulation_id',
        'tipe',
    ];

    /**
     * Relasi ke MstRegulation
     */
    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    /**
     * Relasi ke MstSop melalui regulation_id
     */
    public function sops(): HasMany
    {
        return $this->hasMany(MstSop::class, 'regulation_id', 'regulation_id');
    }

    /**
     * Relasi ke TrsOrganization
     */
    public function organization(): BelongsTo
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
    public function functions(): BelongsToMany
    {
        return $this->belongsToMany(MstFunction::class, 'trs_map_actor_function', 'actor_id', 'function_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /**
     * One-to-Many relationship with TrsMapActorFunction.
     */
    public function mapActorFunctions(): HasMany
    {
        return $this->hasMany(TrsMapActorFunction::class, 'actor_id');
    }

    /**
     * Many-to-Many relationship with TrsOrganization via trs_map_actor_organization.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(TrsOrganization::class, 'trs_map_actor_organization', 'actor', 'organization')
            ->withTimestamps();
    }

    /**
     * One-to-Many relationship with TrsMapActorOrganization.
     */
    public function mapActorOrganizations(): HasMany
    {
        return $this->hasMany(TrsMapActorOrganization::class, 'actor');
    }
}
