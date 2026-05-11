<?php

namespace App\Http\Controllers\StrategicHouse\ItBuildingBlock;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Services\StrategicHouse\ItBuildingBlockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        protected ItBuildingBlockService $itBuildingBlockService
    ) {}

    public function __invoke(): Response
    {
        return Inertia::render('StrategicHouse/ItBuildingBlocks/Index', [
            'groups' => $this->itBuildingBlockService->getGroupedMappings(),
            'coeOptions' => $this->itBuildingBlockService->getCoeOptions(),
            'initiativeOptions' => $this->itBuildingBlockService->getItInitiativeOptions(),
            'statusPeriods' => $this->itBuildingBlockService->getStatusPeriods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary' => ['required', 'integer', 'exists:mst_coe,id'],
            'secondary' => ['required', 'integer', 'exists:mst_coe,id', 'different:primary'],
            'initiative_id' => ['nullable', 'integer', 'exists:mst_initiative,id'],
            'initiative_ids' => ['nullable', 'array', 'min:1'],
            'initiative_ids.*' => ['required', 'integer', 'exists:mst_initiative,id'],
        ]);

        $createdCount = $this->itBuildingBlockService->storeMapping($validated);

        return back()->with('success', $createdCount > 1
            ? "{$createdCount} mapping IT Building Block berhasil ditambahkan."
            : 'Mapping IT Building Block berhasil ditambahkan.');
    }

    public function destroyPrimary(MstCoe $primary): RedirectResponse
    {
        $this->itBuildingBlockService->deletePrimary($primary->id);

        return back()->with('success', 'Primary IT Building Block berhasil dihapus.');
    }

    public function destroySecondary(MstCoe $primary, MstCoe $secondary): RedirectResponse
    {
        $this->itBuildingBlockService->deleteSecondary($primary->id, $secondary->id);

        return back()->with('success', 'Secondary IT Building Block berhasil dihapus.');
    }

    public function destroyInitiative(MstCoe $primary, MstCoe $secondary, MstInitiative $initiative): RedirectResponse
    {
        $this->itBuildingBlockService->deleteInitiative($primary->id, $secondary->id, $initiative->id);

        return back()->with('success', 'Mapping initiative berhasil dihapus.');
    }

    public function destroyInitiatives(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'removals' => ['required', 'array', 'min:1'],
            'removals.*.primary' => ['required', 'integer', 'exists:mst_coe,id'],
            'removals.*.secondary' => ['required', 'integer', 'exists:mst_coe,id'],
            'removals.*.initiative_id' => ['required', 'integer', 'exists:mst_initiative,id'],
        ]);

        $this->itBuildingBlockService->deleteMultipleMappings($validated['removals']);

        return back()->with('success', 'Penghapusan initiative berhasil disimpan.');
    }
}
