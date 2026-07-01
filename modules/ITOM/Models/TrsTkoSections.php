<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrsTkoSections extends Model
{
    protected $table = 'trs_tko_sections';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'order',
    ];

    /**
     * Relasi ke TrsTkoContent
     */
    public function contents(): HasMany
    {
        return $this->hasMany(TrsTkoContent::class, 'section_id');
    }
}
