<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class TrsPcStatusImplementation extends Model
{
    use LogsActivity;

    protected $table = 'trs_pc_status_implementation';

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(TrsProject::class, 'project_id');
    }
}
