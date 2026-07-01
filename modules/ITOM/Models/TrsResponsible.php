<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsResponsible extends Model
{
    protected $table = 'trs_responsible';

    protected $fillable = [
        'role_id',
        'responsible_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(MstRole::class, 'role_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(MstResponsible::class, 'responsible_id');
    }
}
