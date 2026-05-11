<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class TrsScDetails extends Model
{
    use LogsActivity;

    protected $table = 'trs_sc_details';

    protected $guarded = ['id'];

    protected $casts = ['update_doc' => 'date:Y-m-d'];

    public function ScInitiative()
    {
        return $this->belongsTo(TrsScInitiative::class, 'sc_id');
    }
}
