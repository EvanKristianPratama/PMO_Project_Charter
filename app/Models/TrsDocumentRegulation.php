<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsDocumentRegulation extends Model
{
    protected $table = 'trs_document_regulation';

    /**
     * Composite primary key — Eloquent tidak support native composite PK,
     * nonaktifkan auto-increment dan tidak mendefinisikan $primaryKey tunggal.
     */
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'regulation_id',
        'document_id',
    ];

    protected $casts = [
        'regulation_id' => 'integer',
        'document_id'   => 'integer',
    ];

    /**
     * Relasi ke MstRegulation.
     */
    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    /**
     * Relasi ke MstDocument.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(MstDocument::class, 'document_id');
    }
}
