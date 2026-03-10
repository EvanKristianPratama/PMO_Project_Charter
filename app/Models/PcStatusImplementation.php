<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class PcStatusImplementation extends Model
{
    use LogsActivity;
    protected $table = 'trs_pc_status_implementation';
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(TrsProject::class, 'project_id');
    }
}
