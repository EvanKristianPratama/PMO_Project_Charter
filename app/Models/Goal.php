<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use SoftDeletes;

    protected $table = 'mst_goals';

    protected $fillable = ['code', 'title', 'description'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'trs_goal_project');
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'idGoal');
    }
}
