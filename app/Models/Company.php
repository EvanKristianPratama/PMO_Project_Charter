<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'mst_company';

    protected $fillable = ['name'];

    public function groups()
    {
        return $this->hasMany(Groub::class, 'company_id');
    }
}
