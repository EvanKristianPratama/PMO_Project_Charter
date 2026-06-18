<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MstFunction extends Model
{
    protected $table = 'mst_function';

    protected $fillable = [
        'groub_id',
        'parent_id',
        'code',
        'name',
        'alias',
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

    /**
     * Relasi ke MstRegulation.
     */
    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_map_func_regulation', 'function_id', 'regulation_id')
            ->withTimestamps();
    }
}
