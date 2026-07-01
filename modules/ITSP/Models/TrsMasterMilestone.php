<?php

namespace Modules\ITSP\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrsMasterMilestone extends Model
{
    protected $table = 'trs_master_milestone';

    protected $fillable = [
        'initiative_id',
        'startYear',
        'startQ',
        'endYear',
        'endQ',
        'acitvity',
        'activity',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'initiative_id' => 'integer',
            'startYear' => 'integer',
            'endYear' => 'integer',
        ];
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(MstInitiative::class, 'initiative_id');
    }

    protected function activity(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => trim((string) ($attributes['acitvity'] ?? '')),
            set: fn (mixed $value): array => ['acitvity' => trim((string) $value)],
        );
    }
}
