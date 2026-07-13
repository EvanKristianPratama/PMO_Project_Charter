<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ITOM\Models\MstCompany;

class Groub extends Model
{
    use LogsActivity;

    protected $table = 'trs_groub';

    protected $fillable = ['company_id', 'name'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MstCompany::class, 'company_id');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(TrsOrganization::class, 'groub_id');
    }

    public function organization(): HasMany
    {
        return $this->organizations();
    }
}
