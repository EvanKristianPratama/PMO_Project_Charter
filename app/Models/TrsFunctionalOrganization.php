<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsFunctionalOrganization extends Model
{
    use LogsActivity;

    protected $table = 'trs_functional_organization';

    protected $primaryKey = ['structure_id', 'organization_id'];

    public $incrementing = false;

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

    protected $fillable = [
        'structure_id',
        'organization_id',
    ];

    public function functionalStructure(): BelongsTo
    {
        return $this->belongsTo(TrsFunctionalStructure::class, 'structure_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(MstBod::class, 'organization_id');
    }
}
