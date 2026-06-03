<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstItSteeringComittee extends Model
{
    protected $table = 'mst_it_steering_comitte';

    protected $fillable = [
        'organization_id',
        'code'
    ];

    public function organization()
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }
}
