<?php

namespace App\Services\ProgramImplementation;

use App\Models\InitiativeStatus;
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
                    ]);

                    $query->oldest('trs_project_charters.id');
                },
                'projectCharters.statusRef:id,name',
            ])
            ->oldest('trs_projects.id')
            ->get()
            ->flatMap(fn (TrsProject $project): array => $this->mapProjectResources($project))
            ->values()
            ->all();

        return [
            'resourceProjects' => $resourceRows,
            'resourceSummary' => $this->buildResourceSummary($resourceRows),
            'filters' => [
                'status' => 'all',
            ],
            'filterOptions' => [
                'statuses' => $this->statusOptions(),
            ],
        ];
    }

    private function mapProjectResources(TrsProject $project): array
    {
        if ($project->projectCharters->isEmpty()) {
            return [[
                'id' => null,
                'row_key' => sprintf('project-%d', $project->id),
                'project_id' => (int) $project->id,
                'project_code' => $this->normalizeText($project->getAttribute('code')),
                'project_name' => $this->normalizeProjectName($project),
                'project_type' => (int) ($project->tipe_inisiative ?? 0),
                'project_type_label' => $this->typeLabel($project->tipe_inisiative),
                'status_id' => null,
                'status' => '-',
                'budget' => null,
                'key_personnel' => null,
            ]];
        }

        return $project->projectCharters
            ->map(function ($charter) use ($project): array {
                return [
                    'id' => (int) $charter->id,
                    'row_key' => sprintf('charter-%d', $charter->id),
                    'project_id' => (int) $project->id,
                    'project_code' => $this->normalizeText($project->getAttribute('code')),
                    'project_name' => $this->normalizeProjectName($project),
                    'project_type' => (int) ($project->tipe_inisiative ?? 0),
                    'project_type_label' => $this->typeLabel($project->tipe_inisiative),
                    'status_id' => $charter->status !== null && $charter->status !== ''
                        ? (int) $charter->status
                        : null,
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
