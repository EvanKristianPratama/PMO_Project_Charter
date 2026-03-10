<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class TrsScDetails extends Model
{
    use LogsActivity;
    protected $table = 'trs_sc_details';

    protected $guarded = ['id'];

    public function ScInitiative()
    {
        return $this->belongsTo(TrsScInitiative::class, 'sc_id');
    }
}
