<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsTkoContent extends Model
{
    protected $table = 'trs_tko_content';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'regulation_id',
        'section_id',
        'content',
    ];

    /**
     * Relasi ke TrsTkoSections
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(TrsTkoSections::class, 'section_id');
    }

    /**
     * Relasi ke MstRegulation
     */
    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }
}
