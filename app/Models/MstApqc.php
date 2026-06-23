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

    protected $appends = [
        'level',
        'depth',
    ];

    /**
     * Get the level of the APQC process (1-based index).
     */
    public function getLevelAttribute(): int
    {
        if (array_key_exists('level', $this->attributes)) {
            return (int) $this->attributes['level'];
        }

        $level = 1;
        $current = $this;
        while ($current->parent_id) {
            $level++;
            $parent = $current->parent;
            if ($parent) {
                $current = $parent;
            } else {
                break;
            }
        }
        return $level;
    }

    /**
     * Get the depth of the APQC process (0-based index).
     */
    public function getDepthAttribute(): int
    {
        if (array_key_exists('depth', $this->attributes)) {
            return (int) $this->attributes['depth'];
        }
        return $this->level - 1;
    }

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
