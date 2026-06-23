<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstBod extends Model
{
    use LogsActivity;

    protected $table = 'mst_bod';

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'alias',
        'sumber',
        'pejabat',
        'tipe',
        'sk_id',
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

    public function skOrganization(): BelongsTo
    {
        return $this->belongsTo(MstSkOrganization::class, 'sk_id');
    }
}
