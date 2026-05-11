<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsInitiativeSupport extends Model
{
    use LogsActivity;

    protected $table = 'trs_initiative_support';
    public $timestamps = false;

    protected $fillable = [
        'digital_id',
        'it_id',
        'notes',
    ];

    protected $casts = [
        'digital_id' => 'integer',
        'it_id' => 'integer',
    ];

    public function digitalInitiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'digital_id');
    }

    public function itInitiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'it_id');
    }
}
