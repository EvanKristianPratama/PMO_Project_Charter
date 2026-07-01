<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsFunctionOrganization extends Model
{
    protected $table = 'trs_function_organization';

    /**
     * Composite primary key.
     */
    protected $primaryKey = ['function_id', 'organization_id'];

    public $incrementing = false;

    protected $fillable = [
        'function_id',
        'organization_id',
    ];

    protected $casts = [
        'function_id'     => 'integer',
        'organization_id' => 'integer',
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
     * Fungsi (mst_function) yang di-assign.
     */
    public function function(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'function_id');
    }

    /**
     * Jabatan / BOD (mst_bod) yang menjadi anggota fungsi ini.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(MstBod::class, 'organization_id');
    }
}
