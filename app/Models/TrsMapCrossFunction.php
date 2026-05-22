<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapCrossFunction extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_cross_function';

    public $timestamps = false;

    protected $fillable = [
        'pc_id',
        'organization_id',
    ];

    /**
     * Relasi ke TrsProject (Project level)
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'pc_id');
    }

    /**
     * Relasi ke TrsOrganization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }
}
