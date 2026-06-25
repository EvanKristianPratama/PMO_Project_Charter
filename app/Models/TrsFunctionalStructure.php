<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrsFunctionalStructure extends Model
{
    use LogsActivity;

    protected $table = 'trs_functional_structure';

    protected $fillable = [
        'functional_id',
        'name',
        'parent_id',
    ];

    // ─── Self-Referencing Relations ───────────────────────────────────────────

    /**
     * Parent structure (parent_id → id pada tabel yang sama).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TrsFunctionalStructure::class, 'parent_id');
    }

    /**
     * Child structures (recursive children).
     */
    public function children(): HasMany
    {
        return $this->hasMany(TrsFunctionalStructure::class, 'parent_id');
    }

    /**
     * All descendants (recursive children of children).
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(TrsFunctionalStructure::class, 'parent_id')
                    ->with('allChildren');
    }

    // ─── Other Relations ─────────────────────────────────────────────────────

    public function function(): BelongsTo
    {
        return $this->belongsTo(MstFunction::class, 'functional_id');
    }

    public function functionalOrganization(): BelongsTo
    {
        return $this->belongsTo(MstFunctionalOrganization::class, 'functional_id');
    }

    public function trsFunctionalOrganizations(): HasMany
    {
        return $this->hasMany(TrsFunctionalOrganization::class, 'structure_id');
    }
}
