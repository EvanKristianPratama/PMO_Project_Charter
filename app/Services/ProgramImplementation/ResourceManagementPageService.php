<?php

namespace App\Services\ProgramImplementation;

use App\Models\TrsProject;

class ResourceManagementPageService
{
    public function getIndexProps(): array
    {
        $projects = TrsProject::query()
            ->select([
                'trs_projects.id',
                'trs_projects.code',
                'trs_projects.name',
            ])
            ->with([
                'projectCharter' => static fn ($query) => $query->select([
                    'trs_project_charters.id',
                    'trs_project_charters.project_id',
                    'trs_project_charters.budget',
                    'trs_project_charters.key_personnel',
                ]),
            ])
            ->whereHas('projectCharters')
            ->orderBy('trs_projects.name')
            ->orderBy('trs_projects.id')
            ->get()
            ->map(function (TrsProject $project): array {
                $charter = $project->projectCharter;

                return [
                    'id' => (int) $project->id,
                    'project_name' => $this->normalizeProjectName($project),
                    'budget' => $this->normalizeText($charter?->budget),
                    'key_personnel' => $this->normalizeText($charter?->key_personnel),
                ];
            })
            ->values()
            ->all();

        return [
            'resourceProjects' => $projects,
            'resourceSummary' => [
                'total_projects' => count($projects),
            ],
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
}
