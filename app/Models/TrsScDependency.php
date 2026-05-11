<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsScDependency extends Model
{
    protected $table = 'trs_sc_dependency';

    // Composite primary key – no auto-increment id
    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'compendium_id',
        'appendix_id',
    ];

    /**
     * The compendium (parent TrsScInitiative) this dependency belongs to.
     */
    public function compendium(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'compendium_id');
    }

    /**
     * The appendix (child TrsScInitiative) this dependency belongs to.
     */
    public function appendix(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'appendix_id');
    }
}
