<?php

namespace App\Services\ProgramPlanning\StrategicHouse\RoadMap;

use App\Models\MstInitiative;

class RoadmapSummaryService
{
    private const DEFAULT_START_YEAR = 2024;
    private const DEFAULT_END_YEAR   = 2029;

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
        $itGroups      = $this->buildItSummaryGroups();

        [$startYear, $endYear] = $this->resolveUnifiedYearRange($digitalGroups, $itGroups);

        $yearLabels = $this->buildYearLabels($startYear, $endYear);

        return [
            'digitalGroups' => $digitalGroups,
            'itGroups'      => $itGroups,
            'startYear'     => $startYear,
            'endYear'       => $endYear,
            'yearLabels'    => $yearLabels,
        ];
    }

    /* ── Digital Initiative Summary ──────────────────────────────── */

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
                'rows'          => [],
            ],
            2 => [
                'section_order' => 1,
                'section_label' => 'Sub Holding',
                'rows'          => [],
            ],
        ];

        $grouped = [];

        foreach ($initiatives as $initiative) {
            $groub     = $initiative->organization?->groub;
            $groubId   = (int) ($groub?->id ?? $initiative->organization?->groub_id ?? 0);
            $groubName = trim((string) ($groub?->name ?? ''));
            $coeName   = trim((string) ($initiative->coe?->name ?? ''));

            if ($groubId <= 0 || $coeName === '') {
                continue;
            }

            $key = $groubId . '||' . $coeName;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'groub_id'        => $groubId,
                    'groub_name'      => $groubName,
                    'coe_name'        => $coeName,
                    'initiative_ids'  => [],
                    'start_years'     => [],
                    'start_quarters'  => [],
                    'end_years'       => [],
                    'end_quarters'    => [],
                ];
            }

            $grouped[$key]['initiative_ids'][(int) $initiative->id] = true;

            foreach ($initiative->masterMilestones as $milestone) {
                $startYear = (int) $milestone->startYear;
                $endYear   = (int) $milestone->endYear;
                $startQ    = $this->parseQuarter($milestone->startQ);
                $endQ      = $this->parseQuarter($milestone->endQ);

                if ($startYear > 0 && $startQ > 0) {
                    $grouped[$key]['start_years'][]    = $startYear;
                    $grouped[$key]['start_quarters'][] = $startQ;
                }

                if ($endYear > 0 && $endQ > 0) {
                    $grouped[$key]['end_years'][]    = $endYear;
                    $grouped[$key]['end_quarters'][] = $endQ;
                }
            }
        }

        foreach ($grouped as $data) {
            $groubId = (int) $data['groub_id'];

            if (! isset($byGroub[$groubId])) {
                $byGroub[$groubId] = [
                    'section_order' => $this->resolveDigitalGroupOrder($groubId, $data['groub_name']),
                    'section_label' => $data['groub_name'] !== '' ? $data['groub_name'] : 'Other',
                    'rows'          => [],
                ];
            }

            [$minYear, $minQ] = $this->findMinYearQuarter(
                $data['start_years'],
                $data['start_quarters'],
            );

            [$maxYear, $maxQ] = $this->findMaxYearQuarter(
                $data['end_years'],
                $data['end_quarters'],
            );

            $byGroub[$groubId]['rows'][] = [
                'label'          => $data['coe_name'],
                'count'          => count($data['initiative_ids']),
                'has_timeline'   => $minYear !== null && $maxYear !== null,
                'start_year'     => $minYear,
                'start_quarter'  => $minQ,
                'end_year'       => $maxYear,
                'end_quarter'    => $maxQ,
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

    /* ── IT Initiative Summary ──────────────────────────────────── */

    private function buildItSummaryGroups(): array
    {
        $roadmapData = $this->itRoadmapService->getPageProps();
        $groups      = $roadmapData['groups'] ?? [];

        $rows = [];

        foreach ($groups as $group) {
            $coeName     = $group['coe_name'] ?? 'Uncategorized';
            $initiatives = $group['initiatives'] ?? [];
            $count       = count($initiatives);

            $minDate = null;
            $maxDate = null;

            foreach ($initiatives as $initiative) {
                $projects = $initiative['projects'] ?? [];

                foreach ($projects as $project) {
                    $milestones = $project['milestones'] ?? [];

                    foreach ($milestones as $ms) {
                        $startDate = $ms['start_date'] ?? null;
                        $endDate   = $ms['end_date'] ?? null;

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

            [$startYear, $startQ] = $this->dateToYearQuarter($minDate);
            [$endYear, $endQ]     = $this->dateToYearQuarter($maxDate);

            if ($startYear === null || $endYear === null) {
                continue;
            }

            $rows[] = [
                'label'         => $coeName,
                'count'         => $count,
                'start_year'    => $startYear,
                'start_quarter' => $startQ,
                'end_year'      => $endYear,
                'end_quarter'   => $endQ,
            ];
        }

        return [
            [
                'section_label' => 'IT Initiative',
                'rows'          => $rows,
            ],
        ];
    }

    /* ── Helpers ─────────────────────────────────────────────────── */

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

        $matched = preg_match('/^(\d{4})-(\d{2})/', $date, $m);

        if ($matched !== 1) {
            return [null, null];
        }

        $year  = (int) $m[1];
        $month = (int) $m[2];
        $quarter = (int) ceil($month / 3);

        return [$year, max(1, min(4, $quarter))];
    }

    private function findMinYearQuarter(array $years, array $quarters): array
    {
        if (empty($years)) {
            return [null, null];
        }

        $minComposite = null;
        $minYear      = null;
        $minQ         = null;

        foreach ($years as $i => $year) {
            $q = $quarters[$i] ?? 1;
            $composite = $year * 10 + $q;

            if ($minComposite === null || $composite < $minComposite) {
                $minComposite = $composite;
                $minYear      = $year;
                $minQ         = $q;
            }
        }

        return [$minYear, $minQ];
    }

    private function findMaxYearQuarter(array $years, array $quarters): array
    {
        if (empty($years)) {
            return [null, null];
        }

        $maxComposite = null;
        $maxYear      = null;
        $maxQ         = null;

        foreach ($years as $i => $year) {
            $q = $quarters[$i] ?? 1;
            $composite = $year * 10 + $q;

            if ($maxComposite === null || $composite > $maxComposite) {
                $maxComposite = $composite;
                $maxYear      = $year;
                $maxQ         = $q;
            }
        }

        return [$maxYear, $maxQ];
    }

    private function resolveUnifiedYearRange(array $digitalGroups, array $itGroups): array
    {
        $allRows = collect($digitalGroups)
            ->merge($itGroups)
            ->flatMap(fn (array $group) => $group['rows'] ?? []);

        $startYears = $allRows->pluck('start_year')->filter()->all();
        $endYears   = $allRows->pluck('end_year')->filter()->all();

        $minYear = ! empty($startYears) ? min(min($startYears), self::DEFAULT_START_YEAR) : self::DEFAULT_START_YEAR;
        $maxYear = ! empty($endYears) ? max(max($endYears), self::DEFAULT_END_YEAR) : self::DEFAULT_END_YEAR;

        return [$minYear, $maxYear];
    }

    private function buildYearLabels(int $startYear, int $endYear): array
    {
        $labels = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $offset = $year - $startYear;
            $labels[] = [
                'year'  => $year,
                'label' => self::YEAR_LABELS[$offset] ?? "Year " . ($offset + 1),
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
