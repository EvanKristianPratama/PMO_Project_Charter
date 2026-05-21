<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstSop extends Model
{
    protected $table = 'mst_sop';

    protected $fillable = [
        'tipe',
        'description'
    ];

    public function mapActorSops()
    {
        return $this->hasMany(TrsMapActorSop::class, 'sop_id');
    }
}
