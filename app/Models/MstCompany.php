<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MstCompany extends Model
{
    use LogsActivity;

    protected $table = 'mst_company';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'name',
        'organization',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Groub::class, 'company_id');
    }

    public function bods(): HasMany
    {
        return $this->hasMany(MstBod::class, 'company_id');
    }

    public function organizations(): HasManyThrough
    {
        return $this->hasManyThrough(
            TrsOrganization::class,
            Groub::class,
            'company_id',
            'groub_id',
            'id',
            'id'
        );
    }
}
