<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MstPicProject extends Model
{
    use LogsActivity;
    protected $table = 'mst_pic_project';

    protected $casts = [
        'organization_id' => 'integer',
    ];

    protected $fillable = [
        'organization_id',
        'name',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'organization_id');
    }
}
