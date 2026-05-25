<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstRole extends Model
{
    protected $table = 'mst_roles';

    protected $fillable = [
        'name',
        'description',
    ];

    public function responsibilities(): HasMany
    {
        return $this->hasMany(TrsResponsibility::class, 'role_id');
    }

    public function mappedResponsibles()
    {
        return $this->belongsToMany(MstResponsible::class, 'trs_responsible', 'role_id', 'responsible_id')
            ->withTimestamps();
    }

    public function practices()
    {
        return $this->belongsToMany(MstPractice::class, 'trs_practicerole', 'role_id', 'practice_id')
            ->withPivot('r_a')
            ->withTimestamps();
    }
}
