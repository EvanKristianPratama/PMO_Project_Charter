<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstApqc extends Model
{
    protected $table = 'mst_apqc';

    protected $fillable = [
        'parent_id',
        'name',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * Relasi ke parent (self-referential).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstApqc::class, 'parent_id');
    }

    /**
     * Relasi ke children (self-referential).
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstApqc::class, 'parent_id');
    }
}
