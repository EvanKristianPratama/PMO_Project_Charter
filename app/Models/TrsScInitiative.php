<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrsScInitiative extends Model
{
    use LogsActivity;
    protected $table = 'trs_sc_initiative';

    protected $guarded = ['id'];

    public function scStatusImplementations(): HasMany
    {
        return $this->hasMany(ScStatusImplementation::class, 'digital_initiative_id')->orderBy('date', 'desc')->orderBy('time_start', 'desc');
    }

    public function latestScStatusImplementation(): HasOne
    {
        return $this->hasOne(ScStatusImplementation::class, 'digital_initiative_id')->latestOfMany('id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }

    public function useCase(): BelongsTo
    {
        return $this->belongsTo(UseCase::class, 'useCase_id');
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
