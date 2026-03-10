<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\RjppTagging;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'trs_themes';

    protected $fillable = ['idGoal', 'theme_number', 'name'];

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'idGoal');
    }

    public function rjppTaggings()
    {
        return $this->hasMany(RjppTagging::class, 'theme_id');
    }

    public function digitalInitiatives()
    {
        return $this->belongsToMany(TrsDigitalInitiative::class, 'trs_rjpp', 'theme_id', 'digital_id')->withTimestamps();
    }

    public function initiativeTaggings()
    {
        return $this->hasMany(InitiativeTagging::class, 'themes_id');
    }
}
