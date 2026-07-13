<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsItspInfoflowOutput extends Model
{
    protected $table = 'trs_itsp_infoflow_outputs';

    protected $fillable = [
        'practice_id',
        'to',
        'description',
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(MstPractice::class, 'practice_id', 'practice_id');
    }
}
