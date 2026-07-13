<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsReviewSc extends Model
{
    use LogsActivity;
    protected $table = 'trs_review_sc';

    protected $fillable = [
        'initiative_id',
        'month',
        'year',
        'notes',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
