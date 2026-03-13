<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScStatusImplementation extends Model
{
    use LogsActivity;

    protected $table = 'trs_sc_status_implementation';

    protected $guarded = ['id'];

    /* ── Relationships ─────────────────────────────── */

    /**
     * The scope-charter initiative (trs_sc_initiative) this status belongs to.
     */
    public function scInitiative(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'digital_initiative_id');
    }
}
