<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsDefinitionRegulation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trs_definition_regulation';

    /**
     * The primary key associated with the table.
     * This table uses a composite primary key (definition_id, regulation_id).
     *
     * @var array
     */
    protected $primaryKey = ['definition_id', 'regulation_id'];

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
        'definition_id',
        'regulation_id',
    ];

    /**
     * Relasi ke MstDefinition.
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(MstDefinition::class, 'definition_id');
    }

    /**
     * Relasi ke MstRegulation.
     */
    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }
}
