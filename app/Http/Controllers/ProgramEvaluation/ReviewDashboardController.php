<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\TrsOrganization;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Response;

class ReviewDashboardController extends Controller
{
    private const REVIEW_STATUS_ORDER = [
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

        return inertia('ProgramEvaluation/ReviewApproval', $data);
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
                'latestStatusImplementation' => static fn ($query) => $query
                    ->select([
                        'trs_status_implementation.id',
                        'trs_status_implementation.initiative_id',
                        'trs_status_implementation.review_status',
                    ]),
                'mappedProjects' => static fn ($query) => $query
                    ->with([
                        'mapPicProject.ownerOrganization',
                        'mapPicProject.leaderOrganization',
                        'projectCharters' => static fn ($charterQuery) => $charterQuery
                            ->orderByDesc('id'),
                        'projectStatusHistories' => static fn ($historyQuery) => $historyQuery
                            ->orderByDesc('tanggal')
                            ->orderByDesc('id'),
                        'reviewPcStatusImplementations' => static fn ($reviewQuery) => $reviewQuery
                            ->orderByDesc('year')
                            ->orderByDesc('updated_at')
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
                $latestReviewState = $this->resolveLatestReviewState($initiative, $projects);

                // Get owner and leader from the latest project charter of any mapped project
                $latestCharter = $projects
                    ->flatMap(fn ($p) => $p->projectCharters ?? collect())
                    ->sortByDesc('id')
                    ->first();

                $projectOwner = $latestCharter?->owner ?? '-';
                $projectLeader = $latestCharter?->leader ?? '-';
                $projectOwnerCode = $this->resolveOrganizationCode($projectOwner, $organizationCodeLookup);
                $projectLeaderCode = $this->resolveOrganizationCode($projectLeader, $organizationCodeLookup);
                [$projectLeaderParentCode, $projectLeaderParent] = $this->resolveParentOrganization(
                    $projectLeaderCode,
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
                [$projectLeaderRestructureParentCode, $projectLeaderRestructureParent] = $this->resolveParentOrganization(
                    $projectLeaderRestructureCode,
                    $organizationDisplayLookup,
                );

                return [
                    'no' => $index + 1,
                    'initiative_id' => (int) $initiative->id,
                    'building_block_type' => trim((string) ($initiative->coe?->name ?? '')) !== '' ? $initiative->coe->name : '-',
                    'initiative_name' => trim((string) $initiative->name) !== '' ? $initiative->name : '-',
                    'baseline_date' => $this->formatDate($baselineDate),
                    'approve_date' => $this->formatDate($approveDate),
                    'process_month_value' => $processMonthValue,
                    'process_month' => $this->formatProcessMonth($processMonthValue),
                    'latest_review_status' => $latestReviewState['status'],
                    'latest_review_period' => $latestReviewState['period'],
                    'project_owner' => $projectOwner,
                    'project_leader' => $projectLeader,
                    'project_owner_code' => $projectOwnerCode,
                    'project_leader_code' => $projectLeaderCode,
                    'project_leader_parent_code' => $projectLeaderParentCode,
                    'project_leader_parent' => $projectLeaderParent,
                    'project_owner_restructure' => $projectOwnerRestructure,
                    'project_leader_restructure' => $projectLeaderRestructure,
                    'project_owner_restructure_code' => $projectOwnerRestructureCode,
                    'project_leader_restructure_code' => $projectLeaderRestructureCode,
                    'project_leader_restructure_parent_code' => $projectLeaderRestructureParentCode,
                    'project_leader_restructure_parent' => $projectLeaderRestructureParent,
                ];
            })
            ->values();

        $withReviewStatus = $rows->filter(
            static fn (array $row): bool => ($row['latest_review_status'] ?? null) !== 'Belum Ada Status',
        )->count();
        $statusBreakdown = $this->buildStatusBreakdown($rows);

        return [
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'buildingBlock' => $rows->count(),
                'withReviewStatus' => $withReviewStatus,
                'withoutReviewStatus' => $rows->count() - $withReviewStatus,
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
            ->select(['code', 'jabatan', 'name', 'alias'])
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

    private function resolveLatestReviewState(MstInitiative $initiative, Collection $projects): array
    {
        $latestReviewLog = $projects
            ->flatMap(static fn ($project) => $project->reviewPcStatusImplementations ?? collect())
            ->filter(static fn ($history) => trim((string) ($history->review_status ?? '')) !== '')
            ->sort(fn ($left, $right) => $this->compareReviewLogs($left, $right))
            ->last();

        if ($latestReviewLog) {
            return [
                'status' => $this->normalizeReviewStatus((string) ($latestReviewLog->review_status ?? '')),
                'period' => $this->resolveReviewPeriodLabel($latestReviewLog),
            ];
        }

        $fallbackStatus = $this->normalizeReviewStatus(
            (string) ($initiative->latestStatusImplementation?->review_status ?? ''),
        );

        return [
            'status' => $fallbackStatus ?? 'Belum Ada Status',
            'period' => null,
        ];
    }

    private function buildStatusBreakdown(Collection $rows): array
    {
        $counts = $rows->reduce(function (array $carry, array $row): array {
            $status = $this->normalizeReviewStatus((string) ($row['latest_review_status'] ?? ''))
                ?? 'Belum Ada Status';

            $carry[$status] = ($carry[$status] ?? 0) + 1;

            return $carry;
        }, []);

        $total = max(1, $rows->count());

        $orderedRows = collect(self::REVIEW_STATUS_ORDER)
            ->map(fn (string $status): ?array => isset($counts[$status])
                ? [
                    'status' => $status,
                    'count' => $counts[$status],
                    'percentage' => round(($counts[$status] / $total) * 100, 1),
                ]
                : null)
            ->filter();

        $remainingRows = collect($counts)
            ->except(self::REVIEW_STATUS_ORDER)
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

    private function normalizeReviewStatus(?string $rawStatus): ?string
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

    private function compareReviewLogs(mixed $left, mixed $right): int
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

        $index = array_search($normalized, self::MONTHS_ORDER, true);

        return $index === false ? 0 : $index + 1;
    }

    private function resolveReviewPeriodLabel(mixed $reviewLog): ?string
    {
        $start = trim((string) ($reviewLog->start ?? ''));
        $end = trim((string) ($reviewLog->end ?? ''));
        $year = trim((string) ($reviewLog->year ?? ''));

        if ($start === '') {
            return null;
        }

        if ($end !== '' && $year !== '') {
            return sprintf('%s - %s %s', $start, $end, $year);
        }

        if ($year !== '') {
            return sprintf('%s %s', $start, $year);
        }

        return $start;
    }
}
