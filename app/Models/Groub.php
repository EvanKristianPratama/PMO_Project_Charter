<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groub extends Model
{
    protected $table = 'trs_groub';

    protected $fillable = ['company_id', 'name'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function organization()
    {
        return $this->hasMany(Organization::class, 'groub_id');
    }
}
