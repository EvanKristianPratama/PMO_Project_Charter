<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseDigital extends Model
{
    protected $table = 'mst_phase_digital';

    protected $fillable = [
        'name',
    ];

    public function statuses(): HasMany
    {
        return $this->hasMany(StatusDigital::class, 'phase_id');
    }
}

