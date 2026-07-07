<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstDocument extends Model
{
    protected $table = 'mst_document';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
    ];
}