<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalInitiative extends Model
{
    use SoftDeletes;
    protected $table = 'mst_digitalInitiative';

    protected $fillable = [
        'type', 
        'no', 
        'projectOwner', 
        'useCase', 
        'desc',
        'value', 
        'urgency',
        'rjjp',
        'coe'
    ];
}
