<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsFunctionalFunction extends Model
{
    use LogsActivity;

    protected $table = 'trs_functional_function';

    /**
     * Composite primary key.
     */
    protected $primaryKey = ['functional_id', 'function_id'];

    public $incrementing = false;

    protected $fillable = [
        'functional_id',
        'function_id',
    ];

    protected $casts = [
        'functional_id' => 'integer',
        'function_id'   => 'integer',
    ];

    // ─── Composite Key Support ────────────────────────────────────────────────

    public function getKeyName(): array|string
    {
        return $this->primaryKey;
    }

    public function getKey(): string
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[] = $this->getAttribute($key);
        }
        return implode('-', $keys);
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }
        return $query;
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    /**
     * Relasi ke MstFunctionalOrganization.
     */
    public function functionalOrganization(): BelongsTo
    {
        return $this->belongsTo(MstFunctionalOrganization::class, 'functional_id');
    }

    /**
     * Relasi ke MstFunction.
     */
    public function functionModel(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'function_id');
    }
}
