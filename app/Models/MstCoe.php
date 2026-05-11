<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function mapTechnologies(): HasMany
    {
        return $this->hasMany(TrsMapTechnology::class, 'coe_id');
    }
}
