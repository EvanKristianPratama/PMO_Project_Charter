<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsmapFuncRegulation extends Model
{
    protected $table = 'trs_map_func_regulation';

    protected $fillable = [
        'function_id',
        'regulation_id',
    ];
}
