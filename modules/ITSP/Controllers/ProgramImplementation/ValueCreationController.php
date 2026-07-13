<?php

namespace Modules\ITSP\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\MstInitiative;
use Modules\ITSP\Services\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeService;
use Inertia\Inertia;
use Inertia\Response;

class ValueCreationController extends Controller
{
    public function __construct(
        private readonly ITInitiativeService $itInitiativeService
    ) {}

    public function index(): Response
    {
        $props = $this->itInitiativeService->getIndexProps();
        $props['tableMode'] = 'value_creation';

        $valueCreationData = MstInitiative::query()
            ->where('tipe_initiative', 2)
            ->with([
                'coe:id,name',
                'organization:id,name',
                'mappedProjects.charter' => function ($query) {
                    $query->select(
                        'trs_project_charters.id',
                        'trs_project_charters.project_id',
                        'trs_project_charters.impact_value',
                        'trs_project_charters.status'
                    );
                },
                'mappedProjects.charters' => function ($query) {
                    $query->select(
                        'trs_project_charters.id',
                        'trs_project_charters.project_id',
                        'trs_project_charters.impact_value',
                        'trs_project_charters.status'
                    )->orderByDesc('trs_project_charters.id');
                },
                'mappedProjects.reviewPcStatusImplementations' => function ($query) {
                    $query->select(
                        'trs_review_pc_status_implementation.id',
                        'trs_review_pc_status_implementation.project_id',
                        'trs_review_pc_status_implementation.review_status',
                        'trs_review_pc_status_implementation.start',
                        'trs_review_pc_status_implementation.end',
                        'trs_review_pc_status_implementation.year'
                    )->orderByDesc('id');
                },
                'mappedProjects.pcStatusImplementations' => function ($query) {
                    $query->select(
                        'trs_pc_status_implementation.id',
                        'trs_pc_status_implementation.project_id',
                        'trs_pc_status_implementation.status',
                        'trs_pc_status_implementation.month',
                        'trs_pc_status_implementation.year'
                    )->orderBy('year', 'asc')
                        ->orderByRaw(
                            "CASE LOWER(TRIM(COALESCE(trs_pc_status_implementation.month, '')))
                                WHEN 'januari' THEN 1
                                WHEN 'februari' THEN 2
                                WHEN 'maret' THEN 3
                                WHEN 'april' THEN 4
                                WHEN 'mei' THEN 5
                                WHEN 'juni' THEN 6
                                WHEN 'juli' THEN 7
                                WHEN 'agustus' THEN 8
                                WHEN 'september' THEN 9
                                WHEN 'oktober' THEN 10
                                WHEN 'november' THEN 11
                                WHEN 'desember' THEN 12
                                ELSE 0
                            END"
                        )
                        ->orderBy('id', 'asc');
                },
            ])
            ->get()
            ->flatMap(function ($initiative) {
                $coeName = $initiative->coe?->name ?? 'Unassigned';
                $latestReviewStatus = $this->latestReviewStatus($initiative);
                $latestPcLog = $this->latestPcLog($initiative);
                $latestPcStatus = $latestPcLog?->status;
                $latestPcMonth = $latestPcLog?->month;
                $latestPcYear = $latestPcLog?->year;

                $earliestPcLog = $this->earliestPcLog($initiative);
                $earliestPcMonth = $earliestPcLog?->month;
                $earliestPcYear = $earliestPcLog?->year;

                $projects = collect($initiative->mappedProjects ?? []);

                if ($projects->isEmpty()) {
                    return [[
                        'id' => $initiative->id,
                        'row_key' => sprintf('initiative-%d-project-none', $initiative->id),
                        'code' => $initiative->code,
                        'name' => $initiative->name,
                        'project_id' => null,
                        'project_code' => null,
                        'project_name' => null,
                        'version_status' => null,
                        'version_status_label' => null,
                        'impact_value' => '-',
                        'coe_name' => $coeName,
                        'latest_review_status' => $latestReviewStatus,
                        'latest_pc_status' => $latestPcStatus,
                        'latest_pc_month' => $latestPcMonth,
                        'latest_pc_year' => $latestPcYear,
                        'earliest_pc_month' => $earliestPcMonth,
                        'earliest_pc_year' => $earliestPcYear,
                        'status_logs' => [],
                    ]];
                }

                return $projects->flatMap(function ($project) use ($initiative, $coeName, $latestReviewStatus, $latestPcStatus, $latestPcMonth, $latestPcYear, $earliestPcMonth, $earliestPcYear) {
                    $statusLogs = collect($project->pcStatusImplementations ?? [])
                        ->map(fn($log) => [
                            'status' => $log->status,
                            'month' => $log->month,
                            'year' => $log->year,
                            'period' => trim("{$log->month} {$log->year}"),
                        ])
                        ->values()
                        ->toArray();

                    $charters = collect($project->charters ?? [])
                        ->filter()
                        ->sortByDesc('id')
                        ->values();

                    if ($charters->isEmpty()) {
                        $charter = $project->charter;
                        $charters = $charter ? collect([$charter]) : collect();
                    }

                    if ($charters->isEmpty()) {
                        return [[
                            'id' => $initiative->id,
                            'row_key' => sprintf(
                                'initiative-%d-project-%d-charter-none',
                                $initiative->id,
                                $project->id
                            ),
                            'code' => $initiative->code,
                            'name' => $initiative->name,
                                'project_id' => (int) $project->id,
                                'project_code' => $project->code,
                                'project_name' => $project->name,
                                'version_status' => null,
                                'version_status_label' => null,
                                'impact_value' => '-',
                                'coe_name' => $coeName,
                                'latest_review_status' => $latestReviewStatus,
                                'latest_pc_status' => $latestPcStatus,
                                'latest_pc_month' => $latestPcMonth,
                                'latest_pc_year' => $latestPcYear,
                                'earliest_pc_month' => $earliestPcMonth,
                                'earliest_pc_year' => $earliestPcYear,
                                'status_logs' => $statusLogs,
                        ]];
                    }

                    return $charters->map(function ($charter) use ($initiative, $project, $coeName, $latestReviewStatus, $latestPcStatus, $latestPcMonth, $latestPcYear, $earliestPcMonth, $earliestPcYear, $statusLogs) {
                        return [
                            'id' => $charter->id ?? $initiative->id,
                            'row_key' => sprintf(
                                'initiative-%d-project-%d-charter-%d',
                                $initiative->id,
                                $project->id,
                                $charter->id ?? 0
                            ),
                            'code' => $initiative->code,
                            'name' => $initiative->name,
                            'project_id' => (int) $project->id,
                            'project_code' => $project->code,
                            'project_name' => $project->name,
                            'version_status' => $charter->status !== null && $charter->status !== ''
                                ? (int) $charter->status
                                : null,
                            'version_status_label' => $this->versionStatusLabel($charter->status),
                            'impact_value' => $charter->impact_value ?? '-',
                            'coe_name' => $coeName,
                            'latest_review_status' => $latestReviewStatus,
                            'latest_pc_status' => $latestPcStatus,
                            'latest_pc_month' => $latestPcMonth,
                            'latest_pc_year' => $latestPcYear,
                            'earliest_pc_month' => $earliestPcMonth,
                            'earliest_pc_year' => $earliestPcYear,
                            'status_logs' => $statusLogs,
                        ];
                    });
                });
            })
            ->values();

        $props['valueCreationData'] = $valueCreationData;
        $props['valueCreationVersionOptions'] = $this->versionLegend($valueCreationData);

        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Index',
            $props
        );
    }

    private function latestReviewStatus(MstInitiative $initiative): ?string
    {
        return collect($initiative->mappedProjects ?? [])
            ->flatMap(static fn ($project) => $project->reviewPcStatusImplementations ?? collect())
            ->sortByDesc('id')
            ->first()?->review_status;
    }

    private function latestPcLog(MstInitiative $initiative): ?object
    {
        return collect($initiative->mappedProjects ?? [])
            ->flatMap(static fn ($project) => $project->pcStatusImplementations ?? collect())
            ->sort(fn ($left, $right) => $this->compareImplementationStatus($left, $right))
            ->last();
    }

    private function earliestPcLog(MstInitiative $initiative): ?object
    {
        return collect($initiative->mappedProjects ?? [])
            ->flatMap(static fn ($project) => $project->pcStatusImplementations ?? collect())
            ->sort(fn ($left, $right) => $this->compareImplementationStatus($left, $right))
            ->first();
    }

    private function latestPcStatus(MstInitiative $initiative): ?string
    {
        return $this->latestPcLog($initiative)?->status;
    }

    private function versionLegend(iterable $rows): array
    {
        $legend = [
            ['value' => '5', 'label' => 'Baseline'],
            ['value' => '4', 'label' => 'Approved'],
        ];

        $counts = [
            '5' => 0,
            '4' => 0,
        ];

        foreach ($rows as $row) {
            $statusId = $this->normalizeVersionStatus($row['version_status'] ?? null);

            if ($statusId === null || ! array_key_exists((string) $statusId, $counts)) {
                continue;
            }

            $counts[(string) $statusId]++;
        }

        return array_map(static function (array $item) use ($counts): array {
            return [
                'value' => $item['value'],
                'label' => $item['label'],
                'count' => $counts[$item['value']] ?? 0,
            ];
        }, $legend);
    }

    private function versionStatusLabel(mixed $status): ?string
    {
        $statusId = $this->normalizeVersionStatus($status);

        return match ($statusId) {
            5 => 'Baseline',
            4 => 'Approved',
            default => $statusId !== null ? (string) $statusId : null,
        };
    }

    private function normalizeVersionStatus(mixed $status): ?int
    {
        if ($status === null || $status === '') {
            return null;
        }

        $parsed = (int) $status;

        return in_array($parsed, [4, 5], true) ? $parsed : null;
    }

    private function compareImplementationStatus(mixed $left, mixed $right): int
    {
        $leftKey = $this->implementationStatusSortKey($left);
        $rightKey = $this->implementationStatusSortKey($right);

        return $leftKey <=> $rightKey;
    }

    private function implementationStatusSortKey(mixed $log): array
    {
        return [
            $this->normalizeYear($log?->year ?? null),
            $this->normalizeMonth($log?->month ?? null),
            $this->normalizeTimestamp($log?->created_at ?? null, $log?->updated_at ?? null),
            (int) ($log?->id ?? 0),
        ];
    }

    private function normalizeYear(mixed $value): int
    {
        $parsed = (int) trim((string) ($value ?? ''));

        return $parsed > 0 ? $parsed : PHP_INT_MIN;
    }

    private function normalizeMonth(mixed $value): int
    {
        static $monthOrder = [
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,
        ];

        $normalized = strtolower(trim((string) ($value ?? '')));

        return $monthOrder[$normalized] ?? 0;
    }

    private function normalizeTimestamp(mixed $createdAt, mixed $updatedAt): int
    {
        $candidates = [$createdAt, $updatedAt];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }

            $parsed = strtotime((string) $candidate);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        return PHP_INT_MIN;
    }
}
