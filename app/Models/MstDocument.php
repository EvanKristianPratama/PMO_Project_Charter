<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstDocument extends Model
{
    protected $table = 'mst_document';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
    ];

    /**
     * Relasi ke TrsDocumentRegulation.
     */
    public function documentRegulations(): HasMany
    {
        return $this->hasMany(TrsDocumentRegulation::class, 'document_id');
    }

    /**
     * Relasi many-to-many ke MstRegulation via trs_document_regulation.
     */
    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_document_regulation', 'document_id', 'regulation_id')
            ->withTimestamps();
    }
}