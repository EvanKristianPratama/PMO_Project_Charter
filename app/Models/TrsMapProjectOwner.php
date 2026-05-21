<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMapProjectOwner extends Model
{
    use LogsActivity;

    protected $table = 'trs_map_project_owner';

    public $timestamps = false;

    protected $fillable = [
        'pic_id',
        'pc_id'
    ];

    public function organizationOwner(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'pic_id');
    }
    public function projectCharter(): BelongsTo
    {
        return $this->belongsTo(TrsProject::class, 'priority_strategic_id');
    }

}
