<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrsBuCollaboration extends Pivot
{
    protected $table = 'trs_bu_collaboration';

    protected $fillable = [
        'initiative_id',
        'organization_id',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }
}
