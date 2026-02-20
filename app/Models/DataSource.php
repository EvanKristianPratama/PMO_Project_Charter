<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    protected $table = 'mst_data_source';

    protected $fillable = [
        'name',
        'month',
        'year',
    ];
}

