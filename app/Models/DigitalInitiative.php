<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalInitiative extends Model
{
    use SoftDeletes;

    protected $table = 'mst_digitalInitiative';

    protected $fillable = [
        'type',
        'no',
        'projectOwner',
        'useCase',
        'desc',
        'value',
        'urgency',
        'rjjp',
        'coe',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function statusRef(): BelongsTo
    {
        return $this->belongsTo(InitiativeStatus::class, 'status');
    }
}
