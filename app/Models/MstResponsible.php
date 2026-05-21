<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstResponsible extends Model
{
    protected $table = 'mst_responsible';

    protected $fillable = [
        'responsible',
    ];
}
