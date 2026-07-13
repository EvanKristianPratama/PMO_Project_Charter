<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalHistory extends Model
{
    use LogsActivity;

    protected $table = 'trs_digital_history';

    public $incrementing = false;

    protected $fillable = [
        'digital_id',
        'status_id',
    ];

    public $timestamps = true;

    public function digitalInitiative(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'digital_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusDigital::class, 'status_id');
    }
}
