<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapPriorityStrategicInitiative extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_pic_strategic';

    protected $fillable = [
        'pic_id',
        'priority_strategic_id'
    ];

    public function picPrioriyStrategic(): BelongsTo
    {
        return $this->belongsTo(MstPic::class, 'pic_id');
    }
    public function priorityStrategic(): BelongsTo
    {
        return $this->belongsTo(MstPriorityStrategicInitiative::class, 'priority_strategic_id');
    }

}
