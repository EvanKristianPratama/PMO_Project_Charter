<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\ProjectIndexRequest;
use App\Http\Requests\Projects\ProjectStoreRequest;
use App\Http\Requests\Projects\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function roadmapIndex(Request $request): Response
    {
        $selectedProjectId = $request->integer('project_id');

        $roadmapScope = static function ($query): void {
            $query->where(function ($builder): void {
                $builder
                    ->whereHas('charter', static function ($charter): void {
                        $charter
                            ->whereNotNull('duration')
                            ->where('duration', '!=', '');
                    })
                    ->orWhereHas('milestones', static function ($milestones): void {
                        $milestones
                            ->whereNotNull('start_date')
                            ->orWhereNotNull('end_date');
                    });
            });
        };

        $projects = Project::query()
            ->select(['id', 'name', 'code'])
            ->where($roadmapScope)
            ->orderBy('name')
            ->get();

        $resolvedProjectId = $projects->contains('id', $selectedProjectId)
            ? $selectedProjectId
            : $projects->first()?->id;

        $selectedProject = $resolvedProjectId
            ? Project::query()
                ->with(['charter', 'milestones', 'owner'])
                ->find($resolvedProjectId)
            : null;

        return Inertia::render('Roadmap/Index', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'selectedProjectId' => $resolvedProjectId,
        ]);
    }

    public function index(ProjectIndexRequest $request): Response
    {
        $filters = $request->validated();

        $projects = Project::query()
            ->with('owner')
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            }))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters'  => [
                'search' => $filters['search'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project): Response
    {
        $project->load(['charter', 'milestones', 'programs', 'goals', 'owner']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
