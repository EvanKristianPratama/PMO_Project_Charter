<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\CharterStoreRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class CharterController extends Controller
{
    public function store(CharterStoreRequest $request, Project $project): RedirectResponse
    {
        $project->charter()->updateOrCreate(
            ['project_id' => $project->id],
            $request->validated()
        );

        return back()->with('success', 'Project Charter saved successfully.');
    }
}
