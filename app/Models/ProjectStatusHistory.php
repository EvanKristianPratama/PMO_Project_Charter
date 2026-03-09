<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStatusHistory extends Model
{
    protected $table = 'trs_project_status_history';

    protected $fillable = [
        'project_charter_id',
        'version',
        'tanggal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'project_charter_id' => 'integer',
            'version' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function projectCharter(): BelongsTo
    {
        return $this->belongsTo(ProjectCharter::class, 'project_charter_id');
    }
}
