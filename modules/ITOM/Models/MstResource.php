<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'jabatan');
    }
}
