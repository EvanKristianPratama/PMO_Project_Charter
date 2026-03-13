<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coe extends Model
{
    use LogsActivity;

    protected $table = 'mst_coe';

    protected $fillable = [
        'name',
    ];

    public function useCases(): HasMany
    {
        return $this->hasMany(UseCase::class, 'coe_id');
    }
}
