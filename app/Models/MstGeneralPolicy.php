<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstGeneralPolicy extends Model
{
    protected $table = 'mst_general_policy';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'number',
        'description',
    ];
}
