<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstProgram extends Model
{
    use SoftDeletes;

    protected $table = 'mst_programs';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(TrsProject::class, 'trs_program_project', 'program_id', 'project_id');
    }
}