<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstPriorityStrategicInitiative extends Model
{
    use LogsActivity;

    protected $table = 'mst_priority_strategic_initiative';

    protected $fillable = [
        'priority',
        'no',
        'initiative',
        'deskripsi',
        'it_initiatives',
    ];

    public function mapPriorityStrategicInitiative(): HasMany
    {
        return $this->hasMany(TrsMapPriorityStrategicInitiative::class, 'priority_strategic_id');
    }
    

}
