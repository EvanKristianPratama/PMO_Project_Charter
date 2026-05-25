<?php

namespace App\Models;

use App\Models\MstSop;
use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstRegulation extends Model
{
    protected $table = 'mst_regulation';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'judul',
        'nomor',
        'tipe',
        'stk',
        'owner',
        'revisi',
        'terbit',
        'berlaku',
        'pic_id',
    ];

    protected $casts = [
        'terbit' => 'date:Y-m-d',
        'berlaku' => 'date:Y-m-d',
    ];

    /**
     * Relasi ke TrsOrganization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'pic_id');
    }

    /**
     * Relasi ke MstSop
     */
    public function sops(): HasMany
    {
        return $this->hasMany(MstSop::class, 'regulation_id');
    }

    /**
     * Relasi ke MstGeneralPolicy
     */
    public function generalPolicies(): HasMany
    {
        return $this->hasMany(MstGeneralPolicy::class, 'regulation_id');
    }
}
