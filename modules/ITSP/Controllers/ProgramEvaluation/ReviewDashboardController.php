<?php

namespace Modules\ITSP\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\InitiativeStatus;
use Modules\ITSP\Models\MstInitiative;
use App\Models\TrsOrganization;
use Modules\ITSP\Models\TrsProjectCharter;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Response;

class ReviewDashboardController extends Controller
{
    private const PROJECT_STATUS_ORDER = [
        'On Track',
        'At Risk',
        'Delayed',
        'Done',
        'On Progress',
        'On Review',
        'Not Started',
        'Not Signed',
        'Belum Ada Status',
    ];

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
    
    public function index(): Response
    {
        $data = $this->getDashboardData();

        return inertia('ProgramEvaluation/ReviewApproval/Index', $data);
    }

    public function summary(): Response
    {
        $data = $this->getDashboardData();

        return inertia('ProgramEvaluation/ReviewDashboard/Summary', $data);
    }

    private function getDashboardData(): array
    {
        $organizationCodeLookup = $this->buildOrganizationCodeLookup();
        $organizationDisplayLookup = $this->buildOrganizationDisplayLookup();

        $rows = MstInitiative::query()
            ->select(['id', 'code', 'name', 'tipe_initiative', 'coe_id'])
            ->whereIn('tipe_initiative', [1, 2])
            ->whereHas('mappedProjects')
            ->with([
                'coe:id,name',
                'mappedProjects' => static fn ($query) => $query
                    ->with([
                        'mapPicProject.ownerOrganization',
                        'mapPicProject.leaderOrganization',
                        'projectCharters' => static fn ($charterQuery) => $charterQuery
                            ->orderByDesc('id'),
                        'projectStatusHistories' => static fn ($historyQuery) => $historyQuery
                            ->orderByDesc('tanggal')
                            ->orderByDesc('id'),
                        'pcStatusImplementations' => static fn ($statusQuery) => $statusQuery
                            ->select([
                                'trs_pc_status_implementation.id',
                                'trs_pc_status_implementation.project_id',
                                'trs_pc_status_implementation.month',
                                'trs_pc_status_implementation.year',
                                'trs_pc_status_implementation.status',
                                'trs_pc_status_implementation.created_at',
                                'trs_pc_status_implementation.updated_at',
                            ])
                            ->orderByDesc('year')
                            ->orderByRaw("CASE
                                WHEN trs_pc_status_implementation.month = 'Desember' THEN 12
                                WHEN trs_pc_status_implementation.month = 'November' THEN 11
                                WHEN trs_pc_status_implementation.month = 'Oktober' THEN 10
                                WHEN trs_pc_status_implementation.month = 'September' THEN 9
                                WHEN trs_pc_status_implementation.month = 'Agustus' THEN 8
                                WHEN trs_pc_status_implementation.month = 'Juli' THEN 7
                                WHEN trs_pc_status_implementation.month = 'Juni' THEN 6
                                WHEN trs_pc_status_implementation.month = 'Mei' THEN 5
                                WHEN trs_pc_status_implementation.month = 'April' THEN 4
                                WHEN trs_pc_status_implementation.month = 'Maret' THEN 3
                                WHEN trs_pc_status_implementation.month = 'Februari' THEN 2
                                WHEN trs_pc_status_implementation.month = 'Januari' THEN 1
                                ELSE 0 END DESC")
                            ->orderByDesc('id'),
                    ])
                    ->orderBy('trs_projects.code')
                    ->orderBy('trs_projects.id'),
            ])
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('name')
            ->get()
            ->map(function (MstInitiative $initiative, int $index) use ($organizationCodeLookup, $organizationDisplayLookup): array {
                $projects = $initiative->mappedProjects ?? collect();
                $baselineDate = $this->resolveStatusDate($projects, InitiativeStatus::BASELINE);
                $approveDate = $this->resolveStatusDate($projects, InitiativeStatus::APPROVE);
                $processMonthValue = $this->resolveProcessMonthValue($baselineDate, $approveDate);
                $projectStatusLogs = $this->resolveProjectStatusLogs($projects);
                $projectStatusPeriods = collect($projectStatusLogs)
                    ->pluck('period')
                    ->filter(static fn ($period) => trim((string) $period) !== '')
                    ->unique()
                    ->values()
                    ->all();
                $latestProjectStatus = $this->resolveLatestProjectStatusState($projectStatusLogs);
                $baselineCharter = $this->resolveLatestProjectCharterByStatus($projects, 5);
                $approvedCharter = $this->resolveLatestProjectCharterByStatus($projects, 4);

                // Get owner and leader from the latest project charter of any mapped project
                $latestCharter = $projects
                    ->flatMap(fn ($p) => $p->projectCharters ?? collect())
                    ->sortByDesc('id')
                    ->first();
                $latestCharterProject = $latestCharter
                    ? $projects->firstWhere('id', (int) $latestCharter->project_id)
                    : null;

                $projectOwner = $latestCharter?->owner ?? '-';
                $projectLeader = $latestCharter?->leader ?? '-';
                $projectOwnerCode = $this->resolveOrganizationCode($projectOwner, $organizationCodeLookup);
                $projectLeaderCode = $this->resolveOrganizationCode($projectLeader, $organizationCodeLookup);
                
                $projectLeaderDisplay = $projectLeader;
                if ($projectLeaderCode && isset($organizationDisplayLookup[$projectLeaderCode])) {
                    $projectLeaderDisplay = $organizationDisplayLookup[$projectLeaderCode];
                }

                [$projectLeaderParentCode, $projectLeaderParent] = $this->resolveParentOrganization(
                    $projectLeaderCode,
                    $organizationDisplayLookup,
                );
                $projectLeaderParents = $this->resolveParentOrganizationsByLevel(
                    $projectLeaderCode,
                    $organizationDisplayLookup,
                );

                $baselineCharterData = $this->buildProjectCharterVersionData(
                    $baselineCharter,
                    $latestCharterProject?->name,
                    $organizationCodeLookup,
                    $organizationDisplayLookup,
                );
                $approvedCharterData = $this->buildProjectCharterVersionData(
                    $approvedCharter,
                    $latestCharterProject?->name,
                    $organizationCodeLookup,
                    $organizationDisplayLookup,
                );

                // Restructured data (from TrsMapPicProject)
                $latestProjectWithMap = $projects
                    ->filter(fn ($p) => $p->mapPicProject)
                    ->sortByDesc('id')
                    ->first();

                $projectOwnerRestructure = $latestProjectWithMap?->mapPicProject?->ownerOrganization?->jabatan ?? '-';
                $projectLeaderRestructure = $latestProjectWithMap?->mapPicProject?->leaderOrganization?->jabatan ?? '-';
                $projectOwnerRestructureCode = trim((string) ($latestProjectWithMap?->mapPicProject?->ownerOrganization?->code ?? '')) ?: null;
                $projectLeaderRestructureCode = trim((string) ($latestProjectWithMap?->mapPicProject?->leaderOrganization?->code ?? '')) ?: null;

                $projectLeaderRestructureDisplay = $projectLeaderRestructure;
                if ($projectLeaderRestructureCode && isset($organizationDisplayLookup[$projectLeaderRestructureCode])) {
                    $projectLeaderRestructureDisplay = $organizationDisplayLookup[$projectLeaderRestructureCode];
                }

                [$projectLeaderRestructureParentCode, $projectLeaderRestructureParent] = $this->resolveParentOrganization(
                    $projectLeaderRestructureCode,
                    $organizationDisplayLookup,
                );
                $projectLeaderRestructureParents = $this->resolveParentOrganizationsByLevel(
                    $projectLeaderRestructureCode,
                    $organizationDisplayLookup,
                );

                return [
                    'no' => $index + 1,
                    'initiative_id' => (int) $initiative->id,
                    'project_id' => $latestCharterProject?->id,
                    'building_block_type' => trim((string) ($initiative->coe?->name ?? '')) !== '' ? $initiative->coe->name : '-',
                    'initiative_name' => trim((string) $initiative->name) !== '' ? $initiative->name : '-',
                    'project_charter_name' => trim((string) ($latestCharterProject?->name ?? '')) !== '' ? $latestCharterProject->name : null,
                    'project_charter_name_baseline' => $baselineCharterData['project_charter_name'],
                    'project_charter_name_approved' => $approvedCharterData['project_charter_name'],
                    'project_owner_baseline' => $baselineCharterData['project_owner'],
                    'project_owner_baseline_code' => $baselineCharterData['project_owner_code'],
                    'project_leader_baseline' => $baselineCharterData['project_leader'],
                    'project_leader_baseline_code' => $baselineCharterData['project_leader_code'],
                    'project_owner_approved' => $approvedCharterData['project_owner'],
                    'project_owner_approved_code' => $approvedCharterData['project_owner_code'],
                    'project_leader_approved' => $approvedCharterData['project_leader'],
                    'project_leader_approved_code' => $approvedCharterData['project_leader_code'],
                    'has_baseline' => $baselineCharter !== null,
                    'has_approved' => $approvedCharter !== null,
                    'baseline_date' => $this->formatDate($baselineDate),
                    'approve_date' => $this->formatDate($approveDate),
                    'process_month_value' => $processMonthValue,
                    'process_month' => $this->formatProcessMonth($processMonthValue),
                    'latest_project_status' => $latestProjectStatus['status'],
                    'latest_project_status_period' => $latestProjectStatus['period'],
                    'project_status_logs' => $projectStatusLogs,
                    'project_status_periods' => $projectStatusPeriods,
                    'project_owner' => $projectOwner,
                    'project_leader' => $projectLeaderDisplay,
                    'project_owner_code' => $projectOwnerCode,
                    'project_leader_code' => $projectLeaderCode,
                    'project_leader_parent_code' => $projectLeaderParentCode,
                    'project_leader_parent' => $projectLeaderParent,
                    'project_leader_parent_level2' => $projectLeaderParents[2],
                    'project_leader_parent_level3' => $projectLeaderParents[3],
                    'project_leader_parent_level4' => $projectLeaderParents[4],
                    'project_leader_parent_level5' => $projectLeaderParents[5],
                    'project_leader_parent_level6' => $projectLeaderParents[6],
                    'project_owner_restructure' => $projectOwnerRestructure,
                    'project_leader_restructure' => $projectLeaderRestructureDisplay,
                    'project_owner_restructure_code' => $projectOwnerRestructureCode,
                    'project_leader_restructure_code' => $projectLeaderRestructureCode,
                    'project_leader_restructure_parent_code' => $projectLeaderRestructureParentCode,
                    'project_leader_restructure_parent' => $projectLeaderRestructureParent,
                    'project_leader_restructure_parent_level2' => $projectLeaderRestructureParents[2],
                    'project_leader_restructure_parent_level3' => $projectLeaderRestructureParents[3],
                    'project_leader_restructure_parent_level4' => $projectLeaderRestructureParents[4],
                    'project_leader_restructure_parent_level5' => $projectLeaderRestructureParents[5],
                    'project_leader_restructure_parent_level6' => $projectLeaderRestructureParents[6],
                ];
            })
            ->values();

        $withProjectStatus = $rows->filter(
            static fn (array $row): bool => ($row['latest_project_status'] ?? null) !== 'Belum Ada Status',
        )->count();
        $statusBreakdown = $this->buildStatusBreakdown($rows);

        return [
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'buildingBlock' => $rows->count(),
                'withProjectStatus' => $withProjectStatus,
                'withoutProjectStatus' => $rows->count() - $withProjectStatus,
                'statusBreakdown' => $statusBreakdown,
            ],
        ];
    }

    private function buildOrganizationCodeLookup(): array
    {
        return TrsOrganization::query()
            ->select(['code', 'name', 'alias', 'jabatan', 'pejabat'])
            ->get()
            ->flatMap(function (TrsOrganization $organization): array {
                $code = trim((string) ($organization->code ?? ''));

                if ($code === '') {
                    return [];
                }

                $keys = [
                    $organization->name,
                    $organization->alias,
                    $organization->jabatan,
                    $organization->pejabat,
                    $organization->code,
                ];

                return collect($keys)
                    ->filter(static fn ($value) => trim((string) $value) !== '')
                    ->mapWithKeys(function ($value) use ($code): array {
                        return [mb_strtolower(trim((string) $value)) => $code];
                    })
                    ->all();
            })
            ->all();
    }

    private function buildOrganizationDisplayLookup(): array
    {
        return TrsOrganization::query()
            ->select(['code', 'jabatan', 'name', 'alias', 'pejabat'])
            ->get()
            ->mapWithKeys(function (TrsOrganization $organization): array {
                $code = trim((string) ($organization->code ?? ''));

                if ($code === '') {
                    return [];
                }

                $displayName = trim((string) ($organization->jabatan ?? ''));

                if ($displayName === '') {
                    $displayName = trim((string) ($organization->name ?? ''));
                }

                if ($displayName === '') {
                    $displayName = trim((string) ($organization->alias ?? ''));
                }

                if ($displayName === '') {
                    $displayName = $code;
                }

                $pejabat = trim((string) ($organization->pejabat ?? ''));
                if ($pejabat !== '' && $pejabat !== '-') {
                    $displayName = $displayName . ' - ' . $pejabat;
                }

                return [$code => $displayName];
            })
            ->all();
    }

    private function resolveOrganizationCode(?string $label, array $organizationCodeLookup): ?string
    {
        $normalizedLabel = mb_strtolower(trim((string) $label));

        if ($normalizedLabel === '' || $normalizedLabel === '-') {
            return null;
        }

        return $organizationCodeLookup[$normalizedLabel] ?? null;
    }

    private function resolveParentOrganization(?string $code, array $organizationDisplayLookup): array
    {
        $normalizedCode = trim((string) $code);

        if ($normalizedCode === '') {
            return [null, null];
        }

        $digits = str_split($normalizedCode);
        $lastNonZeroIndex = null;

        for ($index = count($digits) - 1; $index >= 0; $index--) {
            if ($digits[$index] !== '0') {
                $lastNonZeroIndex = $index;
                break;
            }
        }

        if ($lastNonZeroIndex === null || $lastNonZeroIndex === 0) {
            return [null, null];
        }

        $digits[$lastNonZeroIndex] = '0';
        for ($index = $lastNonZeroIndex + 1; $index < count($digits); $index++) {
            $digits[$index] = '0';
        }

        $parentCode = implode('', $digits);
        $parentLabel = $organizationDisplayLookup[$parentCode] ?? null;

        return [$parentCode, $parentLabel];
    }

    private function resolveParentOrganizationsByLevel(?string $code, array $organizationDisplayLookup): array
    {
        $normalizedCode = trim((string) $code);
        $parents = [
            2 => null,
            3 => null,
            4 => null,
            5 => null,
            6 => null,
        ];

        if ($normalizedCode === '' || strlen($normalizedCode) !== 7) {
            return $parents;
        }

        $digits = str_split($normalizedCode);
        $lastNonZeroIndex = null;

        for ($index = count($digits) - 1; $index >= 0; $index--) {
            if ($digits[$index] !== '0') {
                $lastNonZeroIndex = $index;
                break;
            }
        }

        if ($lastNonZeroIndex === null) {
            return $parents;
        }

        for ($level = 2; $level <= 6; $level++) {
            if ($level < $lastNonZeroIndex) {
                $parentDigits = $digits;
                for ($index = $level + 1; $index < count($digits); $index++) {
                    $parentDigits[$index] = '0';
                }
                $parentCode = implode('', $parentDigits);
                $parents[$level] = $organizationDisplayLookup[$parentCode] ?? null;
            }
        }

        return $parents;
    }

    private function resolveProjectStatusLogs(Collection $projects): array
    {
        return $projects
            ->flatMap(static fn ($project) => $project->pcStatusImplementations ?? collect())
            ->filter(static fn ($history) => trim((string) ($history->status ?? '')) !== '')
            ->sort(fn ($left, $right) => $this->compareProjectStatusLogs($left, $right))
            ->map(function ($history): array {
                return [
                    'status' => $this->normalizeProjectStatus((string) ($history->status ?? '')),
                    'period' => $this->resolveProjectStatusPeriodLabel($history),
                    'month' => trim((string) ($history->month ?? '')),
                    'year' => trim((string) ($history->year ?? '')),
                    'project_id' => (int) ($history->project_id ?? 0),
                    'id' => (int) ($history->id ?? 0),
                ];
            })
            ->filter(static fn (array $history): bool => $history['status'] !== null && trim((string) ($history['period'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function resolveLatestProjectCharterByStatus(Collection $projects, int $status): ?TrsProjectCharter
    {
        return $projects
            ->flatMap(static fn ($project) => $project->projectCharters ?? collect())
            ->filter(static fn ($charter) => (int) ($charter->status ?? 0) === $status)
            ->sortByDesc('id')
            ->first();
    }

    private function buildProjectCharterVersionData(
        ?TrsProjectCharter $charter,
        ?string $fallbackProjectName,
        array $organizationCodeLookup,
        array $organizationDisplayLookup,
    ): array {
        $projectName = trim((string) ($fallbackProjectName ?? ''));
        if (! $charter) {
            return [
                'project_charter_name' => $projectName !== '' ? $projectName : null,
                'project_owner' => null,
                'project_owner_code' => null,
                'project_leader' => null,
                'project_leader_code' => null,
            ];
        }

        $owner = trim((string) ($charter?->owner ?? '')) ?: '-';
        $leader = trim((string) ($charter?->leader ?? '')) ?: '-';
        $ownerCode = $this->resolveOrganizationCode($owner, $organizationCodeLookup);
        $leaderCode = $this->resolveOrganizationCode($leader, $organizationCodeLookup);

        $leaderDisplay = $leader;
        if ($leaderCode && isset($organizationDisplayLookup[$leaderCode])) {
            $leaderDisplay = $organizationDisplayLookup[$leaderCode];
        }

        return [
            'project_charter_name' => $projectName !== '' ? $projectName : null,
            'project_owner' => $owner,
            'project_owner_code' => $ownerCode,
            'project_leader' => $leaderDisplay,
            'project_leader_code' => $leaderCode,
        ];
    }

    private function resolveLatestProjectStatusState(array $projectStatusLogs): array
    {
        $latestStatusLog = ! empty($projectStatusLogs)
            ? $projectStatusLogs[array_key_last($projectStatusLogs)]
            : null;

        if ($latestStatusLog) {
            return [
                'status' => $latestStatusLog['status'],
                'period' => $latestStatusLog['period'],
            ];
        }

        return [
            'status' => 'Belum Ada Status',
            'period' => null,
        ];
    }

    private function buildStatusBreakdown(Collection $rows): array
    {
        $counts = $rows->reduce(function (array $carry, array $row): array {
            $status = $this->normalizeProjectStatus((string) ($row['latest_project_status'] ?? ''))
                ?? 'Belum Ada Status';

            $carry[$status] = ($carry[$status] ?? 0) + 1;

            return $carry;
        }, []);

        $total = max(1, $rows->count());

        $orderedRows = collect(self::PROJECT_STATUS_ORDER)
            ->map(fn (string $status): ?array => isset($counts[$status])
                ? [
                    'status' => $status,
                    'count' => $counts[$status],
                    'percentage' => round(($counts[$status] / $total) * 100, 1),
                ]
                : null)
            ->filter();

        $remainingRows = collect($counts)
            ->except(self::PROJECT_STATUS_ORDER)
            ->map(fn (int $count, string $status): array => [
                'status' => $status,
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 1),
            ])
            ->sortByDesc('count')
            ->values();

        return $orderedRows
            ->concat($remainingRows)
            ->values()
            ->all();
    }

    private function resolveStatusDate(Collection $projects, int $statusId): ?Carbon
    {
        $latest = $projects
            ->flatMap(static fn ($project) => $project->projectCharters ?? collect())
            ->filter(static fn ($charter) => (int) $charter->status === $statusId)
            ->sortByDesc('id')
            ->first();

        if (! $latest || ! $latest->tgl_dokumen) {
            return null;
        }

        return Carbon::parse($latest->tgl_dokumen);
    }

    private function formatDate(?Carbon $date): ?string
    {
        return $date?->translatedFormat('d M Y');
    }

    private function resolveProcessMonthValue(?Carbon $baselineDate, ?Carbon $approveDate): ?int
    {
        if (! $baselineDate || ! $approveDate) {
            return null;
        }

        if ($approveDate->lt($baselineDate)) {
            return null;
        }

        // Subtract absolute month count to match frontend logic (e.g., Oct - Feb = 8)
        return ($approveDate->year * 12 + $approveDate->month) - ($baselineDate->year * 12 + $baselineDate->month);
    }

    private function formatProcessMonth(?int $months): ?string
    {
        if ($months === null) {
            return null;
        }

        return (string) $months;
    }

    private function normalizeProjectStatus(?string $rawStatus): ?string
    {
        $value = strtolower(trim((string) $rawStatus));

        if ($value === '') {
            return null;
        }

        return match (true) {
            $value === 'on track' => 'On Track',
            $value === 'at risk' => 'At Risk',
            $value === 'delayed' => 'Delayed',
            $value === 'done', $value === 'completed' => 'Done',
            $value === 'on progress', $value === 'on progres', $value === 'in progress' => 'On Progress',
            $value === 'on review' => 'On Review',
            $value === 'not started' => 'Not Started',
            $value === 'not signed' => 'Not Signed',
            default => ucwords($value),
        };
    }

    private function compareProjectStatusLogs(mixed $left, mixed $right): int
    {
        $leftYear = (int) ($left->year ?? 0);
        $rightYear = (int) ($right->year ?? 0);

        if ($leftYear !== $rightYear) {
            return $leftYear <=> $rightYear;
        }

        $leftStartOrder = $this->monthOrderValue($left->start ?? null);
        $rightStartOrder = $this->monthOrderValue($right->start ?? null);

        if ($leftStartOrder !== $rightStartOrder) {
            return $leftStartOrder <=> $rightStartOrder;
        }

        $leftEndOrder = $this->monthOrderValue($left->end ?? null);
        $rightEndOrder = $this->monthOrderValue($right->end ?? null);

        if ($leftEndOrder !== $rightEndOrder) {
            return $leftEndOrder <=> $rightEndOrder;
        }

        $leftTimestamp = max(
            strtotime((string) ($left->updated_at ?? '')) ?: 0,
            strtotime((string) ($left->created_at ?? '')) ?: 0,
        );
        $rightTimestamp = max(
            strtotime((string) ($right->updated_at ?? '')) ?: 0,
            strtotime((string) ($right->created_at ?? '')) ?: 0,
        );

        if ($leftTimestamp !== $rightTimestamp) {
            return $leftTimestamp <=> $rightTimestamp;
        }

        return (int) ($left->id ?? 0) <=> (int) ($right->id ?? 0);
    }

    private function monthOrderValue(?string $month): int
    {
        $normalized = trim((string) $month);

        if ($normalized === '') {
            return 0;
        }

        if (is_numeric($normalized)) {
            $monthNumber = (int) $normalized;

            return $monthNumber >= 1 && $monthNumber <= 12 ? $monthNumber : 0;
        }

        $index = array_search($normalized, self::MONTHS_ORDER, true);

        return $index === false ? 0 : $index + 1;
    }

    private function resolveProjectStatusPeriodLabel(mixed $statusLog): ?string
    {
        $month = trim((string) ($statusLog->month ?? ''));
        $year = trim((string) ($statusLog->year ?? ''));

        if ($month === '') {
            return null;
        }

        if (is_numeric($month)) {
            $monthNumber = (int) $month;
            $month = $monthNumber >= 1 && $monthNumber <= 12
                ? self::MONTHS_ORDER[$monthNumber - 1]
                : (string) $monthNumber;
        }

        if ($year !== '') {
            return sprintf('%s %s', $month, $year);
        }

        return $month;
    }
}
