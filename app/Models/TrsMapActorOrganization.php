<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapActorOrganization extends Model
{
    protected $table = 'trs_map_actor_organization';

    /**
     * The primary key associated with the table.
     * This table uses a composite primary key (actor, organization).
     *
     * @var array
     */
    protected $primaryKey = ['actor', 'organization'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the composite primary key name.
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the composite primary key value.
     */
    public function getKey()
    {
        $keys = (array) $this->getKeyName();
        $values = [];
        foreach ($keys as $key) {
            $values[] = $this->getAttribute($key);
        }
        return implode('-', $values);
    }

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'actor',
        'organization',
    ];

    /**
     * Relasi ke MstActor.
     */
    public function actorModel(): BelongsTo
    {
        return $this->belongsTo(MstActor::class, 'actor');
    }

    /**
     * Relasi ke TrsOrganization.
     */
    public function organizationModel(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization');
    }
}
