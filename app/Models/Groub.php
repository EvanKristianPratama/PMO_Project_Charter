<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Groub extends Model
{
    use LogsActivity;
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
