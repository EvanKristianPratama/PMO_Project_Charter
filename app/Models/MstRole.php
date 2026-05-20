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
}
