<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcInitiative extends Model
{
    protected $table = 'trs_pc_initiative';

    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'pc_id');
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
