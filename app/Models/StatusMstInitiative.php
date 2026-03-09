<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusMstInitiative extends Model
{
    protected $table = 'trs_status_mstinitiative';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    /* ── Relationships ─────────────────────────────── */

    /**
     * The master initiative this status belongs to.
     */
    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
