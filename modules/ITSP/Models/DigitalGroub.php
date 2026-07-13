<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalGroub extends Model
{
    use LogsActivity;

    protected $table = 'trs_digital_groub';

    public $incrementing = false;

    protected $fillable = [
        'digital_id',
        'organization_id',
    ];

    public $timestamps = true;

    public function digitalInitiative(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'digital_id');
    }

    public function organizations()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
