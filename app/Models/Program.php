<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $table = 'mst_programs';

    protected $fillable = ['code', 'name', 'description'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'trs_program_project');
    }
}
