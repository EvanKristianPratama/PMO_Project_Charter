<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrsOrganization extends Model
{
    use LogsActivity;

    protected $table = 'trs_organization';

    protected $fillable = [
        'groub_id',
        'name',
    ];

    public function groub(): BelongsTo
    {
        return $this->belongsTo(Groub::class, 'groub_id');
    }

    public function initiatives(): HasMany
    {
        return $this->hasMany(MstInitiative::class, 'business_unit');
    }

    public function collaborationInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(
            MstInitiative::class,
            'trs_bu_collaboration',
            'organization_id',
            'initiative_id',
        )->using(TrsBuCollaboration::class)
            ->withTimestamps();
    }
}
