<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    use LogsActivity;

    protected $table = 'trs_milestones';

    protected $fillable = [
        'pc_id',
        'version',
        'title',
        'output',
        'start_date',
        'end_date',
        'type',
        'milestone_type',
        'order',
    ];

    public const ROADMAP_TYPE_BLOCK_PRIMARY = 1;

    public const ROADMAP_TYPE_DASHED_PRIMARY = 2;

    public const ROADMAP_TYPE_BLOCK_SECONDARY = 3;

    public const ROADMAP_TYPE_DASHED_SECONDARY = 4;

    public const ROADMAP_TYPE_BLOCK_TERTIARY = 5;

    public const ROADMAP_TYPE_DEFINITIONS = [
        self::ROADMAP_TYPE_BLOCK_PRIMARY => [
            'label' => '1',
            'timeline_style' => 'block',
        ],
        self::ROADMAP_TYPE_DASHED_PRIMARY => [
            'label' => '2',
            'timeline_style' => 'dashed',
        ],
        self::ROADMAP_TYPE_BLOCK_SECONDARY => [
            'label' => '3',
            'timeline_style' => 'block',
        ],
        self::ROADMAP_TYPE_DASHED_SECONDARY => [
            'label' => '4',
            'timeline_style' => 'dashed',
        ],
        self::ROADMAP_TYPE_BLOCK_TERTIARY => [
            'label' => '5',
            'timeline_style' => 'block',
        ],
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'milestone_type' => 'integer',
        ];
    }

    public function charter(): BelongsTo
    {
        return $this->belongsTo(TrsProjectCharter::class, 'pc_id');
    }

    public static function roadmapTypeCodes(): array
    {
        return array_keys(self::ROADMAP_TYPE_DEFINITIONS);
    }

    public static function roadmapTypeOptions(): array
    {
        return collect(self::ROADMAP_TYPE_DEFINITIONS)
            ->map(static fn (array $definition, int $code): array => [
                'value' => $code,
                'label' => $definition['label'],
                'timeline_style' => $definition['timeline_style'],
            ])
            ->values()
            ->all();
    }

    public static function normalizeRoadmapType(mixed $value): int
    {
        if ($value === null || $value === '') {
            return self::ROADMAP_TYPE_BLOCK_PRIMARY;
        }

        $normalized = (int) $value;

        if (! in_array($normalized, self::roadmapTypeCodes(), true)) {
            return self::ROADMAP_TYPE_BLOCK_PRIMARY;
        }

        return $normalized;
    }
}
