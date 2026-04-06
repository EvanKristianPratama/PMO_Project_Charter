<?php

namespace App\Models;

use App\Models\DataSource;
use App\Models\MstInitiative;
use App\Models\MstScSource;
use App\Models\ScStatusImplementation;
use App\Models\Theme;
use App\Models\TrsMapSc;
use App\Models\TrsScDetails;
use App\Models\UseCase;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrsScInitiative extends Model
{
    use LogsActivity;

    protected $table = 'trs_sc_initiative';

    protected $guarded = ['id'];

    public function scStatusImplementations(): HasMany
    {
        return tap(
            $this->hasMany(ScStatusImplementation::class, 'digital_initiative_id'),
            fn ($q) => $q->orderBy('date', 'desc')->orderBy('id', 'desc')
        );
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
        return $this->belongsToMany(Theme::class, 'trs_rjpp', 'sc_id', 'theme_id')->withTimestamps();
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

    /**
     * Appendix initiatives linked to this compendium via trs_sc_dependency.
     */
    public function appendixes(): BelongsToMany
    {
        return $this->belongsToMany(
            TrsScInitiative::class,
            'trs_sc_dependency',
            'compendium_id',
            'appendix_id'
        );
    }

    /**
     * Compendium initiatives this initiative is an appendix of.
     */
    public function compendiums(): BelongsToMany
    {
        return $this->belongsToMany(
            TrsScInitiative::class,
            'trs_sc_dependency',
            'appendix_id',
            'compendium_id'
        );
    }

    public function sourceData(): BelongsTo
    {
        return $this->belongsTo(MstScSource::class, 'source_id');
    }
}
