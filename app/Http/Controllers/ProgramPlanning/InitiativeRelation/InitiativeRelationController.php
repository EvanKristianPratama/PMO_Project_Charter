<?php

namespace App\Http\Controllers\ProgramPlanning\InitiativeRelation;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\InitiativeRelation\StoreInitiativeRelationRequest;
use App\Http\Requests\ProgramPlanning\InitiativeRelation\UpdateInitiativeRelationRequest;
use App\Models\MstInitiativeRelation;
use App\Services\ProgramPlanning\InitiativeRelation\InitiativeRelationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InitiativeRelationController extends Controller
{
    public function __construct(
        private readonly InitiativeRelationService $initiativeRelationService
    ) {}

    public function index(): Response
    {
        return Inertia::render(
            'ProgramPlanning/InitiativeRelation/Index',
            $this->initiativeRelationService->getIndexProps()
        );
    }

    public function create(): Response
    {
        return Inertia::render(
            'ProgramPlanning/InitiativeRelation/Create',
            $this->initiativeRelationService->getCreateProps()
        );
    }

    public function store(StoreInitiativeRelationRequest $request): RedirectResponse
    {
        $this->initiativeRelationService->createInitiativeRelation($request->validated());

        return redirect()
            ->route('initiative-relations.index')
            ->with('success', 'Initiative relation berhasil ditambahkan.');
    }

    public function show(MstInitiativeRelation $initiativeRelation): JsonResponse
    {
        return response()->json(
            $this->initiativeRelationService->getShowPayload($initiativeRelation)
        );
    }

    public function edit(MstInitiativeRelation $initiativeRelation): Response
    {
        return Inertia::render(
            'ProgramPlanning/InitiativeRelation/Edit',
            $this->initiativeRelationService->getEditProps($initiativeRelation)
        );
    }

    public function update(
        UpdateInitiativeRelationRequest $request,
        MstInitiativeRelation $initiativeRelation
    ): RedirectResponse {
        $this->initiativeRelationService->updateInitiativeRelation($initiativeRelation, $request->validated());

        return redirect()
            ->route('initiative-relations.index')
            ->with('success', 'Initiative relation berhasil diperbarui.');
    }

    public function destroy(MstInitiativeRelation $initiativeRelation): RedirectResponse
    {
        $this->initiativeRelationService->deleteInitiativeRelation($initiativeRelation);

        return back()->with('success', 'Initiative relation berhasil dihapus.');
    }
}
