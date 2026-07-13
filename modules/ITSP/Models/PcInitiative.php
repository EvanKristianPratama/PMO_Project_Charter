<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcInitiative extends Model
{
    use LogsActivity;

    protected $table = 'trs_pc_initiative';

    protected $fillable = [
        'pc_id',
        'initiative_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'pc_id');
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
