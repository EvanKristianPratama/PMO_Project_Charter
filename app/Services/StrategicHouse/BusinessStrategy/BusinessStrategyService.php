<?php

namespace App\Services\StrategicHouse\BusinessStrategy;

use App\Models\Goal;
use App\Models\Theme;
use App\Models\MstInitiative;
use App\Models\TrsOrganization;
use App\Models\TrsBusinessStrategy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BusinessStrategyService
{
    private const FIXED_BUSINESS_UNITS = [
        ['id' => 13, 'label' => 'Upstream', 'logo' => '/icon/Upstream.png', 'order' => 1],
        ['id' => 16, 'label' => 'Gas', 'logo' => '/icon/Gas.png', 'order' => 2],
        ['id' => 14, 'label' => 'R&P', 'logo' => '/icon/R&P.png', 'order' => 3],
        ['id' => 17, 'label' => 'C&T', 'logo' => '/icon/C&T.png', 'order' => 4],
        ['id' => 18, 'label' => 'IML', 'logo' => '/icon/IML.png', 'order' => 5],
        ['id' => 15, 'label' => 'PNRE', 'logo' => '/icon/PNRE.png', 'order' => 6],
        ['id' => 21, 'label' => 'APFS', 'logo' => '/icon/APFS.png', 'order' => 7],
    ];

    private const STRATEGY_GOAL_CONFIG = [
        'legacy' => [
            'goal_id' => 8,
            'default_title' => 'Maximize Legacy Business',
        ],
        'low_carbon' => [
            'goal_id' => 9,
            'default_title' => 'Build Low Carbon Business',
        ],
    ];

    private const STRATEGY_COLUMNS = [
        [
            'key' => 'maximazing_value',
            'theme_id' => 34,
            'default_label' => 'Maximizing Value',
            'description' => 'Fokus penguatan value dari bisnis inti yang sudah berjalan.',
            'tone' => 'sky',
            'group' => 'legacy',
        ],
        [
            'key' => 'expand',
            'theme_id' => 38,
            'default_label' => 'Expand To New Markets & Adjacencies',
            'description' => 'Peluang ekspansi pasar dan pengembangan adjacency baru.',
            'tone' => 'amber',
            'group' => 'legacy',
        ],
        [
            'key' => 'low_carbon',
            'goal_id' => 9,
            'default_label' => 'Build Low Carbon Business',
            'default_goal_code' => 'B',
            'description' => 'Arah strategi untuk pertumbuhan bisnis rendah karbon.',
            'tone' => 'emerald',
            'group' => 'low_carbon',
            'direct_goal_tagging' => true,
        ],
    ];

    private const ENABLER_CONFIG = [
        [
            'key' => 'digital_enabler',
            'goal_id' => 11,
            'default_title' => 'Digital Enabler',
            'default_goal_code' => 'C',
            'tone' => 'indigo',
        ],
        [
            'key' => 'sustainability_enabler',
            'goal_id' => 12,
            'default_title' => 'Sustainability Enabler',
            'default_goal_code' => 'D',
            'tone' => 'emerald',
        ],
    ];

    public function getPageProps(): array
    {
        $strategyMetadata = $this->getStrategyMetadata();
        $initiativesByBusinessUnit = $this->getInitiativesByBusinessUnit();
        $fixedBusinessUnits = collect(self::FIXED_BUSINESS_UNITS);
        $fixedBusinessUnitIds = $fixedBusinessUnits->pluck('id')->all();
        $organizationMeta = TrsOrganization::query()
            ->select(['id', 'name', 'groub_id'])
            ->whereIn('id', $fixedBusinessUnitIds)
            ->get()
            ->keyBy('id');

        $rows = TrsBusinessStrategy::query()
            ->select([
                'id',
                'business_unit',
                'maximazing_value',
                'expand',
                'low_carbon',
                'updated_at',
            ])
            ->with([
                'businessUnit' => fn ($query) => $query->select(['id', 'name', 'groub_id']),
            ])
            ->whereIn('business_unit', $fixedBusinessUnitIds)
            ->get()
            ->sortBy(function (TrsBusinessStrategy $strategy) use ($fixedBusinessUnits): int {
                return (int) ($fixedBusinessUnits
                    ->firstWhere('id', (int) ($strategy->business_unit ?? 0))['order'] ?? PHP_INT_MAX);
            })
            ->map(fn (TrsBusinessStrategy $strategy): array => $this->mapRow(
                $strategy,
                $initiativesByBusinessUnit,
                $fixedBusinessUnits->firstWhere('id', (int) ($strategy->business_unit ?? 0)) ?? [],
                $organizationMeta->get((int) ($strategy->business_unit ?? 0)),
                $strategyMetadata['strategy_columns'],
                $strategyMetadata['enabler_goals']
            ))
            ->values();

        return [
            'page' => [
                'title' => 'Business Strategy',
                'headline' => 'Business Strategy Matrix',
                'description' => 'Pemetaan arah strategi bisnis per business unit dalam tiga fokus utama dual growth.',
            ],
            'summary' => $this->buildSummary($rows, $strategyMetadata['strategy_columns']),
            'headerGoals' => $strategyMetadata['header_goals'],
            'enablerGoals' => $strategyMetadata['enabler_goals'],
            'strategyColumns' => $this->getStrategyColumns($strategyMetadata['strategy_columns']),
            'groups' => $this->buildGroups($rows)->all(),
            'organizationOptions' => $this->buildOrganizationOptions(),
        ];
    }

    public function createStrategy(array $payload): TrsBusinessStrategy
    {
        $strategy = TrsBusinessStrategy::query()->create($this->normalizePayload($payload));

        return $strategy->fresh(['businessUnit']);
    }

    public function updateStrategy(TrsBusinessStrategy $strategy, array $payload): TrsBusinessStrategy
    {
        $strategy->update($this->normalizePayload($payload));

        return $strategy->fresh(['businessUnit']);
    }

    public function bulkUpdateStrategies(array $rows): void
    {
        DB::transaction(function () use ($rows): void {
            $strategies = TrsBusinessStrategy::query()
                ->whereIn('id', collect($rows)->pluck('id')->filter()->all())
                ->get()
                ->keyBy('id');

            foreach ($rows as $row) {
                $strategy = $strategies->get((int) ($row['id'] ?? 0));

                if (!$strategy) {
                    continue;
                }

                $strategy->update($this->normalizePayload($row));
            }
        });
    }

    public function deleteStrategy(TrsBusinessStrategy $strategy): void
    {
        $strategy->delete();
    }

    private function mapRow(
        TrsBusinessStrategy $strategy,
        Collection $initiativesByBusinessUnit,
        array $businessUnitConfig = [],
        ?TrsOrganization $organization = null,
        array $strategyColumns = [],
        array $enablerGoals = []
    ): array
    {
        $groupMeta = $this->resolveGroupMeta($organization?->groub_id ?? $strategy->businessUnit?->groub_id);
        $businessUnitId = (int) ($strategy->business_unit ?? 0);
        $values = collect(self::STRATEGY_COLUMNS)
            ->mapWithKeys(fn (array $column): array => [
                $column['key'] => $this->normalizeValue($strategy->{$column['key']} ?? null),
            ])
            ->all();
        $initiatives = $initiativesByBusinessUnit->get($businessUnitId, []);
        $initiativesByKey = $this->buildInitiativesBySlot($initiatives, $strategyColumns);
        $enablerInitiativesByKey = $this->buildInitiativesBySlot($initiatives, $enablerGoals);
        $mainInitiatives = $this->mergeSlotInitiatives($initiativesByKey);

        $completionCount = collect($values)
            ->filter(fn (?string $value): bool => filled($value))
            ->count();

        return [
            'id' => (int) $strategy->id,
            'business_unit_id' => $businessUnitId,
            'business_unit' => trim((string) ($businessUnitConfig['label'] ?? $strategy->businessUnit?->name ?? 'Business Unit tidak ditemukan')),
            'business_unit_logo' => $businessUnitConfig['logo'] ?? null,
            'group_key' => $groupMeta['key'],
            'group_label' => $groupMeta['label'],
            'group_order' => $groupMeta['order'],
            'values' => $values,
            'initiatives' => $mainInitiatives,
            'initiatives_count' => count($mainInitiatives),
            'initiatives_by_key' => $initiativesByKey,
            'enabler_initiatives_by_key' => $enablerInitiativesByKey,
            'completion_count' => $completionCount,
            'is_complete' => $completionCount === count(self::STRATEGY_COLUMNS),
            'updated_at' => $strategy->updated_at?->toDateTimeString(),
        ];
    }

    private function getInitiativesByBusinessUnit(): Collection
    {
        $fixedBusinessUnitIds = collect(self::FIXED_BUSINESS_UNITS)->pluck('id')->all();

        return MstInitiative::query()
            ->select([
                'id',
                'business_unit',
                'code',
                'name',
                'description',
                'tipe_initiative',
            ])
            ->with([
                'latestStatusImplementation' => fn ($query) => $query->select([
                    'trs_status_implementation.id',
                    'trs_status_implementation.initiative_id',
                    'trs_status_implementation.review_status',
                ]),
                'statusImplementations' => fn ($query) => $query->select([
                    'trs_status_implementation.id',
                    'trs_status_implementation.initiative_id',
                    'trs_status_implementation.start',
                    'trs_status_implementation.end',
                    'trs_status_implementation.year',
                    'trs_status_implementation.review_status',
                ]),
                'taggings' => fn ($query) => $query->select([
                    'id',
                    'initiative_id',
                    'goal',
                    'pilar',
                    'themes_id',
                ])->where('pilar', '2'),
            ])
            ->whereNotNull('business_unit')
            ->whereIn('business_unit', $fixedBusinessUnitIds)
            ->get()
            ->groupBy(fn (MstInitiative $initiative): int => (int) ($initiative->business_unit ?? 0))
            ->map(function (Collection $initiatives): array {
                return $initiatives
                    ->sortBy(
                        fn (MstInitiative $initiative): string => trim(sprintf(
                            '%s %s',
                            (string) ($initiative->code ?? 'ZZZ'),
                            (string) ($initiative->name ?? '')
                        )),
                        SORT_NATURAL | SORT_FLAG_CASE
                    )
                    ->values()
                    ->map(fn (MstInitiative $initiative): array => $this->mapInitiative($initiative))
                    ->all();
            });
    }

    private function mapInitiative(MstInitiative $initiative): array
    {
        return [
            'id' => (int) $initiative->id,
            'code' => $this->normalizeValue($initiative->code),
            'name' => trim((string) ($initiative->name ?? '')),
            'description' => $this->normalizeValue($initiative->description),
            'tipe_initiative' => (int) ($initiative->tipe_initiative ?? 0),
            'implementation_status' => $initiative->latestStatusImplementation?->review_status,
            'statuses' => collect($initiative->statusImplementations ?? [])
                ->map(fn ($status): array => [
                    'start' => $status->start,
                    'end' => $status->end,
                    'year' => (int) $status->year,
                    'status' => $status->review_status,
                ])
                ->values()
                ->all(),
            'taggings' => collect($initiative->taggings ?? [])
                ->map(fn ($tagging): array => [
                    'goal' => strtoupper(trim((string) ($tagging->goal ?? ''))),
                    'pilar' => (string) ($tagging->pilar ?? ''),
                    'themes_id' => !is_null($tagging->themes_id) ? (int) $tagging->themes_id : null,
                ])
                ->values()
                ->all(),
        ];
    }

    private function buildGroups(Collection $rows): Collection
    {
        return collect([
            ['key' => 'holding', 'label' => 'Holding', 'order' => 1],
            ['key' => 'subholding', 'label' => 'Sub Holding', 'order' => 2],
            ['key' => 'other', 'label' => 'Other Organization', 'order' => 3],
        ])
            ->map(function (array $group) use ($rows): array {
                $groupRows = $rows
                    ->where('group_key', $group['key'])
                    ->values()
                    ->all();

                return [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'order' => $group['order'],
                    'count' => count($groupRows),
                    'rows' => $groupRows,
                ];
            })
            ->filter(fn (array $group): bool => $group['count'] > 0)
            ->values();
    }

    private function buildOrganizationOptions(): array
    {
        $fixedBusinessUnitIds = collect(self::FIXED_BUSINESS_UNITS)->pluck('id')->all();
        $organizationMeta = TrsOrganization::query()
            ->select(['id', 'groub_id'])
            ->whereIn('id', $fixedBusinessUnitIds)
            ->get()
            ->keyBy('id');

        return collect(self::FIXED_BUSINESS_UNITS)
            ->map(function (array $businessUnit) use ($organizationMeta): array {
                $groupMeta = $this->resolveGroupMeta($organizationMeta->get($businessUnit['id'])?->groub_id);

                return [
                    'value' => (string) $businessUnit['id'],
                    'label' => "{$groupMeta['label']} - {$businessUnit['label']}",
                ];
            })
            ->values()
            ->all();
    }

    private function buildSummary(Collection $rows, array $strategyColumns): array
    {
        $total = $rows->count();
        $columnCount = count($strategyColumns);

        return [
            'total_business_units' => $total,
            'holding_count' => (int) $rows->where('group_key', 'holding')->count(),
            'subholding_count' => (int) $rows->where('group_key', 'subholding')->count(),
            'other_count' => (int) $rows->where('group_key', 'other')->count(),
            'complete_count' => (int) $rows->where('completion_count', $columnCount)->count(),
            'partial_count' => (int) $rows->filter(
                fn (array $row): bool => ($row['completion_count'] ?? 0) > 0
                    && ($row['completion_count'] ?? 0) < $columnCount
            )->count(),
            'empty_count' => (int) $rows->where('completion_count', 0)->count(),
            'strategy_coverage' => collect($strategyColumns)
                ->map(function (array $column) use ($rows, $total): array {
                    $filledCount = (int) $rows->filter(
                        fn (array $row): bool => filled($row['values'][$column['key']] ?? null)
                    )->count();

                    return [
                        'key' => $column['key'],
                        'label' => $column['label'],
                        'description' => $column['description'],
                        'tone' => $column['tone'],
                        'filled_count' => $filledCount,
                        'empty_count' => max(0, $total - $filledCount),
                        'fill_rate' => $total > 0 ? (int) round(($filledCount / $total) * 100) : 0,
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function getStrategyColumns(array $strategyColumns): array
    {
        return collect($strategyColumns)
            ->map(fn (array $column): array => [
                'key' => $column['key'],
                'label' => $column['label'],
                'description' => $column['description'],
                'tone' => $column['tone'],
            ])
            ->values()
            ->all();
    }

    private function getStrategyMetadata(): array
    {
        $goalIds = collect(self::STRATEGY_GOAL_CONFIG)
            ->pluck('goal_id')
            ->merge(collect(self::STRATEGY_COLUMNS)->pluck('goal_id')->filter())
            ->merge(collect(self::ENABLER_CONFIG)->pluck('goal_id')->filter())
            ->unique()
            ->values()
            ->all();

        $themeIds = collect(self::STRATEGY_COLUMNS)
            ->pluck('theme_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $goals = Goal::query()
            ->select(['id', 'code', 'title', 'pilar'])
            ->whereIn('id', $goalIds)
            ->get()
            ->keyBy('id');

        $themes = Theme::query()
            ->select(['id', 'idGoal', 'theme_number', 'name'])
            ->whereIn('id', $themeIds)
            ->get()
            ->keyBy('id');

        $headerGoals = [
            'legacy' => $this->resolveGoalMetadata(
                $goals->get(self::STRATEGY_GOAL_CONFIG['legacy']['goal_id']),
                self::STRATEGY_GOAL_CONFIG['legacy']['default_title']
            ),
            'low_carbon' => $this->resolveGoalMetadata(
                $goals->get(self::STRATEGY_GOAL_CONFIG['low_carbon']['goal_id']),
                self::STRATEGY_GOAL_CONFIG['low_carbon']['default_title']
            ),
        ];

        $strategyColumns = collect(self::STRATEGY_COLUMNS)
            ->map(function (array $column) use ($goals, $themes): array {
                $theme = !empty($column['theme_id']) ? $themes->get((int) $column['theme_id']) : null;
                $goalId = $theme ? (int) ($theme->idGoal ?? 0) : (int) ($column['goal_id'] ?? 0);
                $goal = $goalId > 0 ? $goals->get($goalId) : null;

                return [
                    'key' => $column['key'],
                    'label' => trim((string) ($theme?->name ?? $goal?->title ?? $column['default_label'])),
                    'description' => $column['description'],
                    'tone' => $column['tone'],
                    'group' => $column['group'] ?? null,
                    'theme_id' => $theme ? (int) $theme->id : null,
                    'goal_id' => $goal ? (int) $goal->id : ($goalId ?: null),
                    'goal_code' => strtoupper(trim((string) ($goal?->code ?? $column['default_goal_code'] ?? ''))),
                    'direct_goal_tagging' => (bool) ($column['direct_goal_tagging'] ?? false),
                ];
            })
            ->values()
            ->all();

        $enablerGoals = collect(self::ENABLER_CONFIG)
            ->map(function (array $enabler) use ($goals): array {
                $goal = $goals->get((int) $enabler['goal_id']);
                $goalCode = strtoupper(trim((string) ($goal?->code ?? $enabler['default_goal_code'])));

                return [
                    'key' => $enabler['key'],
                    'id' => $goal ? (int) $goal->id : (int) $enabler['goal_id'],
                    'code' => $goalCode,
                    'goal_code' => $goalCode,
                    'title' => trim((string) ($goal?->title ?? $enabler['default_title'])),
                    'tone' => $enabler['tone'],
                    'direct_goal_tagging' => true,
                ];
            })
            ->values()
            ->all();

        return [
            'header_goals' => $headerGoals,
            'strategy_columns' => $strategyColumns,
            'enabler_goals' => $enablerGoals,
        ];
    }

    private function resolveGoalMetadata(?Goal $goal, string $defaultTitle): array
    {
        return [
            'id' => $goal ? (int) $goal->id : null,
            'code' => trim((string) ($goal?->code ?? '')),
            'title' => trim((string) ($goal?->title ?? $defaultTitle)),
        ];
    }

    private function buildInitiativesBySlot(array $initiatives, array $slots): array
    {
        return collect($slots)
            ->mapWithKeys(function (array $slot) use ($initiatives): array {
                $slotInitiatives = collect($initiatives)
                    ->filter(fn (array $initiative): bool => $this->initiativeMatchesSlot($initiative, $slot))
                    ->values()
                    ->all();

                return [
                    $slot['key'] => $slotInitiatives,
                ];
            })
            ->all();
    }

    private function initiativeMatchesSlot(array $initiative, array $slot): bool
    {
        $taggings = collect($initiative['taggings'] ?? [])
            ->filter(fn (array $tagging): bool => (string) ($tagging['pilar'] ?? '') === '2');

        if ($taggings->isEmpty()) {
            return false;
        }

        $themeId = !empty($slot['theme_id']) ? (int) $slot['theme_id'] : null;
        if ($themeId) {
            return $taggings->contains(
                fn (array $tagging): bool => (int) ($tagging['themes_id'] ?? 0) === $themeId
            );
        }

        $goalCode = strtoupper(trim((string) ($slot['goal_code'] ?? '')));
        if ($goalCode === '') {
            return false;
        }

        return $taggings->contains(function (array $tagging) use ($goalCode): bool {
            return strtoupper(trim((string) ($tagging['goal'] ?? ''))) === $goalCode
                && empty($tagging['themes_id']);
        });
    }

    private function mergeSlotInitiatives(array $slotInitiatives): array
    {
        return collect($slotInitiatives)
            ->flatMap(fn (array $initiatives): array => $initiatives)
            ->unique('id')
            ->sortBy(
                fn (array $initiative): string => trim(sprintf(
                    '%s %s',
                    (string) ($initiative['code'] ?? 'ZZZ'),
                    (string) ($initiative['name'] ?? '')
                )),
                SORT_NATURAL | SORT_FLAG_CASE
            )
            ->values()
            ->all();
    }

    private function resolveGroupMeta(?int $groupId): array
    {
        if ((int) $groupId === 2) {
            return [
                'key' => 'subholding',
                'label' => 'Sub Holding',
                'order' => 2,
            ];
        }

        if ((int) $groupId > 0) {
            return [
                'key' => 'holding',
                'label' => 'Holding',
                'order' => 1,
            ];
        }

        return [
            'key' => 'other',
            'label' => 'Other Organization',
            'order' => 3,
        ];
    }

    private function normalizeValue(?string $value): ?string
    {
        $rawValue = str_replace(["\r\n", "\r"], "\n", (string) $value);
        $lines = collect(explode("\n", $rawValue))
            ->map(function (string $line): string {
                $line = preg_replace('/[ \t]+/', ' ', $line) ?? '';

                return trim($line);
            })
            ->all();

        $normalized = trim(implode("\n", $lines));

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizePayload(array $payload): array
    {
        return [
            'business_unit' => (int) ($payload['business_unit'] ?? 0),
            'maximazing_value' => $this->normalizeValue($payload['maximazing_value'] ?? null),
            'expand' => $this->normalizeValue($payload['expand'] ?? null),
            'low_carbon' => $this->normalizeValue($payload['low_carbon'] ?? null),
        ];
    }
}
