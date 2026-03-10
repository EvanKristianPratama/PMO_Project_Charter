<?php

namespace App\Models;

use App\Models\Organization;
use App\Models\TrsDigitalInitiative;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class DigitalGroub extends Model
{
    use LogsActivity;
    protected $table = 'trs_digital_groub';

    public $incrementing = false;

    protected $fillable = [
        'digital_id', 
        'organization_id'
    ];

    public $timestamps = true;

    public function digitalInitiative()
    {
        return $this->belongsTo(TrsDigitalInitiative::class, 'digital_id');
    }

    public function organizations()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
