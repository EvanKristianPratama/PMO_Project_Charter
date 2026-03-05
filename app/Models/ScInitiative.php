<?php

namespace App\Models;

use App\Models\DataSource;
use App\Models\DigitalDetail;
use App\Models\DigitalGroub;
use App\Models\DigitalHistory;
use App\Models\Organization;
use App\Models\StatusDigital;
use App\Models\Theme;
use App\Models\ScStatusImplementation;
use App\Models\UseCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScInitiative extends Model
{
    protected $table = 'trs_sc_initiative';

    protected $guarded = ['id'];

    public function scStatusImplementations(): HasMany
    {
        return $this->hasMany(ScStatusImplementation::class, 'sc_initiative_id')->orderBy('date', 'desc')->orderBy('time_start', 'desc');
    }

    public function latestScStatusImplementation(): HasOne
    {
        return $this->hasOne(ScStatusImplementation::class, 'sc_initiative_id')->latestOfMany('id');
    }

    public function planningOrImplementation()
    {
        return $this->belongsTo(TrsDigitalInitiative::class, 'ref_id');
    }

    public function useCase()
    {
        return $this->belongsTo(UseCase::class, 'useCase_id');
    }

    public function useCases()
    {
        return $this->belongsToMany(UseCase::class, 'trs_digital_initiative', 'id', 'useCase_id');
    }

    public function source()
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }

    public function histories()
    {
        return $this->hasMany(DigitalHistory::class, 'digital_id');
    }

    public function statuses()
    {
        return $this->belongsToMany(StatusDigital::class, 'trs_digital_history', 'digital_id', 'status_id')->withTimestamps();
    }

    public function digitalGroub()
    {
        return $this->hasMany(DigitalGroub::class, 'digital_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'trs_digital_groub', 'digital_id', 'organization_id')->withTimestamps();
    }

    public function rjpps()
    {
        return $this->belongsToMany(Theme::class, 'trs_rjpp', 'digital_id', 'theme_id')->withTimestamps();
    }

    public function digitalDetail()
    {
        return $this->hasMany(DigitalDetail::class, 'digital_id');
    }

    public function masterInitiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    public function scDetails(): HasMany
    {
        return $this->hasMany(ScDetail::class, 'digital_id');
    }
}
