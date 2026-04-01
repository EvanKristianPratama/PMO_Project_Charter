<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsStatusImplementation extends Model
{
    protected $table = 'trs_status_implementation';

    protected $guarded = ['id'];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id', 'id');
    }

    public function mstDigitalInitiatives(): BelongsTo
    {
        return $this->initiative();
    }
}
