<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstSop extends Model
{
    protected $table = 'mst_sop';

    protected $fillable = [
        'regulation_id',
        'tipe',
        'description'
    ];

    public function regulation()
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    public function mapActorSops()
    {
        return $this->hasMany(TrsMapActorSop::class, 'sop_id');
    }
}
