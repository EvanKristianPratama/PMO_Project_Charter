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
        'company_id',
        'parent_id',
        'name',
        'alias',
        'deskripsi',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id' => 'integer',
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
     * Relasi ke MstCompany.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    /**
     * Relasi ke MstRegulation.
     */
    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_map_func_regulation', 'function_id', 'regulation_id')
            ->withTimestamps();
    }



    /**
     * Relasi ke MstActor via trs_map_actor_function.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(MstActor::class, 'trs_map_actor_function', 'function_id', 'actor_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /**
     * One-to-Many relationship with TrsMapActorFunction.
     */
    public function mapActorFunctions(): HasMany
    {
        return $this->hasMany(TrsMapActorFunction::class, 'function_id');
    }
}
