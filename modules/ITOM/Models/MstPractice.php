<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstPractice extends Model
{
    protected $table = 'mst_practice';
    protected $primaryKey = 'practice_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'practice_id',
        'objective_id',
        'practice_name',
        'practice_description',
    ];

    public function objective(): BelongsTo
    {
        return $this->belongsTo(MstObjective::class, 'objective_id', 'objective_id');
    }

    public function roles()
    {
        return $this->belongsToMany(MstRole::class, 'trs_practicerole', 'practice_id', 'role_id')
            ->withPivot('r_a')
            ->withTimestamps();
    }

    public function itspInputs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrsItspInfoflowInput::class, 'practice_id', 'practice_id');
    }

    public function itspOutputs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrsItspInfoflowOutput::class, 'practice_id', 'practice_id');
    }
}
