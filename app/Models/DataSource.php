<?php

namespace App\Models;

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

    public function scInitiatives()
    {
        return $this->hasMany(TrsScInitiative::class, 'source_id');
    }
}
