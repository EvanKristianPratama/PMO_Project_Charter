<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UcStatusImplementation extends Model
{
    use LogsActivity;

    protected $table = 'trs_uc_status_implementation';

    protected $guarded = ['id'];

    public function digitalInitiative(): BelongsTo
    {
        return $this->belongsTo(TrsScInitiative::class, 'digital_initiative_id');
    }
}
