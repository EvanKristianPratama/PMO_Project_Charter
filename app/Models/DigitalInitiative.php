<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalInitiative extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'mst_digitalInitiative';

    protected $fillable = [
        'type',
        'tipe_inisiative',
        'no',
        'projectOwner',
        'useCase',
        'desc',
        'value',
        'urgency',
        'rjjp',
        'coe',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function statusRef(): BelongsTo
    {
        return $this->belongsTo(InitiativeStatus::class, 'status');
    }

    public function ucStatusImplementations()
    {
        return $this->hasMany(UcStatusImplementation::class, 'digital_initiative_id')->orderBy('date', 'desc')->orderBy('time_start', 'desc');
    }

    public function scStatusImplementations()
    {
        return $this->hasMany(ScStatusImplementation::class, 'digital_initiative_id')->orderBy('date', 'desc')->orderBy('time_start', 'desc');
    }

    public function latestScStatusImplementation()
    {
        return $this->hasOne(ScStatusImplementation::class, 'digital_initiative_id')->latestOfMany('id');
    }
}
