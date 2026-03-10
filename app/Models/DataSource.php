<?php

namespace App\Models;

use App\Models\TrsDigitalInitiative;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class DataSource extends Model
{
    use LogsActivity;
    protected $table = 'mst_data_source';

    protected $fillable = [
        'name',
        'month',
        'year',
    ];

    public function DigitalInitiatives()
    {
        return $this->hasMany(TrsDigitalInitiative::class, 'source_id');
    }
}
