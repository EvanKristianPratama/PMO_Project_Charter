<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class UcStatusImplementation extends Model
{
    use LogsActivity;
    protected $table = 'trs_uc_status_implementation';
    protected $guarded = ['id'];

    public function digitalInitiative()
    {
        return $this->belongsTo(DigitalInitiative::class, 'digital_initiative_id');
    }
}
