<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrsScInitiative extends Model
{
    use LogsActivity;
    protected $table = 'trs_sc_initiative';

    protected $guarded = ['id'];

    public function scStatusImplementations(): HasMany
    {
        return $this->hasMany(ScStatusImplementation::class, 'sc_initiative_id')->orderBy('date', 'desc')->orderBy('time_start', 'desc');
    }

    public function rjpps(): BelongsToMany
    {
        return $this->belongsToMany(Theme::class, 'trs_rjpp', 'digital_id', 'theme_id')->withTimestamps();
    }

    public function digitalDetail(): HasMany
    {
        return $this->hasMany(TrsScDetails::class, 'sc_id');
    }

    public function mstInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(MstInitiative::class, 'trs_map_sc', 'sc_id', 'initiative_id');
    }

    public function mapSc(): HasMany
    {
        return $this->hasMany(TrsMapSc::class, 'sc_id');
    }

    public function scDetails(): HasMany
    {
        return $this->hasMany(TrsScDetails::class, 'sc_id');
    }

    public function sourceData(): BelongsTo
    {
        return $this->belongsTo(MstScSource::class, 'source_id');
    }
}
