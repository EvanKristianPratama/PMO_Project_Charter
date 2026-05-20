<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapProjectLeader extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_project_leader';

    protected $fillable = [
        'pc_id',
        'pic_id'
    ];

    public function picProjectLeader(): BelongsTo
    {
        return $this->belongsTo(MstPicCompany::class, 'pic_id');
    }
    public function projectCharter(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'priority_strategic_id');
    }

}
