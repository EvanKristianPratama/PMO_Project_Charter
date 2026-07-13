<?php

namespace Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\DigitalInitiatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives\StoreDigitalInitiativeRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives\UpsertImplementationStatusRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives\UpdateDigitalInitiativeRequest;
use App\Http\Requests\ProgramImplementation\ProjectCharter\UpdateProjectStatusHistoryRequest;
use Modules\ITSP\Models\ProjectStatusHistory;
use Modules\ITSP\Models\TrsProject;
use Modules\ITSP\Services\ProgramImplementation\ProjectCharter\DigitalInitiatives\DigitalInitiativeService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DigitalInitiativeController extends Controller
{
    public function __construct(
        private readonly DigitalInitiativeService $digitalInitiativeService
    ) {}

    public function index(): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/DigitalInitiatives/Index',
            $this->digitalInitiativeService->getIndexProps()
        );
    }

    public function create(): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/DigitalInitiatives/Create',
            $this->digitalInitiativeService->getCreateProps()
        );
    }

    public function store(StoreDigitalInitiativeRequest $request): RedirectResponse
    {
        $this->digitalInitiativeService->createDigitalInitiative($request->validated());

        return redirect()
            ->route('itsp.digital-initiatives.index')
            ->with('success', 'Digital initiative berhasil ditambahkan.');
    }

    public function show(TrsProject $digitalInitiative): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/DigitalInitiatives/Show',
            $this->digitalInitiativeService->getShowProps($digitalInitiative)
        );
    }

    public function edit(TrsProject $digitalInitiative): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/DigitalInitiatives/Edit',
            $this->digitalInitiativeService->getEditProps($digitalInitiative)
        );
    }

    public function update(
        UpdateDigitalInitiativeRequest $request,
        TrsProject $digitalInitiative
    ): RedirectResponse {
        $this->digitalInitiativeService->updateDigitalInitiative($digitalInitiative, $request->validated());

        return redirect()
            ->route('itsp.digital-initiatives.index')
            ->with('success', 'Digital initiative berhasil diperbarui.');
    }

    public function destroy(TrsProject $digitalInitiative): RedirectResponse
    {
        $this->digitalInitiativeService->deleteDigitalInitiative($digitalInitiative);

        return redirect()
            ->route('itsp.digital-initiatives.index')
            ->with('success', 'Digital initiative berhasil dihapus.');
    }

    public function storeImplementationStatus(
        UpsertImplementationStatusRequest $request
    ): RedirectResponse {
        $this->digitalInitiativeService->storeImplementationStatus($request->validated());

        return back()->with('success', 'Status implementation berhasil ditambahkan.');
    }

    public function updateImplementationStatus(
        UpsertImplementationStatusRequest $request,
        int $statusId
    ): RedirectResponse {
        $this->digitalInitiativeService->updateImplementationStatus($statusId, $request->validated());

        return back()->with('success', 'Status implementation berhasil diperbarui.');
    }

    public function destroyImplementationStatus(int $statusId): RedirectResponse
    {
        $this->digitalInitiativeService->deleteImplementationStatus($statusId);

        return back()->with('success', 'Status implementation berhasil dihapus.');
    }

    public function updateProjectStatusHistory(
        UpdateProjectStatusHistoryRequest $request,
        TrsProject $digitalInitiative,
        ProjectStatusHistory $history
    ): RedirectResponse {
        $this->digitalInitiativeService->updateProjectStatusHistory(
            $digitalInitiative,
            $history,
            $request->validated()
        );

        return back()->with('success', 'Project status history berhasil diperbarui.');
    }

    public function destroyProjectStatusHistory(
        TrsProject $digitalInitiative,
        ProjectStatusHistory $history
    ): RedirectResponse {
        $this->digitalInitiativeService->deleteProjectStatusHistory($digitalInitiative, $history);

        return back()->with('success', 'Project status history berhasil dihapus.');
    }
}
