<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class MstScSource extends Model
{
    use LogsActivity;
    protected $table = 'mst_sc_source';

    protected $guarded = ['id'];

    public function ScInitiative()
    {
        return $this->hasMany(TrsScInitiative::class, 'source_id', 'id');
    }
}
