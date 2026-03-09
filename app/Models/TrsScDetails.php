<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsScDetails extends Model
{
    protected $table = 'trs_sc_details';

    protected $guarded = ['id'];

    public function ScInitiative()
    {
        return $this->belongsTo(TrsScInitiative::class, 'sc_id');
    }
}
