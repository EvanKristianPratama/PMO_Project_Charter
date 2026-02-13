<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\MilestoneStoreRequest;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class MilestoneController extends Controller
{
    public function store(MilestoneStoreRequest $request, Project $project): RedirectResponse
    {
        $payload = $request->validated();
        $nextOrder = (int) ($project->milestones()->max('order') ?? 0) + 1;

        $project->milestones()->create([
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'order' => $nextOrder,
        ]);

        return back()->with('success', 'Roadmap activity added.');
    }

    public function destroy(Project $project, Milestone $milestone): RedirectResponse
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404);
        }

        $milestone->delete();

        return back()->with('success', 'Roadmap activity removed.');
    }
}
