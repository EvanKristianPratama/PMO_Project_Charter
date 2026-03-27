<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsMasterMilestone extends Model
{
    protected $table = 'trs_master_milestone';

    protected $fillable = [
        'initiative_id',
        'initiative_name',
        'organization_name',
        'startYear',
        'startQ',
        'endYear',
        'endQ',
        'activity',
        'version',
    ];
}
