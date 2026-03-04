<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCharter extends Model
{
    protected $table = 'trs_project_charters';

    protected $fillable = [
        'project_id',
        'version_label',
        'status',
        'owner',
        'category',
        'duration',
        'background',
        'objectives',
        'scope',
        'impact_value',
        'key_personnel',
        'key_items',
        'budget',
        'risks_identified',
        'risk_mitigation',
        'tgl_dokumen',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class);
    }
}
