<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MstPic extends Model
{
    use LogsActivity;
    protected $table = 'mst_pic';

    protected $fillable = [
        'name',
    ];

    public function mapPicPriorityStrategic (): HasMany
    {
        return $this->hasMany(TrsMapPriorityStrategicInitiative::class, 'pic_id');
    }
    
}
