<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsBusinessStrategy extends Model
{
    protected $table = 'trs_business_strategy';

    protected $fillable = [
        'business_unit',
        'maximazing_value',
        'expand',
        'low_carbon'
    ];

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'business_unit');
    }
}
