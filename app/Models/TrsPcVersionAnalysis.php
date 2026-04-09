<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsPcVersionAnalysis extends Model
{
    use LogsActivity;

    protected $table = 'trs_pc_version_analysis';

    protected $fillable = [
        'project_id',
        'version_label',
        'sponsor',
        'owner',
        'leader',
        'category',
        'duration',
        'tgl_dokumen',
        'target_kpi',
        'start_year',
        'end_year',
        'background',
        'objectives',
        'impact_value',
        'key_personnel',
        'key_items',
        'budget',
        'key_milestone',
        'risks_identified',
        'risk_mitigation',
        'notes',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class);
    }
}
