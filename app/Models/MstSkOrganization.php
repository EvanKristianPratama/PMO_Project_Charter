<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstSkOrganization extends Model
{
    use LogsActivity;

    protected $table = 'mst_sk_organization';

    protected $fillable = [
        'sk',
        'deskripsi',
    ];

    public function functionalOrganizations(): HasMany
    {
        return $this->hasMany(MstFunctionalOrganization::class, 'sk_id');
    }
}
