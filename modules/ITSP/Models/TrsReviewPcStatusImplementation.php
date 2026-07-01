<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class TrsReviewPcStatusImplementation extends Model
{
    use LogsActivity;

    protected $table = 'trs_review_pc_status_implementation';

    protected $guarded = ['id'];

    protected $appends = ['periode_label'];

    public function project()
    {
        return $this->belongsTo(TrsProject::class, 'project_id');
    }

    /**
     * Accessor: "Start - Year" or "Start - End - Year".
     */
    public function getPeriodeLabelAttribute(): ?string
    {
        $start = $this->attributes['start'] ?? null;
        $year  = $this->attributes['year'] ?? null;

        if (! $start) {
            return null;
        }

        $end = $this->attributes['end'] ?? null;

        if ($end) {
            return "{$start} - {$end} {$year}";
        }

        return $year ? "{$start} {$year}" : $start;
    }
}
