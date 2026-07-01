<?php

namespace Modules\ITSP\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class TrsMapTechnology extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_technology';
    protected $primaryKey = ['coe_id', 'initiative_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'coe_id',
        'initiative_id',
    ];

    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        $keys = (array) $this->getKeyName();
        $values = [];
        foreach ($keys as $key) {
            $values[] = $this->getAttribute($key);
        }
        return implode('-', $values);
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }

    public function coe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'coe_id');
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
