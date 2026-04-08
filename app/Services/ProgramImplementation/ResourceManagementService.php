<?php

namespace App\Services\ProgramImplementation;

use App\Models\InitiativeStatus;
use App\Models\TrsProject;
use Illuminate\Database\Eloquent\Builder;

class ResourceManagementService
{
    private const TYPE_DIGITAL_INITIATIVE = 1;

    private const TYPE_IT_INITIATIVE = 2;

    public function getIndexProps(array $filters = []): array
    {
        $resolvedTypeFilter = $this->resolveTypeFilter($filters['type'] ?? null);
        $resolvedStatusFilter = $this->resolveStatusFilter($filters['status'] ?? null);

        $resourceRows = TrsProject::select([
                'trs_projects.id',
                'trs_projects.code',
                'trs_projects.name',
                'trs_projects.tipe_inisiative',
            ])
            ->with([
                'projectCharters' => function ($query) use ($resolvedStatusFilter) {
                    $query->select([
                    'trs_project_charters.id',
                    'trs_project_charters.project_id',
                    'trs_project_charters.status',
                    'trs_project_charters.budget',
                    'trs_project_charters.key_personnel',
                ]);

                    if ($resolvedStatusFilter !== null) {
                        $query->where('trs_project_charters.status', $resolvedStatusFilter);
                    }

                    $query->oldest('trs_project_charters.id');
                },
                'projectCharters.statusRef:id,name',
            ])
            ->when(
                $resolvedTypeFilter !== null,
                fn ($query) => $query->where('trs_projects.tipe_inisiative', $resolvedTypeFilter)
            )
            ->whereHas('projectCharters', function (Builder $query) use ($resolvedStatusFilter) {
                if ($resolvedStatusFilter !== null) {
                    $query->where('trs_project_charters.status', $resolvedStatusFilter);
                }
            })
            ->oldest('trs_projects.id')
            ->get()
            ->flatMap(fn (TrsProject $project): array => $this->mapProjectResources($project))
            ->values()
            ->all();

        return [
            'resourceProjects' => $resourceRows,
            'resourceSummary' => $this->buildResourceSummary($resourceRows),
            'filters' => [
                'type' => $resolvedTypeFilter !== null ? (string) $resolvedTypeFilter : 'all',
                'status' => $resolvedStatusFilter !== null ? (string) $resolvedStatusFilter : 'all',
            ],
            'filterOptions' => [
                'types' => $this->typeOptions(),
                'statuses' => $this->statusOptions(),
            ],
        ];
    }

    private function mapProjectResources(TrsProject $project): array
    {
        return $project->projectCharters
            ->map(function ($charter) use ($project): array {
                return [
                    'id' => (int) $charter->id,
                    'project_id' => (int) $project->id,
                    'project_code' => $this->normalizeText($project->getAttribute('code')),
                    'project_name' => $this->normalizeProjectName($project),
                    'project_type' => (int) ($project->tipe_inisiative ?? 0),
                    'project_type_label' => $this->typeLabel($project->tipe_inisiative),
                    'status' => $this->normalizeStatusLabel(
                        $charter->statusRef?->name,
                        $charter->status,
                    ),
                    'budget' => $this->normalizeText($charter->budget),
                    'key_personnel' => $this->normalizeText($charter->key_personnel),
                ];
            })
            ->values()
            ->all();
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

    private function resolveTypeFilter(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return self::TYPE_IT_INITIATIVE;
        }

        if ($value === 'all') {
            return null;
        }

        $normalized = (int) $value;

        return in_array($normalized, [self::TYPE_DIGITAL_INITIATIVE, self::TYPE_IT_INITIATIVE], true)
            ? $normalized
            : self::TYPE_IT_INITIATIVE;
    }

    private function resolveStatusFilter(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 'all') {
            return null;
        }

        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : null;
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

    private function statusOptions(): array
    {
        return InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status): array => [
                'value' => (string) $status->id,
                'label' => $this->normalizeStatusLabel($status->name, $status->id),
            ])
            ->values()
            ->all();
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
