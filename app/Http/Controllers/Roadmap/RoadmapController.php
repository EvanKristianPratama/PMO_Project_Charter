<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MstInitiative;
use App\Models\TrsProject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    /**
     * View-only roadmap page with all projects.
     */
    public function index(): Response
    {
        return Inertia::render('ProgramImplementation/RoadMap/Index', $this->buildRoadmapOverviewPayload());
    }


    public function add(Request $request): Response
    {
        return Inertia::render('ProgramImplementation/RoadMap/Create', $this->buildRoadmapEditorPayload($request));
    }

    /**
     * Input/edit roadmap process page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('ProgramImplementation/RoadMap/Edit', $this->buildRoadmapEditorPayload($request));
    }

    /**
     * Display a single initiative with all its related projects.
     */
    public function show(MstInitiative $initiative): Response
    {
        $initiative->load(['mappedProjects' => function ($query): void {
            $this->applyRoadmapProjectRelations($query);
            $query->orderBy('trs_projects.id');
        }]);

        $initiative->setRelation(
            'mappedProjects',
            $this->decorateProjectsWithRoadmapVersionMeta($initiative->mappedProjects)
        );

        return Inertia::render('ProgramImplementation/RoadMap/Show', [
            'initiative' => $initiative,
            'projects' => $initiative->mappedProjects,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ]);
    }

    /**
     * Base project relations for roadmap pages.
     */
    private function applyRoadmapProjectRelations($query): void
    {
        $query
            ->select(['trs_projects.id', 'trs_projects.name', 'trs_projects.code', 'trs_projects.metadata'])
            ->with([
                'charter' => fn ($charterQuery) => $charterQuery->select(
                    'trs_project_charters.id',
                    'trs_project_charters.project_id',
                    'trs_project_charters.objectives',
                    'trs_project_charters.duration',
                ),
                'milestones' => fn ($milestoneQuery) => $milestoneQuery->select(
                    'trs_milestones.id',
                    'trs_milestones.project_id',
                    'trs_milestones.version',
                    'trs_milestones.title',
                    'trs_milestones.output',
                    'trs_milestones.start_date',
                    'trs_milestones.end_date',
                    'trs_milestones.type',
                    'trs_milestones.milestone_type',
                    'trs_milestones.order',
                )->orderBy('trs_milestones.order')->orderBy('trs_milestones.id'),
            ]);
    }

    /**
     * Shared roadmap year range.
     */
    private function roadmapYearRange(): array
    {
        return [
            'yearStart' => 2025,
            'yearEnd' => 2029,
        ];
    }

    /**
     * Payload for roadmap view-only page from project source table.
     */
    private function buildRoadmapOverviewPayload(): array
    {
        $projects = TrsProject::query();
        $this->applyRoadmapProjectRelations($projects);
        $projects = $projects
            ->orderBy('trs_projects.id')
            ->get();
        $projects = $this->decorateProjectsWithRoadmapVersionMeta($projects);

        return [
            'projects' => $projects,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    /**
     * Payload for roadmap input/edit page.
     */
    private function buildRoadmapEditorPayload(Request $request): array
    {
        $selectedProjectId = $request->integer('project_id');
        $selectedVersion = $request->query('version');

        $projects = TrsProject::query();
        $this->applyRoadmapProjectRelations($projects);
        $projects = $projects
            ->orderBy('trs_projects.id')
            ->get();
        $projects = $this->decorateProjectsWithRoadmapVersionMeta($projects);

        $resolvedProjectId = $projects->contains('id', $selectedProjectId)
            ? $selectedProjectId
            : $projects->first()?->id;
        $selectedProject = $projects->firstWhere('id', $resolvedProjectId);
        $resolvedRoadmapVersion = $this->resolveRoadmapVersion($selectedProject, $selectedVersion);

        return [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'selectedProjectId' => $resolvedProjectId,
            'selectedRoadmapVersion' => $resolvedRoadmapVersion,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    private function decorateProjectsWithRoadmapVersionMeta(Collection $projects): Collection
    {
        return $projects->map(function (TrsProject $project): TrsProject {
            $milestones = $project->milestones ?? collect();
            $milestones = $milestones->map(function ($milestone) {
                $milestone->version = $this->normalizeVersionLabel($milestone->version);
                return $milestone;
            });
            $project->setRelation('milestones', $milestones);

            $versions = $this->extractRoadmapVersions($project, $milestones);
            $activeVersion = $this->resolveActiveRoadmapVersion($project, $versions);

            $project->setAttribute('roadmap_versions', $versions
                ->map(fn (string $label): array => [
                    'id' => $label,
                    'version_label' => $label,
                ])
                ->values()
                ->all());
            $project->setAttribute('active_roadmap_version', $activeVersion);

            return $project;
        });
    }

    private function resolveRoadmapVersion(?TrsProject $project, mixed $requestedVersion): ?string
    {
        if ($project === null) {
            return null;
        }

        $versions = collect($project->roadmap_versions ?? [])->pluck('id')->values();
        $rawRequested = trim((string) $requestedVersion);

        if ($rawRequested !== '') {
            $normalizedRequested = $this->normalizeVersionLabel($rawRequested);

            if ($versions->contains($normalizedRequested)) {
                return $normalizedRequested;
            }
        }

        return $project->active_roadmap_version ?? $versions->first() ?? 'v1';
    }

    private function extractRoadmapVersions(TrsProject $project, Collection $milestones): Collection
    {
        $labelsFromMilestones = $milestones
            ->pluck('version')
            ->values();
        $labelsFromMetadata = $this->roadmapVersionLabelsFromMeta($project);

        $labels = $labelsFromMilestones
            ->merge($labelsFromMetadata)
            ->map(fn ($label) => $this->normalizeVersionLabel($label))
            ->filter()
            ->unique()
            ->sortByDesc(fn (string $label): int => $this->extractVersionNumber($label))
            ->values();

        if ($labels->isEmpty()) {
            return collect(['v1']);
        }

        return $labels;
    }

    private function roadmapVersionLabelsFromMeta(TrsProject $project): Collection
    {
        $metadata = is_array($project->metadata) ? $project->metadata : [];
        $labels = $metadata['roadmap_versions'] ?? [];

        if (!is_array($labels)) {
            return collect();
        }

        return collect($labels);
    }

    private function resolveActiveRoadmapVersion(TrsProject $project, Collection $versions): string
    {
        if ($versions->isEmpty()) {
            return 'v1';
        }

        $metadata = is_array($project->metadata) ? $project->metadata : [];
        $metadataActive = $this->normalizeVersionLabel($metadata['active_roadmap_version'] ?? '');

        if ($versions->contains($metadataActive)) {
            return $metadataActive;
        }

        return $versions->first() ?? 'v1';
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

    private function extractVersionNumber(string $label): int
    {
        if (preg_match('/^v(\d+)$/i', trim($label), $matches) === 1) {
            return max((int) $matches[1], 1);
        }

        return 1;
    }
}
