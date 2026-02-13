<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\CharterStoreRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class CharterController extends Controller
{
    public function store(CharterStoreRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();
        $versionLabel = trim((string) ($validated['version_label'] ?? ''));

        if ($versionLabel === '') {
            $versionLabel = sprintf('v%d', $project->charters()->count() + 1);
        }

        $project->charters()->create([
            ...Arr::except($validated, ['version_label']),
            'version_label' => $versionLabel,
        ]);

        return back()->with('success', sprintf('Project Charter %s saved successfully.', $versionLabel));
    }
}
