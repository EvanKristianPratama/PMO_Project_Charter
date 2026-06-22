<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstBod extends Model
{
    use LogsActivity;

    protected $table = 'mst_bod';

    protected $fillable = [
        'company_id',
        'name',
        'sumber',
        'pejabat',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }
}
