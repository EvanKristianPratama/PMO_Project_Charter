<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsItspInfoflowInput extends Model
{
    protected $table = 'trs_itsp_infoflow_inputs';

    protected $fillable = [
        'practice_id',
        'from',
        'description',
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(MstPractice::class, 'practice_id', 'practice_id');
    }
}
