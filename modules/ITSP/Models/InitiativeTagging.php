<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InitiativeTagging extends Model
{
    use LogsActivity;

    protected $table = 'trs_initiative_tagging';

    protected $fillable = [
        'initiative_id',
        'goal',
        'pilar',
        'themes_id',
    ];

    /**
     * The initiative this tagging belongs to.
     */
    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    /**
     * The theme (strategic pillar) this tagging maps to.
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'themes_id');
    }
}
