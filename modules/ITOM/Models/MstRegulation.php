<?php

namespace Modules\ITOM\Models;

use Modules\ITOM\Models\MstSop;
use Modules\ITOM\Models\TrsTkoContent;
use Modules\ITOM\Models\MstDefinition;
use Modules\ITOM\Models\TrsDefinitionRegulation;
use Modules\ITOM\Models\TrsDocumentRegulation;
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
        'company_id',
        'owner_id',
        'judul',
        'nomor',
        'tipe',
        'stk',
        'owner',
        'revisi',
        'terbit',
        'berlaku',
        'status',
        'source'
    ];

    protected $casts = [
        'terbit' => 'date:Y-m-d',
        'berlaku' => 'date:Y-m-d',
        'parent_id' => 'integer',
        'company_id' => 'integer',
        'owner_id' => 'integer',
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
     * Relasi ke MstCompany
     */
    public function mstCompany(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    /**
     * Relasi ke MstBod (Refinement)
     */
    public function mstBod(): BelongsTo
    {
        return $this->belongsTo(MstBod::class, 'owner_id');
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
     * Relasi ke MstBod
     */
    public function bods(): HasMany
    {
        return $this->hasMany(MstBod::class, 'regulation_id');
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
     * Dokumen terkait ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â regulations linked to this one (Many-to-Many via trs_related_regulation)
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

    /**
     * Relasi ke MstDefinition via trs_definition_regulation.
     */
    public function definitions(): BelongsToMany
    {
        return $this->belongsToMany(MstDefinition::class, 'trs_definition_regulation', 'regulation_id', 'definition_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke TrsDefinitionRegulation.
     */
    public function definitionRegulations(): HasMany
    {
        return $this->hasMany(TrsDefinitionRegulation::class, 'regulation_id');
    }

    /**
     * Relasi ke TrsDocumentRegulation.
     */
    public function documentRegulations(): HasMany
    {
        return $this->hasMany(TrsDocumentRegulation::class, 'regulation_id');
    }

    /**
     * Relasi many-to-many ke MstDocument via trs_document_regulation.
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(MstDocument::class, 'trs_document_regulation', 'regulation_id', 'document_id')
            ->withTimestamps();
    }
}
