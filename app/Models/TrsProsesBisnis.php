<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsProsesBisnis extends Model
{
    use LogsActivity;

    protected $table = 'trs_proses_bisnis';

    protected $fillable = [
        'organization_id',
        'no',
        'proses_bisnis',
        'tugas',
        'hasil',
        'status',
    ];

    protected $casts = [
        'organization_id' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }
}
