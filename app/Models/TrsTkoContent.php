<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsTkoContent extends Model
{
    protected $table = 'trs_tko_content';
    protected $primaryKey = ['regulation_id', 'section_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'regulation_id',
        'section_id',
        'content',
    ];

    /**
     * Override setKeysForSaveQuery to support composite primary keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('regulation_id', '=', $this->getAttribute('regulation_id'))
              ->where('section_id', '=', $this->getAttribute('section_id'));

        return $query;
    }

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
