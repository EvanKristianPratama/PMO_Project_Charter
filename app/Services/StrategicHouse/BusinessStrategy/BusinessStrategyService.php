<?php

namespace App\Services\StrategicHouse\BusinessStrategy;

use App\Models\TrsOrganization;
use App\Models\TrsBusinessStrategy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BusinessStrategyService
{
    private const STRATEGY_COLUMNS = [
        [
            'key' => 'maximazing_value',
            'label' => 'Maximizing Value',
            'description' => 'Fokus penguatan value dari bisnis inti yang sudah berjalan.',
            'tone' => 'sky',
        ],
        [
            'key' => 'expand',
            'label' => 'Expand to New Markets & Adjacencies',
            'description' => 'Peluang ekspansi pasar dan pengembangan adjacency baru.',
            'tone' => 'amber',
        ],
        [
            'key' => 'low_carbon',
            'label' => 'Building Low Carbon Business',
            'description' => 'Arah strategi untuk pertumbuhan bisnis rendah karbon.',
            'tone' => 'emerald',
        ],
    ];

    public function getPageProps(): array
    {
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
            ->orderBy('id')
            ->get()
            ->map(fn (TrsBusinessStrategy $strategy): array => $this->mapRow($strategy))
            ->values();

        return [
            'page' => [
                'title' => 'Business Strategy',
                'headline' => 'Business Strategy Matrix',
                'description' => 'Pemetaan arah strategi bisnis per business unit dalam tiga fokus utama dual growth.',
            ],
            'summary' => $this->buildSummary($rows),
            'strategyColumns' => $this->getStrategyColumns(),
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

    private function mapRow(TrsBusinessStrategy $strategy): array
    {
        $groupMeta = $this->resolveGroupMeta($strategy->businessUnit?->groub_id);
        $values = collect(self::STRATEGY_COLUMNS)
            ->mapWithKeys(fn (array $column): array => [
                $column['key'] => $this->normalizeValue($strategy->{$column['key']} ?? null),
            ])
            ->all();

        $completionCount = collect($values)
            ->filter(fn (?string $value): bool => filled($value))
            ->count();

        return [
            'id' => (int) $strategy->id,
            'business_unit_id' => (int) ($strategy->business_unit ?? 0),
            'business_unit' => trim((string) ($strategy->businessUnit?->name ?? 'Business Unit tidak ditemukan')),
            'group_key' => $groupMeta['key'],
            'group_label' => $groupMeta['label'],
            'group_order' => $groupMeta['order'],
            'values' => $values,
            'completion_count' => $completionCount,
            'is_complete' => $completionCount === count(self::STRATEGY_COLUMNS),
            'updated_at' => $strategy->updated_at?->toDateTimeString(),
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
        return TrsOrganization::query()
            ->select(['id', 'name', 'groub_id'])
            ->orderBy('name')
            ->get()
            ->map(function (TrsOrganization $organization): array {
                $groupMeta = $this->resolveGroupMeta($organization->groub_id);

                return [
                    'value' => (string) $organization->id,
                    'label' => "{$groupMeta['label']} - {$organization->name}",
                ];
            })
            ->sortBy(fn (array $item) => $item['label'], SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function buildSummary(Collection $rows): array
    {
        $total = $rows->count();

        return [
            'total_business_units' => $total,
            'holding_count' => (int) $rows->where('group_key', 'holding')->count(),
            'subholding_count' => (int) $rows->where('group_key', 'subholding')->count(),
            'other_count' => (int) $rows->where('group_key', 'other')->count(),
            'complete_count' => (int) $rows->where('completion_count', count(self::STRATEGY_COLUMNS))->count(),
            'partial_count' => (int) $rows->filter(
                fn (array $row): bool => ($row['completion_count'] ?? 0) > 0
                    && ($row['completion_count'] ?? 0) < count(self::STRATEGY_COLUMNS)
            )->count(),
            'empty_count' => (int) $rows->where('completion_count', 0)->count(),
            'strategy_coverage' => collect(self::STRATEGY_COLUMNS)
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

    private function getStrategyColumns(): array
    {
        return collect(self::STRATEGY_COLUMNS)
            ->map(fn (array $column): array => [
                'key' => $column['key'],
                'label' => $column['label'],
                'description' => $column['description'],
                'tone' => $column['tone'],
            ])
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
