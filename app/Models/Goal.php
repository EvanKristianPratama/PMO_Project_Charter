<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'mst_goals';

    protected $fillable = ['code', 'title', 'description'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(TrsProject::class, 'trs_goal_project');
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'idGoal');
    }
}
