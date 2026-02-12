<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\ProjectCharterStoreRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class ProjectCharterController extends Controller
{
    public function store(ProjectCharterStoreRequest $request, Project $project): RedirectResponse
    {
        $project->charter()->updateOrCreate(
            ['project_id' => $project->id],
            $request->validated()
        );

        return back()->with('success', 'Project Charter saved successfully.');
    }
}
