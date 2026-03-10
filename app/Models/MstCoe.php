<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\LogsActivity;

class MstCoe extends Model
{
    use LogsActivity;
    protected $table = 'mst_coe';

    protected $fillable = [
        'name',
    ];

    public function initiatives(): HasMany
    {
        return $this->hasMany(MstInitiative::class, 'coe_id');
    }
}
