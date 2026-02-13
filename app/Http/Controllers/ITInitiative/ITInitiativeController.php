<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\ITInitiativeIndexRequest;
use App\Http\Requests\ITInitiative\ITInitiativeStoreRequest;
use App\Http\Requests\ITInitiative\ITInitiativeUpdateRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ITInitiativeController extends Controller
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
            'itInitiatives' => $projects,
            'selectedProject' => $selectedProject,
            'selectedProjectId' => $resolvedProjectId,
        ]);
    }

    public function index(ITInitiativeIndexRequest $request): Response
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

        return Inertia::render('ITInitiative/Index', [
            'itInitiatives' => $projects,
            'filters'  => [
                'search' => $filters['search'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ITInitiative/Create');
    }

    public function store(ITInitiativeStoreRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()->route('it-initiatives.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project): Response
    {
        $project->load(['charter', 'milestones', 'programs', 'goals', 'owner']);

        return Inertia::render('ITInitiative/Show', [
            'itInitiative' => $project,
        ]);
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('ITInitiative/Edit', [
            'itInitiative' => $project,
        ]);
    }

    public function update(ITInitiativeUpdateRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('it-initiatives.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('it-initiatives.index')->with('success', 'Project deleted successfully.');
    }
}
