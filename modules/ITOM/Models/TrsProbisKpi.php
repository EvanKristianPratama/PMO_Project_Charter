<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsProbisKpi extends Model
{
    protected $table = 'trs_probis_kpi';

    /**
     * The primary key associated with the table.
     * This table uses a composite primary key (probis_id, kpi_id).
     *
     * @var array
     */
    protected $primaryKey = ['probis_id', 'kpi_id'];

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
        'probis_id',
        'kpi_id',
    ];

    /**
     * Relasi ke MstProsesBisnis.
     */
    public function prosesBisnis(): BelongsTo
    {
        return $this->belongsTo(MstProsesBisnis::class, 'probis_id');
    }

    /**
     * Relasi ke MstKpi.
     */
    public function kpi(): BelongsTo
    {
        return $this->belongsTo(MstKpi::class, 'kpi_id');
    }
}
