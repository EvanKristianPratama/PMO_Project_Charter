<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusDigital extends Model
{
    protected $table = 'trs_status_digital';

    protected $fillable = [
        'phase_id',
        'name',
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(PhaseDigital::class, 'phase_id');
    }
}
