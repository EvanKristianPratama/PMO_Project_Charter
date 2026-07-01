<?php

namespace Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\StoreITInitiativeRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\UpdateITInitiativeRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\UpdateInitiativeMappingRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\UpsertImplementationStatusRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\UpdateProjectStatusHistoryRequest;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use App\Services\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ITInitiativeController extends Controller
{
    public function __construct(
        private readonly ITInitiativeService $itInitiativeService
    ) {}

    public function index(): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Index',
            $this->itInitiativeService->getIndexProps()
        );
    }

    public function create(): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Create',
            $this->itInitiativeService->getCreateProps()
        );
    }

    public function store(StoreITInitiativeRequest $request): RedirectResponse
    {
        $this->itInitiativeService->createItInitiative($request->validated());

        return redirect()
            ->route('itsp.it-initiatives.index')
            ->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(TrsProject $project): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Show',
            $this->itInitiativeService->getShowProps($project)
        );
    }

    public function edit(TrsProject $project): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Edit',
            $this->itInitiativeService->getEditProps($project)
        );
    }

    public function update(UpdateITInitiativeRequest $request, TrsProject $project): RedirectResponse
    {
        $this->itInitiativeService->updateItInitiative($project, $request->validated());

        return redirect()
            ->route('itsp.it-initiatives.edit', $project)
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(TrsProject $project): RedirectResponse
    {
        $this->itInitiativeService->deleteItInitiative($project);

        return redirect()
            ->route('itsp.it-initiatives.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    public function storeImplementationStatus(
        UpsertImplementationStatusRequest $request,
        TrsProject $project
    ): RedirectResponse {
        $this->itInitiativeService->storeImplementationStatus($project, $request->validated());

        return back()->with('success', 'Status implementation berhasil ditambahkan.');
    }

    public function updateImplementationStatus(
        UpsertImplementationStatusRequest $request,
        int $id
    ): RedirectResponse {
        $this->itInitiativeService->updateImplementationStatus($id, $request->validated());

        return back()->with('success', 'Status implementation berhasil diperbarui.');
    }

    public function destroyImplementationStatus(int $id): RedirectResponse
    {
        $this->itInitiativeService->deleteImplementationStatus($id);

        return back()->with('success', 'Status implementation berhasil dihapus.');
    }

    public function updateProjectStatusHistory(
        UpdateProjectStatusHistoryRequest $request,
        TrsProject $project,
        ProjectStatusHistory $history
    ): RedirectResponse {
        $this->itInitiativeService->updateProjectStatusHistory($project, $history, $request->validated());

        return back()->with('success', 'Project status history berhasil diperbarui.');
    }

    public function destroyProjectStatusHistory(
        TrsProject $project,
        ProjectStatusHistory $history
    ): RedirectResponse {
        $this->itInitiativeService->deleteProjectStatusHistory($project, $history);

        return back()->with('success', 'Project status history berhasil dihapus.');
    }

    public function updateMapping(
        UpdateInitiativeMappingRequest $request,
        TrsProject $project
    ): RedirectResponse {
        $this->itInitiativeService->updateMapping($project, $request->validated());

        return back()->with('success', 'Mapping initiative berhasil diperbarui.');
    }
}
