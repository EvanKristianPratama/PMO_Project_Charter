<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstProsesBisnis extends Model
{
    protected $table = 'mst_proses_bisnis';

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
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
        return $this->belongsTo(MstProsesBisnis::class, 'parent_id');
    }

    /**
     * Relasi ke children (self-referential).
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstProsesBisnis::class, 'parent_id');
    }

    /**
     * Relasi ke MstCompany.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }
}
