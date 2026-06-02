<?php

namespace App\Services\ProgramImplementation;

use App\Models\TrsProject;

class ResourceManagementService
{
    private const TYPE_DIGITAL_INITIATIVE = 1;

    private const TYPE_IT_INITIATIVE = 2;

    public function getIndexProps(): array
    {
        $resourceRows = TrsProject::select([
                'trs_projects.id',
                'trs_projects.code',
                'trs_projects.name',
                'trs_projects.tipe_inisiative',
            ])
            ->where('trs_projects.tipe_inisiative', self::TYPE_IT_INITIATIVE)
            ->with([
                'projectCharters' => function ($query) {
                    $query->select([
                        'trs_project_charters.id',
                        'trs_project_charters.project_id',
                        'trs_project_charters.status',
                        'trs_project_charters.budget',
                        'trs_project_charters.key_personnel',
                        'trs_project_charters.impact_value',
                    ]);

                    $query->oldest('trs_project_charters.id');
                },
                'projectCharters.statusRef:id,name',
                'pcStatusImplementations' => function ($query) {
                    $query->select([
                        'trs_pc_status_implementation.id',
                        'trs_pc_status_implementation.project_id',
                        'trs_pc_status_implementation.status',
                        'trs_pc_status_implementation.month',
                        'trs_pc_status_implementation.year',
                        'trs_pc_status_implementation.created_at',
                        'trs_pc_status_implementation.updated_at',
                    ]);
                },
                'mapCrossFunctions.organization:id,name',
                'mappedInitiatives.coe:id,name',
            ])
            ->oldest('trs_projects.id')
            ->get()
            ->flatMap(fn (TrsProject $project): array => $this->mapProjectResources($project))
            ->values()
            ->all();

        return [
            'resourceProjects' => $resourceRows,
            'resourceSummary' => $this->buildResourceSummary($resourceRows),
            'resourceManagementFilters' => [
                'project' => 'all',
                'status' => 'all',
                'version' => 'all',
            ],
            'resourceManagementFilterOptions' => [
                'statuses' => $this->implementationStatusOptions(),
                'versions' => $this->versionOptions(),
            ],
        ];
    }

    private function mapProjectResources(TrsProject $project): array
    {
        $coe = $project->mappedInitiatives->first()?->coe;
        $coeName = $coe?->name ?? 'Unassigned';

        if ($project->projectCharters->isEmpty()) {
            return [[
                'id' => null,
                'row_key' => sprintf('project-%d', $project->id),
                'project_id' => (int) $project->id,
                'code' => $this->normalizeText($project->getAttribute('code')),
                'name' => $this->normalizeProjectName($project),
                'project_code' => $this->normalizeText($project->getAttribute('code')),
                'project_name' => $this->normalizeProjectName($project),
                'project_type' => (int) ($project->tipe_inisiative ?? 0),
                'project_type_label' => $this->typeLabel($project->tipe_inisiative),
                'coe_name' => $coeName,
                'status_id' => null,
                'version_status' => null,
                'version_status_label' => null,
                'status' => '-',
                'latest_implementation_status' => null,
                'latest_implementation_status_label' => null,
                'budget' => null,
                'key_personnel' => null,
                'key_personnel_display' => null,
                'impact_value' => null,
            ]];
        }

        return $project->projectCharters
            ->map(function ($charter) use ($project, $coeName): array {
                $latestLog = $this->latestPcLog($project);
                $earliestLog = $this->earliestPcLog($project);

                return [
                    'id' => (int) $charter->id,
                    'row_key' => sprintf('charter-%d', $charter->id),
                    'project_id' => (int) $project->id,
                    'code' => $this->normalizeText($project->getAttribute('code')),
                    'name' => $this->normalizeProjectName($project),
                    'project_code' => $this->normalizeText($project->getAttribute('code')),
                    'project_name' => $this->normalizeProjectName($project),
                    'project_type' => (int) ($project->tipe_inisiative ?? 0),
                    'project_type_label' => $this->typeLabel($project->tipe_inisiative),
                    'coe_name' => $coeName,
                    'status_id' => $charter->status !== null && $charter->status !== ''
                        ? (int) $charter->status
                        : null,
                    'version_status' => $charter->status !== null && $charter->status !== ''
                        ? (int) $charter->status
                        : null,
                    'version_status_label' => $this->versionStatusLabel($charter->status),
                    'status' => $this->normalizeStatusLabel(
                        $charter->statusRef?->name,
                        $charter->status,
                    ),
                    'latest_implementation_status' => $this->latestImplementationStatus($project),
                    'latest_implementation_status_label' => $this->latestImplementationStatusLabel($project),
                    'latest_pc_month' => $latestLog?->month,
                    'latest_pc_year' => $latestLog?->year,
                    'earliest_pc_month' => $earliestLog?->month,
                    'earliest_pc_year' => $earliestLog?->year,
                    'status_logs' => collect($project->pcStatusImplementations ?? [])
                        ->map(fn($log) => [
                            'status' => $log->status,
                            'month' => $log->month,
                            'year' => $log->year,
                            'period' => trim("{$log->month} {$log->year}"),
                        ])
                        ->values()
                        ->toArray(),
                    'budget' => $this->normalizeText($charter->budget),
                    'key_personnel' => $this->normalizeText($charter->key_personnel),
                    'key_personnel_display' => $this->buildKeyPersonnelDisplay(
                        $charter->key_personnel,
                        $project,
                        $charter->status,
                    ),
                    'impact_value' => $this->normalizeText($charter->impact_value),
                ];
            })
            ->values()
            ->all();
    }

    private function latestPcLog(TrsProject $project): ?object
    {
        return collect($project->pcStatusImplementations ?? [])
            ->sort(fn ($left, $right) => $this->compareImplementationStatus($left, $right))
            ->last();
    }

    private function earliestPcLog(TrsProject $project): ?object
    {
        return collect($project->pcStatusImplementations ?? [])
            ->sort(fn ($left, $right) => $this->compareImplementationStatus($left, $right))
            ->first();
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

    private function buildResourceSummary(array $resourceRows): array
    {
        return [
            'total_projects' => collect($resourceRows)->pluck('project_id')->unique()->count(),
            'total_charters' => count($resourceRows),
        ];
    }

    private function normalizeProjectName(TrsProject $project): string
    {
        $name = trim((string) ($project->name ?? ''));

        if ($name !== '') {
            return $name;
        }

        $code = trim((string) ($project->code ?? ''));

        return $code !== '' ? $code : '-';
    }

    private function normalizeText(mixed $value): ?string
    {
        $normalized = trim(str_replace(["\r\n", "\r"], "\n", (string) $value));

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeStatusLabel(mixed $statusName, mixed $statusId): string
    {
        $normalizedStatusName = trim(str_replace('_', ' ', (string) $statusName));

        if ($normalizedStatusName !== '') {
            return ucwords($normalizedStatusName);
        }

        return $statusId !== null && $statusId !== ''
            ? sprintf('Status %s', $statusId)
            : '-';
    }

    private function latestImplementationStatus(TrsProject $project): ?string
    {
        $latest = collect($project->pcStatusImplementations ?? [])
            ->filter(static fn ($log) => trim((string) ($log->status ?? '')) !== '')
            ->last();

        $status = trim((string) ($latest?->status ?? ''));

        return $status !== '' ? $status : null;
    }

    private function latestImplementationStatusLabel(TrsProject $project): ?string
    {
        $status = $this->latestImplementationStatus($project);
        if ($status === null) {
            return null;
        }

        return $this->normalizeImplementationStatus($status);
    }

    private function normalizeImplementationStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match (true) {
            str_contains($normalized, 'on track') => 'On Track',
            str_contains($normalized, 'at risk') => 'At Risk',
            str_contains($normalized, 'not signed') => 'Not Signed',
            str_contains($normalized, 'not started') => 'Not Started',
            str_contains($normalized, 'done') || str_contains($normalized, 'complete') => 'Done',
            default => ucwords($normalized),
        };
    }

    private function versionStatusLabel(mixed $status): ?string
    {
        $statusId = (int) ($status ?? 0);

        return match ($statusId) {
            4 => 'Approved',
            5 => 'Baseline',
            default => $statusId > 0 ? (string) $statusId : null,
        };
    }

    private function implementationStatusOptions(): array
    {
        return [
            ['value' => 'On Track', 'label' => 'On Track'],
            ['value' => 'At Risk', 'label' => 'At Risk'],
            ['value' => 'Not Signed', 'label' => 'Not Signed'],
            ['value' => 'Not Started', 'label' => 'Not Started'],
            ['value' => 'Done', 'label' => 'Done'],
        ];
    }

    private function versionOptions(): array
    {
        return [
            ['value' => '4', 'label' => 'Approved'],
            ['value' => '5', 'label' => 'Baseline'],
        ];
    }

    private function buildKeyPersonnelDisplay(mixed $keyPersonnel, TrsProject $project, mixed $charterStatus): ?string
    {
        $normalizedKeyPersonnel = $this->normalizeText($keyPersonnel);
        $crossFunctionStatus = $this->crossFunctionStatusForCharter($charterStatus);
        $crossFunctionNames = $this->crossFunctionOrganizationNames($project, $crossFunctionStatus);

        $sections = [];

        if ($normalizedKeyPersonnel !== null) {
            $sections[] = $normalizedKeyPersonnel;
        }

        if ($crossFunctionNames !== []) {
            if ($sections !== []) {
                $sections[] = '';
            }

            $sections[] = sprintf(
                'Mapping (%s):',
                $this->crossFunctionStatusLabel($crossFunctionStatus),
            );
            $sections[] = implode("\n", array_map(
                static fn (string $name): string => sprintf('• %s', $name),
                $crossFunctionNames,
            ));
        }

        return $sections !== [] ? implode("\n", $sections) : null;
    }

    private function crossFunctionStatusForCharter(mixed $charterStatus): int
    {
        return (int) $charterStatus === 4 ? 2 : 1;
    }

    private function crossFunctionStatusLabel(int $crossFunctionStatus): string
    {
        return $crossFunctionStatus === 2 ? 'Approved' : 'Baseline';
    }

    private function crossFunctionOrganizationNames(TrsProject $project, int $crossFunctionStatus): array
    {
        return collect($project->mapCrossFunctions ?? [])
            ->filter(static fn ($map) => (int) ($map->status ?? 0) === $crossFunctionStatus)
            ->map(static fn ($map) => trim((string) ($map->organization?->name ?? '')))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function typeOptions(): array
    {
        return [
            [
                'value' => (string) self::TYPE_DIGITAL_INITIATIVE,
                'label' => $this->typeLabel(self::TYPE_DIGITAL_INITIATIVE),
            ],
            [
                'value' => (string) self::TYPE_IT_INITIATIVE,
                'label' => $this->typeLabel(self::TYPE_IT_INITIATIVE),
            ],
        ];
    }

    private function typeLabel(mixed $type): string
    {
        return match ((int) $type) {
            self::TYPE_DIGITAL_INITIATIVE => 'Digital Initiative',
            self::TYPE_IT_INITIATIVE => 'IT Initiative',
            default => '-',
        };
    }
}
