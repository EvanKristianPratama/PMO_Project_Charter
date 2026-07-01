<?php

namespace Modules\ITSP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsPillarStrategy extends Model
{
    protected $table = 'trs_pillar_strategy';

    protected $fillable = [
        'themes_id',
        'title',
        'strategy',
    ];

    public function themesPilar(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'themes_id');
    }
}
