<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class TrsMapTechnology extends Model
{
    use LogsActivity;
    protected $table = 'trs_map_technology';
    protected $fillable = [
        'coed_id',
        'initiative_id',
    ];

    public function coe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'coed_id');
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
