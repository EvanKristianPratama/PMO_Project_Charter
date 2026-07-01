<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;

class TrsRelatedRegulation extends Model
{
    protected $table = 'trs_related_regulation';

    /**
     * The primary key for the model.
     * This table uses a composite primary key (regulation, related).
     */
    protected $primaryKey = ['regulation', 'related'];

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'regulation',
        'related',
    ];

    /**
     * Get the regulation (source) document.
     */
    public function regulation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation', 'id');
    }

    /**
     * Get the related regulation document.
     */
    public function relatedRegulation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'related', 'id');
    }
}
