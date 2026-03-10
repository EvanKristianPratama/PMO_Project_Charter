<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    protected $table = 'mst_company';

    protected $fillable = ['name'];

    public function groups()
    {
        return $this->hasMany(Groub::class, 'company_id');
    }
}
