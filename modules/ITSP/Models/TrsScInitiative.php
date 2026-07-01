<?php

namespace Modules\ITSP\Models;

use Modules\ITSP\Models\DataSource;
use App\Models\MstInitiative;
use Modules\ITSP\Models\MstScSource;
use Modules\ITSP\Models\Theme;
use Modules\ITSP\Models\TrsMapSc;
use Modules\ITSP\Models\TrsScDetails;
use Modules\ITSP\Models\UseCase;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrsScInitiative extends Model
{
    use LogsActivity;

    protected $table = 'trs_sc_initiative';

    protected $guarded = ['id'];

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

    public function appendixes(): BelongsToMany
    {
        return $this->belongsToMany(
            TrsScInitiative::class,
            'trs_sc_dependency',
            'compendium_id',
            'appendix_id'
        );
    }

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
