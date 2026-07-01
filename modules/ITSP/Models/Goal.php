<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'mst_goals';

    protected $fillable = ['code', 'title', 'pilar'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(TrsProject::class, 'trs_goal_project');
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'idGoal');
    }

    public function strategicHouse(): BelongsTo
    {
        return $this->belongsTo(MstStrategicHouse::class, 'pillar');
    }
}
