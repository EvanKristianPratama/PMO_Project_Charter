<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstFunction extends Model
{
    protected $table = 'mst_function';

    protected $fillable = [
        'groub_id',
        'parent_id',
        'code',
        'name',
        'alias',
        'jabatan',
        'pejabat',
        'sk',
    ];

    protected $casts = [
        'groub_id' => 'integer',
        'parent_id' => 'integer',
        'code' => 'string',
    ];

    /**
     * Relasi ke parent (self-referential).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'parent_id');
    }

    /**
     * Relasi ke children (self-referential).
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstFunction::class, 'parent_id');
    }

    /**
     * Relasi ke Groub.
     */
    public function groub(): BelongsTo
    {
        return $this->belongsTo(Groub::class, 'groub_id');
    }
}
