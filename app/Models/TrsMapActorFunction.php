<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapActorFunction extends Model
{
    protected $table = 'trs_map_actor_function';

    /**
     * The primary key associated with the table.
     * This table uses a composite primary key (actor_id, function_id).
     *
     * @var array
     */
    protected $primaryKey = ['actor_id', 'function_id'];

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
        'actor_id',
        'function_id',
    ];

    /**
     * Relasi ke MstActor.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(MstActor::class, 'actor_id');
    }

    /**
     * Relasi ke MstFunction.
     */
    public function mstFunction(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'function_id');
    }
}
