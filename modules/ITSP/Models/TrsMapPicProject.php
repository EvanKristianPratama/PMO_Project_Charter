<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapPicProject extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_pic_project';

    protected $primaryKey = 'project_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'project_id' => 'integer',
        'project_sponsor' => 'integer',
        'project_owner' => 'integer',
        'project_leader' => 'integer',
    ];

    protected $fillable = [
        'project_id',
        'project_sponsor',
        'project_owner',
        'project_leader',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'project_id');
    }

    public function sponsorOrganization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'project_sponsor');
    }

    public function ownerOrganization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'project_owner');
    }

    public function leaderOrganization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'project_leader');
    }
}
