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

        $projects = $this->flattenRoadmapCharters($sources);

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
        $projects = $this->groupRoadmapSources($sources);
        $requestedPcId = $this->requestedPcId($request);
        [$resolvedProjectId, $resolvedCharterId] = $this->resolveSelectedIds($sources, $projects, $requestedPcId, false);

        return [
            'projects' => $projects,
            'selectedProjectId' => $resolvedProjectId,
            'selectedCharterId' => $resolvedCharterId,
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

        $sources = $this->roadmapSourceQuery()
            ->orderByDesc('trs_project_charters.id')
            ->get();
        $projects = $this->groupRoadmapSources($sources);
        [$resolvedProjectId, $resolvedCharterId] = $this->resolveSelectedIds($sources, $projects, $selectedPcId, true);

        return [
            'projects' => $projects,
            'selectedProjectId' => $resolvedProjectId,
            'selectedCharterId' => $resolvedCharterId,
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
            ...$this->roadmapYearRange(),
        ];
    }
private function groupRoadmapSources(Collection $sources): Collection
{
    return $sources
        ->groupBy('project_id')
        ->map(function (Collection $charters): array {
            $project = $charters->first()?->project;

            return [
                'id' => (int) ($project?->id ?? 0),
                'code' => $project?->code,
                'name' => $project?->name,
                'charters' => $charters
                    ->map(fn (ProjectCharter $charter): array => $this->mapCharterForRoadmap($charter))
                    ->values()
                    ->all(),
            ];
        })
        ->sortBy('id')
        ->values();
}
            ->values();
    }

    private function flattenRoadmapCharters(Collection $sources): Collection
    {
        return $sources->map(function (ProjectCharter $charter): array {
            $payload = $this->mapCharterForRoadmap($charter);
            $payload['code'] = $charter->project?->code;
            $payload['name'] = $charter->project?->name;

            return $payload;
        });
    }

    private function mapCharterForRoadmap(ProjectCharter $charter): array
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

    private function resolveSelectedIds(Collection $sources, Collection $projects, ?int $requestedPcId, bool $fallbackToFirst): array
    {
        if ($requestedPcId !== null) {
            $matched = $sources->firstWhere('id', $requestedPcId);

            if ($matched) {
                return [(int) $matched->project_id, (int) $matched->id];
            }
        }

        if (!$fallbackToFirst) {
            return [null, null];
        }

        $firstProject = $projects->first();
        if (!$firstProject) {
            return [null, null];
        }
        $firstCharterId = $firstProject['charters'][0]['id'] ?? null;

        return [$firstProject['id'] ?? null, $firstCharterId ? (int) $firstCharterId : null];
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
