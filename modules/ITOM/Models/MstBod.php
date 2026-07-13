<?php

namespace Modules\ITOM\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstBod extends Model
{
    use LogsActivity;

    protected $table = 'mst_bod';

    protected $fillable = [
        'company_id',
        'parent_id',
        'regulation_id',
        'name',
        'nama_jabatan',
        'alias',
        'sumber',
        'pejabat',
        'role_function',
        'grup_function',
        'tipe',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstBod::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MstBod::class, 'parent_id');
    }

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    public function trsFunctionalOrganizations(): HasMany
    {
        return $this->hasMany(TrsFunctionalOrganization::class, 'organization_id');
    }

    /**
     * Relasi ke MstFunction melalui trs_function_organization.
     */
    public function functions(): BelongsToMany
    {
        return $this->belongsToMany(MstFunction::class, 'trs_function_organization', 'organization_id', 'function_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke TrsFunctionOrganization.
     */
    public function trsFunctionOrganizations(): HasMany
    {
        return $this->hasMany(TrsFunctionOrganization::class, 'organization_id');
    }
}
