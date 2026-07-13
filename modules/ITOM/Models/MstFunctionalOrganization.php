<?php

namespace Modules\ITOM\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MstFunctionalOrganization extends Model
{
    use LogsActivity;

    protected $table = 'mst_functional_organization';

    protected $fillable = [
        'company_id',
        'regulation_id',
        'name',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    public function trsFunctionalStructure(): HasOne
    {
        return $this->hasOne(TrsFunctionalStructure::class, 'functional_id', 'id');
    }

    public function trsFunctionalStructures(): HasMany
    {
        return $this->hasMany(TrsFunctionalStructure::class, 'functional_id', 'id');
    }

    public function trsFunctionalOrganizations(): HasManyThrough
    {
        return $this->hasManyThrough(
            TrsFunctionalOrganization::class,
            TrsFunctionalStructure::class,
            'functional_id',
            'structure_id',
            'id',
            'id'
        );
    }

    public function trsFunctionalFunctions(): HasMany
    {
        return $this->hasMany(TrsFunctionalFunction::class, 'functional_id');
    }
}
