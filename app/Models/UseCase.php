<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UseCase extends Model
{
    protected $table = 'trs_use_case';

    protected $fillable = [
        'coe_id',
        'description',
    ];

    public function coe(): BelongsTo
    {
        return $this->belongsTo(Coe::class, 'coe_id');
    }
}
