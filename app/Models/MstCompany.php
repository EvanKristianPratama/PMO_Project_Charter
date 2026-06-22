<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MstCompany extends Model
{
    use LogsActivity;

    protected $table = 'mst_company';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'mst_companycol',
        'parent_id',
        'name',
        'organization',
        'singkatan',
    ];

    /**
     * Parent company (self-referencing).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'parent_id');
    }

    /**
     * Child companies (self-referencing).
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstCompany::class, 'parent_id');
    }

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
