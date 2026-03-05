<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\MstInitiative;
use App\Models\ProjectCharter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    /**
     * View-only roadmap page with all project charters.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('ProgramImplementation/RoadMap/Index', $this->buildRoadmapOverviewPayload($request));
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
     * Display roadmap by initiative (all mapped project charters).
     */
    public function show(MstInitiative $initiative): Response
    {
        $projectIds = $initiative->mappedProjects()->pluck('trs_projects.id')->values();

        $sources = $projectIds->isEmpty()
            ? collect()
            : $this->roadmapSourceQuery()
                ->whereIn('trs_project_charters.project_id', $projectIds)
                ->orderByDesc('trs_project_charters.id')
                ->get();

        $projects = $this->decorateRoadmapSources($sources);

        return Inertia::render('ProgramImplementation/RoadMap/Show', [
            'program' => [
                'id' => (int) $initiative->id,
                'name' => $initiative->name,
                'projects' => $projects,
            ],
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ]);
    }

    private function roadmapSourceQuery(): Builder
    {
        return ProjectCharter::query()
            ->select([
                'trs_project_charters.id',
                'trs_project_charters.project_id',
                'trs_project_charters.version_label',
                'trs_project_charters.objectives',
                'trs_project_charters.duration',
                'trs_project_charters.metadata',
            ])
            ->with([
                'project:id,code,name,metadata',
                'milestones' => fn ($milestoneQuery) => $milestoneQuery->select(
                    'trs_milestones.id',
                    'trs_milestones.project_id',
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
     * Payload for roadmap view-only page from project charter source table.
     */
    private function buildRoadmapOverviewPayload(Request $request): array
    {
        $sources = $this->roadmapSourceQuery()
            ->orderByDesc('trs_project_charters.id')
            ->get();
        $projects = $this->decorateRoadmapSources($sources);

        $requestedPcId = $this->requestedPcId($request);
        $resolvedPcId = $projects->contains('id', $requestedPcId) ? $requestedPcId : null;

        return [
            'projects' => $projects,
            'selectedProjectId' => $resolvedPcId,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }

    /**
     * Payload for roadmap input/edit page.
     */
    private function buildRoadmapEditorPayload(Request $request): array
    {
        $selectedPcId = $this->requestedPcId($request);
        $selectedVersion = $request->query('version');

        $sources = $this->roadmapSourceQuery()
            ->orderByDesc('trs_project_charters.id')
            ->get();
        $projects = $this->decorateRoadmapSources($sources);

        $resolvedProjectId = $projects->contains('id', $selectedPcId)
            ? $selectedPcId
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

    private function decorateRoadmapSources(Collection $sources): Collection
    {
        return $sources->map(function (ProjectCharter $charter): ProjectCharter {
            $charterVersion = $this->normalizeVersionLabel($charter->version_label);
            $milestones = ($charter->milestones ?? collect())->map(function ($milestone) use ($charterVersion) {
                $milestone->version = $charterVersion;
                return $milestone;
            });
            $charter->setRelation('milestones', $milestones);

            $versions = $this->extractRoadmapVersions($charter, $milestones);
            $activeVersion = $this->resolveActiveRoadmapVersion($charter, $versions);

            $charter->setAttribute('pc_id', (int) $charter->id);
            $charter->setAttribute('code', $charter->project?->code);
            $charter->setAttribute('name', $charter->project?->name);
            $charter->setAttribute('charter', [
                'id' => (int) $charter->id,
                'project_id' => (int) $charter->project_id,
                'version_label' => $charter->version_label,
                'objectives' => $charter->objectives,
                'duration' => $charter->duration,
            ]);
            $charter->setAttribute('roadmap_versions', $versions
                ->map(fn (string $label): array => [
                    'id' => $label,
                    'version_label' => $label,
                ])
                ->values()
                ->all());
            $charter->setAttribute('active_roadmap_version', $activeVersion);

            return $charter;
        });
    }

    private function requestedPcId(Request $request): ?int
    {
        $pcId = $request->integer('pc_id');

        if ($pcId > 0) {
            return $pcId;
        }

        // Backward compatibility for old links that still send project_id.
        $legacyProjectId = $request->integer('project_id');

        if ($legacyProjectId <= 0) {
            return null;
        }

        $resolvedPcId = ProjectCharter::query()
            ->where('project_id', $legacyProjectId)
            ->max('id');

        return $resolvedPcId ? (int) $resolvedPcId : null;
    }

    private function resolveRoadmapVersion(?ProjectCharter $charter, mixed $requestedVersion): ?string
    {
        if ($charter === null) {
            return null;
        }

        $charterVersion = $this->normalizeVersionLabel($charter->version_label);
        $versions = collect([$charterVersion]);

        return $versions->first() ?? 'v1';
    }

    private function extractRoadmapVersions(ProjectCharter $charter, Collection $milestones): Collection
    {
        return collect([$this->normalizeVersionLabel($charter->version_label)]);
    }

    private function roadmapVersionLabelsFromMeta(ProjectCharter $charter): Collection
    {
        return collect([$this->normalizeVersionLabel($charter->version_label)]);
    }

    private function resolveActiveRoadmapVersion(ProjectCharter $charter, Collection $versions): string
    {
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
