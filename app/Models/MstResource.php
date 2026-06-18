<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstResource extends Model
{
    protected $table = 'mst_resource_management';

    protected $casts = [
        'jabatan' => 'integer',
    ];

    protected $fillable = [
        'name',
        'jabatan',
        'internal_id',
        'sk',
    ];

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'jabatan');
    }
}
