<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    use LogsActivity;

    protected $table = 'trs_themes';

    protected $fillable = ['idGoal', 'theme_number', 'name'];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'idGoal');
    }

    public function rjppTaggings(): HasMany
    {
        return $this->hasMany(RjppTagging::class, 'theme_id');
    }

    public function digitalInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(TrsScInitiative::class, 'trs_rjpp', 'theme_id', 'sc_id')->withTimestamps();
    }

    public function initiativeTaggings(): HasMany
    {
        return $this->hasMany(InitiativeTagging::class, 'themes_id');
    }
    
    public function pillarThemes(): HasMany
    {
        return $this->hasMany(TrsPillarStrategy::class, 'themes_id');
    }
}
