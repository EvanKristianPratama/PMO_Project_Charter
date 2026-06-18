<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstResource extends Model
{
    protected $table = 'mst_resource_management';

    protected $fillable = [
        'name',
        'jabatan',
        'internal_id',
        'masa_berlaku',
    ];
}
