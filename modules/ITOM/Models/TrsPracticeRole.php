<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsPracticeRole extends Model
{
    protected $table = 'trs_practicerole';

    protected $fillable = [
        'practice_id',
        'role_id',
        'r_a',
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(MstPractice::class, 'practice_id', 'practice_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(MstRole::class, 'role_id', 'id');
    }
}
