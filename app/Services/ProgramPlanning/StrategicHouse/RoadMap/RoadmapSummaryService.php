<?php

namespace App\Services\ProgramPlanning\StrategicHouse\RoadMap;

use App\Models\MstInitiative;
use App\Models\TrsMasterMilestone;

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
        $milestones = TrsMasterMilestone::query()
            ->with([
                'initiative:id,code,name,coe_id,business_unit',
                'initiative.coe:id,name',
                'initiative.organization:id,name',
            ])
            ->get();

        // Group by organization → coe
        $grouped = [];

        foreach ($milestones as $milestone) {
            $initiative       = $milestone->initiative;
            $organizationName = trim((string) ($initiative?->organization?->name ?? ''));
            $coeName          = trim((string) ($initiative?->coe?->name ?? ''));

            if ($organizationName === '' || $coeName === '') {
                continue;
            }

            $key = $organizationName . '||' . $coeName;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'organization_name' => $organizationName,
                    'coe_name'          => $coeName,
                    'initiative_ids'    => [],
                    'start_years'       => [],
                    'start_quarters'    => [],
                    'end_years'         => [],
                    'end_quarters'      => [],
                ];
            }

            $initiativeId = (int) ($initiative?->id ?? 0);
            if ($initiativeId > 0) {
                $grouped[$key]['initiative_ids'][$initiativeId] = true;
            }

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

        // Build final structure grouped by organization
        $byOrganization = [];

        foreach ($grouped as $data) {
            $orgName = $data['organization_name'];

            if (! isset($byOrganization[$orgName])) {
                $byOrganization[$orgName] = [
                    'section_label' => $orgName,
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

            if ($minYear === null || $maxYear === null) {
                continue;
            }

            $byOrganization[$orgName]['rows'][] = [
                'label'        => $data['coe_name'],
                'count'        => count($data['initiative_ids']),
                'start_year'   => $minYear,
                'start_quarter' => $minQ,
                'end_year'     => $maxYear,
                'end_quarter'  => $maxQ,
            ];
        }

        // Sort rows within each organization by count descending
        foreach ($byOrganization as &$group) {
            usort($group['rows'], fn (array $a, array $b) => $b['count'] <=> $a['count']);
        }
        unset($group);

        return array_values($byOrganization);
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
}
