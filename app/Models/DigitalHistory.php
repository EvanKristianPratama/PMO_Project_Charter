<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class DigitalHistory extends Model
{
    use LogsActivity;

    protected $table = 'trs_digital_history';

    public $incrementing = false;

    protected $fillable = [
        'digital_id',
        'status_id',
    ];

    public $timestamps = true;

    public function digitalInitiative()
    {
        return $this->belongsTo(TrsDigitalInitiative::class, 'digital_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusDigital::class, 'status_id');
    }
}
