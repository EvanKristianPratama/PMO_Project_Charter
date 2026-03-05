<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScDetail extends Model
{
    protected $table = 'trs_sc_details';

    protected $fillable = [
        'digital_id',
        'useCase_description',
        'current_situation',
        'key_functionalities',
        'value_detail',
        'urgency_detail',
        'ease_implementation',
        'ease_detail',
        'resource_requirement',
        'resource_detail',
        'interpendencies',
        'sign_by',
    ];

    public function scInitiative(): BelongsTo
    {
        return $this->belongsTo(ScInitiative::class, 'digital_id');
    }
}
