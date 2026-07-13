<?php

namespace Modules\ITSP\Controllers\StrategicHouse\InitiativeRelation;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\InitiativeRelation\StoreInitiativeRelationRequest;
use App\Http\Requests\ProgramPlanning\InitiativeRelation\UpdateInitiativeRelationRequest;
use Modules\ITSP\Models\MstInitiativeRelation;
use App\Services\Shared\CacheManager;
use Modules\ITSP\Services\StrategicHouse\InitiativeRelation\InitiativeRelationService;
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
        return Inertia::render('modules/ITSP/StrategicHouse/InitiativeRelation/Index',
            $this->initiativeRelationService->getIndexProps()
        );
    }

    public function create(): Response
    {
        return Inertia::render('modules/ITSP/StrategicHouse/InitiativeRelation/Create',
            $this->initiativeRelationService->getCreateProps()
        );
    }

    public function store(StoreInitiativeRelationRequest $request): RedirectResponse
    {
        $this->initiativeRelationService->createInitiativeRelation($request->validated());
        CacheManager::clearInitiativeCaches();

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
        return Inertia::render('modules/ITSP/StrategicHouse/InitiativeRelation/Edit',
            $this->initiativeRelationService->getEditProps($initiativeRelation)
        );
    }

    public function update(
        UpdateInitiativeRelationRequest $request,
        MstInitiativeRelation $initiativeRelation
    ): RedirectResponse {
        $this->initiativeRelationService->updateInitiativeRelation($initiativeRelation, $request->validated());
        CacheManager::clearInitiativeCaches();

        return redirect()
            ->route('initiative-relations.index')
            ->with('success', 'Initiative relation berhasil diperbarui.');
    }

    public function destroy(MstInitiativeRelation $initiativeRelation): RedirectResponse
    {
        $this->initiativeRelationService->deleteInitiativeRelation($initiativeRelation);
        CacheManager::clearInitiativeCaches();

        return back()->with('success', 'Initiative relation berhasil dihapus.');
    }

    public function syncPositions(\Illuminate\Http\Request $request): RedirectResponse
    {
        $positions = $request->input('positions', []);
        
        $upsertData = [];
        foreach ($positions as $pos) {
            $upsertData[] = [
                'initiative_id' => $pos['initiative_id'],
                'x' => $pos['x'],
                'y' => $pos['y'],
                'is_locked' => $pos['is_locked'] ?? false,
            ];
        }

        if (!empty($upsertData)) {
            \Modules\ITSP\Models\TrsInitiativeRelationPosition::upsert(
                $upsertData,
                ['initiative_id'],
                ['x', 'y', 'is_locked']
            );
        }

        CacheManager::clearInitiativeCaches();

        return back()->with('success', 'Posisi diagram berhasil disimpan.');
    }
}
