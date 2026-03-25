<?php

namespace App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\StoreProjectCharterRequest;
use App\Models\ProjectCharter;
use App\Models\TrsProject;
use App\Services\ProgramImplementation\ProjectCharter\ITInitiatives\ProjectCharterService;
use Illuminate\Http\RedirectResponse;

class CharterController extends Controller
{
    public function __construct(
        private readonly ProjectCharterService $projectCharterService
    ) {}

    public function store(StoreProjectCharterRequest $request, TrsProject $project): RedirectResponse
    {
        $versionLabel = $this->projectCharterService->storeProjectCharter($project, $request->validated());

        return back()->with('success', sprintf('Project charter %s berhasil disimpan.', $versionLabel));
    }

    public function update(
        StoreProjectCharterRequest $request,
        TrsProject $project,
        ProjectCharter $charter
    ): RedirectResponse {
        $versionLabel = $this->projectCharterService->updateProjectCharter($project, $charter, $request->validated());

        return back()->with('success', sprintf('Project charter %s berhasil diperbarui.', $versionLabel));
    }
}
