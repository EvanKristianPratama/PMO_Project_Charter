<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

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
