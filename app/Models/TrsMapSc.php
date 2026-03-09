<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsMapSc extends Model
{
    protected $table = 'trs_map_sc';

    protected $guarded = ['id'];

    public function Initiative()
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    public function ScopeCharter()
    {
        return $this->belongsTo(TrsScInitiative::class, 'sc_id');
    }
}
