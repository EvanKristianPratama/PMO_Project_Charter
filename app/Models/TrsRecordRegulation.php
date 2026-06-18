<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsRecordRegulation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trs_record_regulation';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key associated with the table.
     *
     * @var string|null
     */
    protected $primaryKey = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'revocation',
        'revoked',
    ];

    /**
     * Relation to the regulation document doing the revocation.
     */
    public function revocationRegulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'revocation');
    }

    /**
     * Relation to the regulation document that is revoked.
     */
    public function revokedRegulation(): BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'revoked');
    }
}
