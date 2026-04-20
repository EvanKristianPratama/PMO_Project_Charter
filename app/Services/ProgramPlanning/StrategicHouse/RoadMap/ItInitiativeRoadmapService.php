<?php

namespace App\Services\ProgramPlanning\StrategicHouse\RoadMap;

use App\Models\Milestone;
use App\Models\MstInitiative;

class ItInitiativeRoadmapService
{
    private const DEFAULT_START_YEAR = 2025;
    private const DEFAULT_END_YEAR   = 2029;
    private const MONTHS_ORDER = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    public function getPageProps(): array
    {
        $initiatives = MstInitiative::query()
            ->where('tipe_initiative', 2)
            ->with([
                'coe:id,name',
                'latestStatusImplementation' => fn ($query) => $query
                    ->select([
                        'trs_status_implementation.id',
                        'trs_status_implementation.initiative_id',
                        'trs_status_implementation.review_status',
                    ]),
                'mappedProjects' => fn ($q) => $q->with([
                    'reviewPcStatusImplementations' => fn ($rq) => $rq
                        ->select(['id', 'project_id', 'start', 'end', 'year', 'review_status', 'created_at', 'updated_at'])
                        ->orderByDesc('year')
                        ->orderByDesc('updated_at')
                        ->orderByDesc('id'),
                    'charters' => fn ($cq) => $cq
                        ->select(['id', 'project_id', 'version_label', 'objectives', 'duration'])
                        ->with(['milestones' => fn ($mq) => $mq
                            ->select(['id', 'pc_id', 'version', 'title', 'output', 'start_date', 'end_date', 'type', 'milestone_type', 'order'])
                            ->orderBy('order')
                            ->orderBy('id'),
                        ])
                        ->orderByDesc('id'),
                ]),
            ])
            ->orderBy('code')
            ->orderBy('id')
            ->get();

        $groups       = [];
        $globalNumber = 1;

        $byCoE = $initiatives->groupBy(
            fn (MstInitiative $i) => $i->coe?->name ?? 'Uncategorized',
        );

        foreach ($byCoE as $coeName => $groupInitiatives) {
            $initiativeData = [];

            foreach ($groupInitiatives as $initiative) {
                $reviewStatuses = $this->collectInitiativeReviewStatuses($initiative);

                // Flatten all charters from all mapped projects
                $projects = $initiative->mappedProjects
                    ->flatMap(fn ($project) => $project->charters->map(
                        fn ($charter) => $this->mapCharter($charter, $project->name, $project->code),
                    ))
                    ->values()
                    ->all();

                $initiativeData[] = [
                    'no'                    => $globalNumber++,
                    'id'                    => (int) $initiative->id,
                    'name'                  => trim((string) ($initiative->name ?? '-')),
                    'projects'              => $projects,
                    'implementation_status' => $this->resolveInitiativeReviewStatus($reviewStatuses, $initiative),
                    'review_statuses'       => $reviewStatuses,
                ];
            }

            $groups[] = [
                'coe_name'    => $coeName,
                'initiatives' => $initiativeData,
            ];
        }

        return [
            'groups'               => $groups,
            'startYear'            => self::DEFAULT_START_YEAR,
            'endYear'              => self::DEFAULT_END_YEAR,
            'totalCount'           => $initiatives->count(),
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
        ];
    }

    private function collectInitiativeReviewStatuses(MstInitiative $initiative): array
    {
        $reviewLogs = ($initiative->mappedProjects ?? collect())
            ->flatMap(fn ($project) => $project->reviewPcStatusImplementations ?? collect())
            ->filter();

        return $reviewLogs
            ->map(function ($log): ?array {
                $reviewStatus = trim((string) ($log?->review_status ?? ''));
                $periodKey = $this->buildPeriodKey($log);
                $periodLabel = trim((string) ($log?->periode_label ?? ''));

                if ($reviewStatus === '' || $periodKey === null || $periodLabel === '') {
                    return null;
                }

                return [
                    'id' => (int) ($log?->id ?? 0),
                    'project_id' => (int) ($log?->project_id ?? 0),
                    'review_status' => $reviewStatus,
                    'period_key' => $periodKey,
                    'periode_label' => $periodLabel,
                    'start' => trim((string) ($log?->start ?? '')),
                    'end' => trim((string) ($log?->end ?? '')),
                    'year' => trim((string) ($log?->year ?? '')),
                    'created_at' => $log?->created_at?->toISOString(),
                    'updated_at' => $log?->updated_at?->toISOString(),
                ];
            })
            ->filter()
            ->sort(fn (array $left, array $right) => $this->compareReviewLogs($left, $right))
            ->values()
            ->all();
    }

    private function resolveInitiativeReviewStatus(array $reviewStatuses, MstInitiative $initiative): ?string
    {
        if ($reviewStatuses !== []) {
            $latestLog = collect($reviewStatuses)->last();
            $reviewStatus = trim((string) ($latestLog['review_status'] ?? ''));

            if ($reviewStatus !== '') {
                return $reviewStatus;
            }
        }

        $fallbackStatus = trim((string) ($initiative->latestStatusImplementation?->review_status ?? ''));

        return $fallbackStatus !== '' ? $fallbackStatus : null;
    }

    private function compareReviewLogs(mixed $left, mixed $right): int
    {
        $leftYear = (int) ($left['year'] ?? $left?->year ?? 0);
        $rightYear = (int) ($right['year'] ?? $right?->year ?? 0);

        if ($leftYear !== $rightYear) {
            return $leftYear <=> $rightYear;
        }

        $leftStartOrder = $this->monthOrderValue($left['start'] ?? $left?->start ?? null);
        $rightStartOrder = $this->monthOrderValue($right['start'] ?? $right?->start ?? null);

        if ($leftStartOrder !== $rightStartOrder) {
            return $leftStartOrder <=> $rightStartOrder;
        }

        $leftEndOrder = $this->monthOrderValue($left['end'] ?? $left?->end ?? null);
        $rightEndOrder = $this->monthOrderValue($right['end'] ?? $right?->end ?? null);

        if ($leftEndOrder !== $rightEndOrder) {
            return $leftEndOrder <=> $rightEndOrder;
        }

        $leftTimestamp = max(
            (int) data_get($left, 'updated_at.timestamp', strtotime((string) ($left['updated_at'] ?? '')) ?: 0),
            (int) data_get($left, 'created_at.timestamp', strtotime((string) ($left['created_at'] ?? '')) ?: 0),
        );
        $rightTimestamp = max(
            (int) data_get($right, 'updated_at.timestamp', strtotime((string) ($right['updated_at'] ?? '')) ?: 0),
            (int) data_get($right, 'created_at.timestamp', strtotime((string) ($right['created_at'] ?? '')) ?: 0),
        );

        if ($leftTimestamp !== $rightTimestamp) {
            return $leftTimestamp <=> $rightTimestamp;
        }

        return (int) ($left['id'] ?? $left?->id ?? 0) <=> (int) ($right['id'] ?? $right?->id ?? 0);
    }

    private function buildPeriodKey(mixed $log): ?string
    {
        $start = trim((string) ($log?->start ?? ''));
        $end = trim((string) ($log?->end ?? ''));
        $year = trim((string) ($log?->year ?? ''));

        if ($start === '' || $year === '') {
            return null;
        }

        return implode('|', [$start, $end, $year]);
    }

    private function monthOrderValue(?string $month): int
    {
        $normalized = trim((string) $month);

        if ($normalized === '') {
            return 0;
        }

        $index = array_search($normalized, self::MONTHS_ORDER, true);

        return $index === false ? 0 : $index + 1;
    }

    private function mapCharter($charter, string $projectName, ?string $projectCode): array
    {
        $versionLabel = $this->normalizeVersionLabel($charter->version_label);

        $milestones = ($charter->milestones ?? collect())
            ->map(fn ($ms) => [
                'id'             => (int) $ms->id,
                'pc_id'          => (int) $ms->pc_id,
                'version'        => $versionLabel,
                'title'          => $ms->title ?? '-',
                'output'         => $ms->output ?? '',
                'start_date'     => $ms->start_date?->format('Y-m-d'),
                'end_date'       => $ms->end_date?->format('Y-m-d'),
                'type'           => $ms->type,
                'milestone_type' => $ms->milestone_type,
                'order'          => $ms->order,
            ])
            ->values()
            ->all();

        return [
            'id'            => (int) $charter->id,
            'project_id'    => (int) $charter->project_id,
            'name'          => $projectName,
            'code'          => $projectCode,
            'version_label' => $charter->version_label,
            'objectives'    => $charter->objectives,
            'duration'      => $charter->duration,
            'milestones'    => $milestones,
            'charter'       => [
                'id'            => (int) $charter->id,
                'project_id'    => (int) $charter->project_id,
                'version_label' => $charter->version_label,
                'objectives'    => $charter->objectives,
                'duration'      => $charter->duration,
            ],
        ];
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $raw = strtolower(trim((string) $value));

        if ($raw === '' || $raw === 'v') {
            return 'v1';
        }

        if (preg_match('/^v(\d+)$/', $raw, $matches) === 1) {
            return 'v' . max(1, (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $raw) === 1) {
            return 'v' . max(1, (int) $raw);
        }

        return $raw;
    }
}
