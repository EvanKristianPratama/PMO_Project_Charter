<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstRegulation extends Model
{
    protected $table = 'mst_regulation';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'judul',
        'tipe',
        'owner',
        'revisi',
        'terbit',
        'berlaku',
    ];

    protected $casts = [
        'terbit' => 'date:Y-m-d',
        'berlaku' => 'date:Y-m-d',
    ];
}
