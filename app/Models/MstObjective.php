<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstObjective extends Model
{
    protected $table = 'mst_objective';
    protected $primaryKey = 'objective_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'objective_id',
        'objective',
        'objective_description',
        'objective_purpose',
    ];

    public function practices(): HasMany
    {
        return $this->hasMany(MstPractice::class, 'objective_id', 'objective_id');
    }
}
