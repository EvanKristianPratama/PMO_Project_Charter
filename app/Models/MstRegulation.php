<?php

namespace App\Models;

use App\Models\MstSop;
use App\Models\TrsOrganization;
use App\Models\TrsTkoContent;
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
        'pic_id',
        'judul',
        'nomor',
        'tipe',
        'stk',
        'owner',
        'revisi',
        'terbit',
        'berlaku',
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
     * Relasi ke MstSop via TrsSopCategory
     */
    public function sops()
    {
        return $this->hasManyThrough(
            MstSop::class,
            TrsSopCategory::class,
            'regulation_id', // Foreign key on TrsSopCategory table
            'category_id',   // Foreign key on MstSop table
            'id',            // Local key on MstRegulation table
            'id'             // Local key on TrsSopCategory table
        );
    }

    public function sopCategories(): HasMany
    {
        return $this->hasMany(TrsSopCategory::class, 'regulation_id');
    }

    /**
     * Relasi ke MstGeneralPolicy
     */
    public function generalPolicies(): HasMany
    {
        return $this->hasMany(MstGeneralPolicy::class, 'regulation_id');
    }

    /**
     * Relasi ke MstObjective
     */
    public function objectives(): HasMany
    {
        return $this->hasMany(MstObjective::class, 'regulation_id');
    }

    /**
     * Relasi ke TrsTkoContent
     */
    public function tkoContents(): HasMany
    {
        return $this->hasMany(TrsTkoContent::class, 'regulation_id');
    }
}
