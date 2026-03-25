<?php

namespace App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\StoreMilestoneRequest;
use App\Models\Milestone;
use App\Models\ProjectCharter;
use App\Services\ProgramImplementation\ProjectCharter\ITInitiatives\MilestoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function __construct(
        private readonly MilestoneService $milestoneService
    ) {}

    public function store(StoreMilestoneRequest $request, ProjectCharter $project): RedirectResponse
    {
        $this->milestoneService->storeMilestone($project, $request->validated());

        return back()->with('success', 'Roadmap activity berhasil ditambahkan.');
    }

    public function update(
        StoreMilestoneRequest $request,
        ProjectCharter $project,
        Milestone $milestone
    ): RedirectResponse {
        $this->milestoneService->updateMilestone($project, $milestone, $request->validated());

        return back()->with('success', 'Roadmap activity berhasil diperbarui.');
    }

    public function destroy(ProjectCharter $project, Milestone $milestone): RedirectResponse
    {
        $this->milestoneService->deleteMilestone($project, $milestone);

        return back()->with('success', 'Roadmap activity berhasil dihapus.');
    }

    public function createVersion(Request $request, ProjectCharter $project): RedirectResponse
    {
        return back()->with('warning', 'Versi roadmap mengikuti versi project charter. Buat versi charter baru untuk roadmap baru.');
    }
}
