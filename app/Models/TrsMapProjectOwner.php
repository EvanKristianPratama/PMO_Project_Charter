<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapProjectOwner extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_project_owner';

    protected $fillable = [
        'pic_id',
        'pc_id'
    ];

    public function picProjectOwner(): BelongsTo
    {
        return $this->belongsTo(MstPic::class, 'pic_id');
    }
    public function projectCharter(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'priority_strategic_id');
    }

}
