<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class RjppTagging extends Model
{
    use LogsActivity;

    protected $table = 'trs_rjpp';

    public $incrementing = false;

    protected $fillable = [
        'sc_id',
        'theme_id',
    ];

    public $timestamps = true;

    public function digitalInitiative()
    {
        return $this->belongsTo(TrsScInitiative::class, 'sc_id');
    }

    public function rjpp()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }
}
