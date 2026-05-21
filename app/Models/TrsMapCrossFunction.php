<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapCrossFunction extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_cross_function';

    public $timestamps = false;

    protected $fillable = [
        'pic_id',
        'pc_id',
    ];

    public function picCrossFunction(): BelongsTo
    {
        return $this->belongsTo(MstPicProject::class, 'pic_id');
    }
    public function projectCharter(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'priority_strategic_id');
    }

}
