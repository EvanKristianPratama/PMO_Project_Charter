<?php

namespace Modules\ITOM\Models;

use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstResource extends Model
{
    protected $table = 'mst_resource_management';

    protected $casts = [
        'jabatan' => 'integer',
        'start' => 'date:Y-m-d',
        'end' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'name',
        'jabatan',
        'internal_id',
        'sk',
        'start',
        'end',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'jabatan');
    }
}
