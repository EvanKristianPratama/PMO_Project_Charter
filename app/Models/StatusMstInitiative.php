<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\LogsActivity;

class StatusMstInitiative extends Model
{
    use LogsActivity;
    protected $table = 'trs_status_mstinitiative';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saved(static function (StatusMstInitiative $status): void {
            $status->initiative?->syncApprovedProjectToImplementation();
        });
    }

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
