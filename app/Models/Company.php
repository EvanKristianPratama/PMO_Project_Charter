<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use LogsActivity;

    protected $table = 'mst_company';

    protected $fillable = ['name'];

    public function groups(): HasMany
    {
        return $this->hasMany(Groub::class, 'company_id');
    }
}
