<?php

namespace App\Services\ProgramImplementation\Roadmap;

use App\Models\Milestone;
use App\Models\MstInitiative;
use App\Models\TrsProjectCharter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RoadmapPageService
{
    public function getOverviewPageProps(?int $projectCharterId, ?int $legacyProjectId): array
    {
        $roadmapSources = $this->roadmapSourceQuery()
            ->orderByDesc('trs_project_charters.id')
            ->get();

        $projects = $this->groupRoadmapSources($roadmapSources);
        $requestedProjectCharterId = $this->resolveRequestedProjectCharterId($projectCharterId, $legacyProjectId);
        [$selectedProjectId, $selectedCharterId] = $this->resolveSelectedIds(
            $roadmapSources,
            $projects,
            $requestedProjectCharterId,
            false,
        );

        return [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'selectedCharterId' => $selectedCharterId,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    public function getEditorPageProps(?int $projectCharterId, ?int $legacyProjectId): array
    {
        $roadmapSources = $this->roadmapSourceQuery()
            ->orderByDesc('trs_project_charters.id')
            ->get();

        $projects = $this->groupRoadmapSources($roadmapSources);
        $requestedProjectCharterId = $this->resolveRequestedProjectCharterId($projectCharterId, $legacyProjectId);
        [$selectedProjectId, $selectedCharterId] = $this->resolveSelectedIds(
            $roadmapSources,
            $projects,
            $requestedProjectCharterId,
            true,
        );

        return [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'selectedCharterId' => $selectedCharterId,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    public function getProgramPageProps(MstInitiative $initiative): array
    {
        $projectIds = $initiative->mappedProjects()->pluck('trs_projects.id')->values();

        $roadmapSources = $projectIds->isEmpty()
            ? collect()
            : $this->roadmapSourceQuery()
                ->whereIn('trs_project_charters.project_id', $projectIds)
                ->orderByDesc('trs_project_charters.id')
                ->get();

        return [
            'program' => [
                'id' => (int) $initiative->id,
                'name' => $initiative->name,
                'projects' => $this->flattenRoadmapCharters($roadmapSources),
            ],
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    private function roadmapSourceQuery(): Builder
    {
        return TrsProjectCharter::query()
            ->select([
                'trs_project_charters.id',
                'trs_project_charters.project_id',
                'trs_project_charters.version_label',
                'trs_project_charters.objectives',
                'trs_project_charters.duration',
            ])
            ->with([
                'project:id,code,name',
                'milestones' => fn ($milestoneQuery) => $milestoneQuery->select(
                    'trs_milestones.id',
                    'trs_milestones.pc_id',
                    'trs_milestones.version',
                    'trs_milestones.title',
                    'trs_milestones.output',
                    'trs_milestones.start_date',
                    'trs_milestones.end_date',
                    'trs_milestones.type',
                    'trs_milestones.milestone_type',
                    'trs_milestones.order'
                )->orderBy('trs_milestones.order')->orderBy('trs_milestones.id'),
            ]);
    }

    private function roadmapYearRange(): array
    {
        return [
            'yearStart' => 2025,
            'yearEnd' => 2029,
        ];
    }

    private function groupRoadmapSources(Collection $roadmapSources): Collection
    {
        return $roadmapSources
            ->groupBy('project_id')
            ->map(function (Collection $charters): array {
                $project = $charters->first()?->project;

                return [
                    'id' => (int) ($project?->id ?? 0),
                    'code' => $project?->code,
                    'name' => $project?->name,
                    'charters' => $charters
                        ->map(fn (TrsProjectCharter $charter): array => $this->mapCharterForRoadmap($charter))
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('id')
            ->values();
    }

    private function flattenRoadmapCharters(Collection $roadmapSources): Collection
    {
        return $roadmapSources->map(function (TrsProjectCharter $charter): array {
            $payload = $this->mapCharterForRoadmap($charter);
            $payload['code'] = $charter->project?->code;
            $payload['name'] = $charter->project?->name;

            return $payload;
        });
    }

    private function mapCharterForRoadmap(TrsProjectCharter $charter): array
    {
        $charterVersion = $this->normalizeVersionLabel($charter->version_label);
        $milestones = ($charter->milestones ?? collect())
            ->map(function ($milestone) use ($charterVersion) {
                $milestone->version = $charterVersion;

                return $milestone;
            })
            ->values();

        return [
            'id' => (int) $charter->id,
            'project_id' => (int) $charter->project_id,
            'version_label' => $charter->version_label,
            'objectives' => $charter->objectives,
            'duration' => $charter->duration,
            'milestones' => $milestones,
            'charter' => [
                'id' => (int) $charter->id,
                'project_id' => (int) $charter->project_id,
                'version_label' => $charter->version_label,
                'objectives' => $charter->objectives,
                'duration' => $charter->duration,
            ],
        ];
    }

    private function resolveSelectedIds(
        Collection $roadmapSources,
        Collection $projects,
        ?int $requestedProjectCharterId,
        bool $fallbackToFirst,
    ): array {
        if ($requestedProjectCharterId !== null) {
            $matchedCharter = $roadmapSources->firstWhere('id', $requestedProjectCharterId);

            if ($matchedCharter) {
                return [(int) $matchedCharter->project_id, (int) $matchedCharter->id];
            }
        }

        if (! $fallbackToFirst) {
            return [null, null];
        }

        $firstProject = $projects->first();

        if (! $firstProject) {
            return [null, null];
        }

        $firstCharterId = $firstProject['charters'][0]['id'] ?? null;

        return [$firstProject['id'] ?? null, $firstCharterId ? (int) $firstCharterId : null];
    }

    private function resolveRequestedProjectCharterId(?int $projectCharterId, ?int $legacyProjectId): ?int
    {
        if ($projectCharterId !== null) {
            return $projectCharterId;
        }

        if ($legacyProjectId === null) {
            return null;
        }

        $resolvedProjectCharterId = TrsProjectCharter::query()
            ->where('project_id', $legacyProjectId)
            ->max('id');

        return $resolvedProjectCharterId ? (int) $resolvedProjectCharterId : null;
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $raw = strtolower(trim((string) $value));

        if ($raw === '' || $raw === 'v') {
            return 'v1';
        }

        if (preg_match('/^v(\d+)$/', $raw, $matches) === 1) {
            return 'v'.max(1, (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $raw) === 1) {
            return 'v'.max(1, (int) $raw);
        }

        return $raw;
    }
}
