<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsResponsibility extends Model
{
    protected $table = 'trs_responsibilities';

    protected $fillable = [
        'role_id',
        'content',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(MstRole::class, 'role_id');
    }
}
