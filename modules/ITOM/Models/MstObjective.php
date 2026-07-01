<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MstObjective extends Model
{
    protected $table = 'mst_objective';
    protected $primaryKey = 'objective_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'objective_id',
        'regulation_id',
        'domain',
        'objective',
        'objective_description',
        'objective_purpose',
    ];

    /**
     * Relasi ke MstRegulation
     */
    public function regulation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    public function practices(): HasMany
    {
        return $this->hasMany(MstPractice::class, 'objective_id', 'objective_id');
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(
            MstResponsible::class,
            'trs_responsible_objective',
            'objective_id',
            'responsible_id'
        )->withTimestamps();
    }

    public function cobitMappings(): HasMany
    {
        return $this->hasMany(TrsMapingKebijakanCobit::class, 'objective_id', 'objective_id');
    }
}
