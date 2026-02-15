<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'trs_themes';

    protected $fillable = ['idGoal', 'theme_number', 'name'];

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'idGoal');
    }
}
