<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCharter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectCharterController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'background' => 'nullable|string',
            'objectives' => 'nullable|string',
            'scope' => 'nullable|string',
            'impact_value' => 'nullable|string',
            'key_personnel' => 'nullable|string',
            'key_items' => 'nullable|string',
            'budget' => 'nullable|string|max:255',
            'risks_identified' => 'nullable|string',
            'risk_mitigation' => 'nullable|string',
        ]);

        $project->charter()->updateOrCreate(
            ['project_id' => $project->id],
            $validated
        );

        return back()->with('success', 'Project Charter saved successfully.');
    }
}
