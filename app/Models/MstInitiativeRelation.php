<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstInitiativeRelation extends Model
{
    use LogsActivity;

    protected $table = 'mst_initiative_relation';

    protected $fillable = [
        'model_relasi',
        'initiative_code_row',
        'initiative_code_column',
        'type_relation',
        'justifikasi',
    ];

    public function initiativeRow(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_code_row');
    }

    public function initiativeColumn(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_code_column');
    }
}
