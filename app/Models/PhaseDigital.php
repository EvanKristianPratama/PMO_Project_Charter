<?php

namespace App\Models;

use App\Models\StatusDigital;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class PhaseDigital extends Model
{
    use LogsActivity;
    protected $table = 'mst_phase_digital';

    protected $fillable = [
        'name',
    ];

    public function statuses()
    {
        return $this->hasMany(StatusDigital::class, 'phase_id');
    }
}

