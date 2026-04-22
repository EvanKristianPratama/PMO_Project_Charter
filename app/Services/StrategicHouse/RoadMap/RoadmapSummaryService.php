<?php

namespace App\Services\StrategicHouse\RoadMap;

use App\Models\MstInitiative;

class RoadmapSummaryService
{
    private const DEFAULT_START_YEAR = 2024;
    private const DEFAULT_END_YEAR = 2029;

    private const YEAR_LABELS = [
        0 => 'Previous Year',
        1 => 'First Year',
        2 => 'Second Year',
        3 => 'Third Year',
        4 => 'Fourth Year',
        5 => 'Fifth Year',
    ];

    public function __construct(
        private readonly ItInitiativeRoadmapService $itRoadmapService,
    ) {}

    public function getPageProps(): array
    {
        $digitalGroups = $this->buildDigitalSummaryGroups();
        $itGroups = $this->buildItSummaryGroups();

        [$startYear, $endYear] = $this->resolveUnifiedYearRange($digitalGroups, $itGroups);

        return [
            'digitalGroups' => $digitalGroups,
            'itGroups' => $itGroups,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'yearLabels' => $this->buildYearLabels($startYear, $endYear),
        ];
    }

    private function buildDigitalSummaryGroups(): array
    {
        $initiatives = MstInitiative::query()
            ->where('tipe_initiative', 1)
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'masterMilestones:id,initiative_id,startYear,startQ,endYear,endQ',
            ])
            ->get([
                'id',
                'coe_id',
                'business_unit',
            ]);

        $byGroub = [
            1 => [
                'section_order' => 0,
                'section_label' => 'Holding',
                'rows' => [],
            ],
            2 => [
                'section_order' => 1,
                'section_label' => 'Sub Holding',
                'rows' => [],
            ],
        ];

        $grouped = [];

        foreach ($initiatives as $initiative) {
            $groub = $initiative->organization?->groub;
            $groubId = (int) ($groub?->id ?? $initiative->organization?->groub_id ?? 0);
            $groubName = trim((string) ($groub?->name ?? ''));
            $coeName = trim((string) ($initiative->coe?->name ?? ''));

            if ($groubId <= 0 || $coeName === '') {
                continue;
            }

            $key = $groubId . '||' . $coeName;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'groub_id' => $groubId,
                    'groub_name' => $groubName,
                    'coe_name' => $coeName,
                    'initiative_ids' => [],
                    'start_years' => [],
                    'start_quarters' => [],
                    'end_years' => [],
                    'end_quarters' => [],
                ];
            }

            $grouped[$key]['initiative_ids'][(int) $initiative->id] = true;

            foreach ($initiative->masterMilestones as $milestone) {
                $startYear = (int) $milestone->startYear;
                $endYear = (int) $milestone->endYear;
                $startQuarter = $this->parseQuarter($milestone->startQ);
                $endQuarter = $this->parseQuarter($milestone->endQ);

                if ($startYear > 0 && $startQuarter > 0) {
                    $grouped[$key]['start_years'][] = $startYear;
                    $grouped[$key]['start_quarters'][] = $startQuarter;
                }

                if ($endYear > 0 && $endQuarter > 0) {
                    $grouped[$key]['end_years'][] = $endYear;
                    $grouped[$key]['end_quarters'][] = $endQuarter;
                }
            }
        }

        foreach ($grouped as $data) {
            $groubId = (int) $data['groub_id'];

            if (! isset($byGroub[$groubId])) {
                $byGroub[$groubId] = [
                    'section_order' => $this->resolveDigitalGroupOrder($groubId, $data['groub_name']),
                    'section_label' => $data['groub_name'] !== '' ? $data['groub_name'] : 'Other',
                    'rows' => [],
                ];
            }

            [$minYear, $minQuarter] = $this->findMinYearQuarter(
                $data['start_years'],
                $data['start_quarters'],
            );

            [$maxYear, $maxQuarter] = $this->findMaxYearQuarter(
                $data['end_years'],
                $data['end_quarters'],
            );

            $byGroub[$groubId]['rows'][] = [
                'label' => $data['coe_name'],
                'count' => count($data['initiative_ids']),
                'has_timeline' => $minYear !== null && $maxYear !== null,
                'start_year' => $minYear,
                'start_quarter' => $minQuarter,
                'end_year' => $maxYear,
                'end_quarter' => $maxQuarter,
            ];
        }

        foreach ($byGroub as &$group) {
            usort($group['rows'], function (array $left, array $right): int {
                $orderCompare = $this->resolveCoeSortOrder((string) $left['label'])
                    <=> $this->resolveCoeSortOrder((string) $right['label']);

                if ($orderCompare !== 0) {
                    return $orderCompare;
                }

                $countCompare = $right['count'] <=> $left['count'];

                if ($countCompare !== 0) {
                    return $countCompare;
                }

                return strcasecmp((string) $left['label'], (string) $right['label']);
            });
        }
        unset($group);

        uasort($byGroub, fn (array $left, array $right) => $left['section_order'] <=> $right['section_order']);

        return array_values(array_filter(
            array_map(static function (array $group): array {
                unset($group['section_order']);

                return $group;
            }, $byGroub),
            static fn (array $group): bool => ! empty($group['rows']),
        ));
    }

    private function buildItSummaryGroups(): array
    {
        $roadmapData = $this->itRoadmapService->getPageProps();
        $groups = $roadmapData['groups'] ?? [];
        $rows = [];

        foreach ($groups as $group) {
            $coeName = $group['coe_name'] ?? 'Uncategorized';
            $initiatives = $group['initiatives'] ?? [];
            $count = count($initiatives);

            $minDate = null;
            $maxDate = null;

            foreach ($initiatives as $initiative) {
                foreach (($initiative['projects'] ?? []) as $project) {
                    foreach (($project['milestones'] ?? []) as $milestone) {
                        $startDate = $milestone['start_date'] ?? null;
                        $endDate = $milestone['end_date'] ?? null;

                        if ($startDate !== null && ($minDate === null || $startDate < $minDate)) {
                            $minDate = $startDate;
                        }

                        if ($endDate !== null && ($maxDate === null || $endDate > $maxDate)) {
                            $maxDate = $endDate;
                        }
                    }
                }
            }

            if ($minDate === null || $maxDate === null || $count === 0) {
                continue;
            }

            [$startYear, $startQuarter] = $this->dateToYearQuarter($minDate);
            [$endYear, $endQuarter] = $this->dateToYearQuarter($maxDate);

            if ($startYear === null || $endYear === null) {
                continue;
            }

            $rows[] = [
                'label' => $coeName,
                'count' => $count,
                'start_year' => $startYear,
                'start_quarter' => $startQuarter,
                'end_year' => $endYear,
                'end_quarter' => $endQuarter,
            ];
        }

        return [
            [
                'section_label' => 'IT Initiative',
                'rows' => $rows,
            ],
        ];
    }

    private function parseQuarter(mixed $value): int
    {
        $raw = strtoupper(trim((string) $value));

        if (preg_match('/Q?([1-4])/', $raw, $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function dateToYearQuarter(?string $date): array
    {
        if ($date === null || $date === '') {
            return [null, null];
        }

        if (preg_match('/^(\d{4})-(\d{2})/', $date, $matches) !== 1) {
            return [null, null];
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $quarter = (int) ceil($month / 3);

        return [$year, max(1, min(4, $quarter))];
    }

    private function findMinYearQuarter(array $years, array $quarters): array
    {
        if ($years === []) {
            return [null, null];
        }

        $minComposite = null;
        $minYear = null;
        $minQuarter = null;

        foreach ($years as $index => $year) {
            $quarter = $quarters[$index] ?? 1;
            $composite = $year * 10 + $quarter;

            if ($minComposite === null || $composite < $minComposite) {
                $minComposite = $composite;
                $minYear = $year;
                $minQuarter = $quarter;
            }
        }

        return [$minYear, $minQuarter];
    }

    private function findMaxYearQuarter(array $years, array $quarters): array
    {
        if ($years === []) {
            return [null, null];
        }

        $maxComposite = null;
        $maxYear = null;
        $maxQuarter = null;

        foreach ($years as $index => $year) {
            $quarter = $quarters[$index] ?? 1;
            $composite = $year * 10 + $quarter;

            if ($maxComposite === null || $composite > $maxComposite) {
                $maxComposite = $composite;
                $maxYear = $year;
                $maxQuarter = $quarter;
            }
        }

        return [$maxYear, $maxQuarter];
    }

    private function resolveUnifiedYearRange(array $digitalGroups, array $itGroups): array
    {
        $allRows = collect($digitalGroups)
            ->merge($itGroups)
            ->flatMap(fn (array $group) => $group['rows'] ?? []);

        $startYears = $allRows->pluck('start_year')->filter()->all();
        $endYears = $allRows->pluck('end_year')->filter()->all();

        $minYear = $startYears !== []
            ? min(min($startYears), self::DEFAULT_START_YEAR)
            : self::DEFAULT_START_YEAR;
        $maxYear = $endYears !== []
            ? max(max($endYears), self::DEFAULT_END_YEAR)
            : self::DEFAULT_END_YEAR;

        return [$minYear, $maxYear];
    }

    private function buildYearLabels(int $startYear, int $endYear): array
    {
        $labels = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $offset = $year - $startYear;
            $labels[] = [
                'year' => $year,
                'label' => self::YEAR_LABELS[$offset] ?? 'Year ' . ($offset + 1),
            ];
        }

        return $labels;
    }

    private function resolveDigitalGroupOrder(int $groubId, string $groubName): int
    {
        $normalizedName = strtolower(trim($groubName));

        if ($groubId === 1 || $normalizedName === 'holding') {
            return 0;
        }

        if ($groubId === 2 || str_contains($normalizedName, 'sub')) {
            return 1;
        }

        return 99;
    }

    private function resolveCoeSortOrder(string $coeName): int
    {
        return match (strtolower(trim($coeName))) {
            'ai/analytics' => 0,
            'iot' => 1,
            'cloud & advanced computing' => 2,
            'cloud & adv. computing' => 2,
            'rpa' => 3,
            'robotics' => 4,
            default => 99,
        };
    }
}
