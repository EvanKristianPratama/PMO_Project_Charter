<?php

namespace App\Models;

use App\Models\MstSop;
use App\Models\TrsOrganization;
use App\Models\TrsTkoContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstRegulation extends Model
{
    protected $table = 'mst_regulation';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'pic_id',
        'master_id',
        'company_id',
        'judul',
        'nomor',
        'tipe',
        'stk',
        'owner',
        'revisi',
        'terbit',
        'berlaku',
        'status',
    ];

    protected $casts = [
        'terbit' => 'date:Y-m-d',
        'berlaku' => 'date:Y-m-d',
        'parent_id' => 'integer',
        'master_id' => 'integer',
        'company_id' => 'integer',
    ];

    /**
     * Relasi ke parent regulation (self relation)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'parent_id');
    }

    /**
     * Relasi ke children regulation (self relation)
     */
    public function children(): HasMany
    {
        return $this->hasMany(MstRegulation::class, 'parent_id');
    }

    /**
     * Relasi ke children regulation secara rekursif
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Relasi ke TrsOrganization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'pic_id');
    }

    /**
     * Relasi ke TrsOrganization (Master)
     */
    public function master(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'master_id');
    }

    /**
     * Relasi ke MstBod (Refinement)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MstBod::class, 'company_id');
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

    /**
     * Regulations revoked by this regulation (Many-to-Many via trs_record_regulation)
     */
    public function revokedRegulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_record_regulation', 'revocation', 'revoked')
            ->withTimestamps();
    }

    /**
     * Regulations that revoke this regulation (Many-to-Many via trs_record_regulation)
     */
    public function revocationRegulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_record_regulation', 'revoked', 'revocation')
            ->withTimestamps();
    }

    /**
     * Dokumen terkait — regulations linked to this one (Many-to-Many via trs_related_regulation)
     */
    public function relatedRegulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_related_regulation', 'regulation', 'related')
            ->withTimestamps();
    }

    /**
     * Regulations that link to this one as related (inverse)
     */
    public function relatedByRegulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_related_regulation', 'related', 'regulation')
            ->withTimestamps();
    }

    /**
     * Relasi ke MstFunction.
     */
    public function functions(): BelongsToMany
    {
        return $this->belongsToMany(MstFunction::class, 'trs_map_func_regulation', 'regulation_id', 'function_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke MstProsesBisnis via trs_probis_regulation.
     */
    public function prosesBisnis(): BelongsToMany
    {
        return $this->belongsToMany(MstProsesBisnis::class, 'trs_probis_regulation', 'regulation_id', 'probis_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke TrsProbisRegulation.
     */
    public function probisRegulations(): HasMany
    {
        return $this->hasMany(TrsProbisRegulation::class, 'regulation_id');
    }
}
