<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrsOrganization extends Model
{
    use LogsActivity;

    protected $table = 'trs_organization';
    protected $fillable = [
        'groub_id',
        'parent_id',
        'code',
        'name',
        'alias',
        'jabatan',
        'pejabat',
        'sk'
    ];

    protected $casts = [
        'groub_id' => 'integer',
        'parent_id' => 'integer',
        'code' => 'string',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TrsOrganization::class, 'parent_id');
    }

    public function groub(): BelongsTo
    {
        return $this->belongsTo(Groub::class, 'groub_id');
    }

    public function initiatives(): HasMany
    {
        return $this->hasMany(MstInitiative::class, 'business_unit');
    }

    public function businessStrategies(): HasMany
    {
        return $this->hasMany(TrsBusinessStrategy::class, 'business_unit');
    }

    public function picOrganization(): HasMany
    {
        return $this->hasMany(MstPicProject::class, 'organization_id');
    }

    public function picProjects(): HasMany
    {
        return $this->hasMany(MstPicProject::class, 'organization_id');
    }

    public function prosesBisnis(): HasMany
    {
        return $this->hasMany(TrsProsesBisnis::class, 'organization_id');
    }

    public function itSteeringCommittees(): HasMany
    {
        return $this->hasMany(MstItSteeringComittee::class, 'organization_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(MstResource::class, 'jabatan');
    }

    /**
     * Many-to-Many relationship with MstActor via trs_map_actor_organization.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(MstActor::class, 'trs_map_actor_organization', 'organization', 'actor')
            ->withTimestamps();
    }

    /**
     * One-to-Many relationship with TrsMapActorOrganization.
     */
    public function mapActorOrganizations(): HasMany
    {
        return $this->hasMany(TrsMapActorOrganization::class, 'organization');
    }
}
