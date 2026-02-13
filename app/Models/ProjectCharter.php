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
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
