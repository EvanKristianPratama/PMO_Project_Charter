<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsReviewPC extends Model
{
    use LogsActivity;

    protected $table = 'trs_review_pc';

    protected $fillable = [
        'initiative_id',
        'month',
        'year',
        'kesimpulan',
        'detail_kesimpulan',
        'detail_penjelasan',
        'penjelasan',
        'why',
        'what',
        'how',
        'project_profile',
        'key_milestone',
        'risk_impact',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }
}
