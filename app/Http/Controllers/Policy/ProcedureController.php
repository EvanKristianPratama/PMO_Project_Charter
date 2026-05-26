<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstActor;
use App\Models\MstRegulation;
use App\Models\MstSop;
use App\Models\TrsMapActorSop;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProcedureController extends Controller
{
    /**
     * Display a listing of procedures.
     */
    public function index(Request $request): Response
    {
        $actors = MstActor::with('organization')->get();
        $sop = MstSop::with('regulation.organization')
            ->orderBy('tipe')
            ->orderBy('id')
            ->get();
        $flowChartSops = MstSop::with(['mapActorSops.actor.organization'])
            ->whereIn('tipe', ['A', 'B'])
            ->orderBy('tipe')
            ->orderBy('id')
            ->get();
        $regulations = MstRegulation::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        return Inertia::render('Policy/Procedure/Index', [
            'actors' => $actors,
            'sop' => $sop,
            'flowChartSops' => $flowChartSops,
            'regulations' => $regulations,
            'organizations' => $organizations,
            'selectedRegulationId' => $selectedRegulation?->id,
        ]);
    }

    /**
     * Display the procedure management CRUD view.
     */
    public function manage(Request $request): Response
    {
        $actors = MstActor::with('organization')->get();
        $sop = MstSop::with('regulation.organization')
            ->orderBy('tipe')
            ->orderBy('id')
            ->get();
        $flowChartSops = MstSop::with(['mapActorSops.actor.organization'])
            ->whereIn('tipe', ['A', 'B'])
            ->orderBy('tipe')
            ->orderBy('id')
            ->get();
        $regulations = MstRegulation::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        return Inertia::render('Policy/Procedure/Manage', [
            'actors' => $actors,
            'sop' => $sop,
            'flowChartSops' => $flowChartSops,
            'regulations' => $regulations,
            'organizations' => $organizations,
            'selectedRegulationId' => $selectedRegulation?->id,
        ]);
    }

    /**
     * Store a newly created actor.
     */
    public function storeActor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:trs_organization,id',
        ]);

        MstActor::create($validated);

        return back()->with('success', 'Aktor berhasil ditambahkan.');
    }

    /**
     * Update the specified actor.
     */
    public function updateActor(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:trs_organization,id',
        ]);

        $actor = MstActor::findOrFail($id);
        $actor->update($validated);

        return back()->with('success', 'Aktor berhasil diperbarui.');
    }

    /**
     * Remove the specified actor.
     */
    public function destroyActor(int $id): RedirectResponse
    {
        $actor = MstActor::findOrFail($id);
        $actor->delete();

        return back()->with('success', 'Aktor berhasil dihapus.');
    }

    /**
     * Store a newly created SOP item.
     */
    public function storeSop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'regulation_id' => 'nullable|exists:mst_regulation,id',
            'tipe' => 'required|in:A,B',
            'description' => 'required|string',
        ]);

        MstSop::create($validated);

        return back()->with('success', 'SOP berhasil ditambahkan.');
    }

    /**
     * Update the specified SOP item.
     */
    public function updateSop(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'regulation_id' => 'nullable|exists:mst_regulation,id',
            'tipe' => 'required|in:A,B',
            'description' => 'required|string',
        ]);

        $sop = MstSop::findOrFail($id);
        $sop->update($validated);

        return back()->with('success', 'SOP berhasil diperbarui.');
    }

    /**
     * Remove the specified SOP item.
     */
    public function destroySop(int $id): RedirectResponse
    {
        $sop = MstSop::findOrFail($id);

        TrsMapActorSop::where('sop_id', $sop->id)->delete();
        $sop->delete();

        return back()->with('success', 'SOP berhasil dihapus.');
    }

    /**
     * Store a newly created flowchart mapping.
     */
    public function storeDiagram(Request $request): RedirectResponse
    {
        $validated = $this->validateDiagramMapping($request);

        if ($this->diagramMappingExists($validated)) {
            return back()
                ->withErrors(['actor_id' => 'Mapping aktor dan SOP untuk kategori ini sudah ada.'])
                ->withInput();
        }

        TrsMapActorSop::create($validated);

        return back()->with('success', 'Mapping diagram berhasil ditambahkan.');
    }

    /**
     * Update the specified flowchart mapping.
     */
    public function updateDiagram(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateDiagramMapping($request);
        $mapping = TrsMapActorSop::findOrFail($id);

        if ($this->diagramMappingExists($validated, $mapping->id)) {
            return back()
                ->withErrors(['actor_id' => 'Mapping aktor dan SOP untuk kategori ini sudah ada.'])
                ->withInput();
        }

        $mapping->update($validated);

        return back()->with('success', 'Mapping diagram berhasil diperbarui.');
    }

    /**
     * Remove the specified flowchart mapping.
     */
    public function destroyDiagram(int $id): RedirectResponse
    {
        $mapping = TrsMapActorSop::findOrFail($id);
        $mapping->delete();

        return back()->with('success', 'Mapping diagram berhasil dihapus.');
    }

    private function validateDiagramMapping(Request $request): array
    {
        return $request->validate([
            'tipe' => ['required', Rule::in(['A', 'B'])],
            'sop_id' => [
                'required',
                Rule::exists('mst_sop', 'id')->where(fn ($query) => $query->where('tipe', $request->input('tipe'))),
            ],
            'actor_id' => ['required', Rule::exists('mst_actor', 'id')],
        ]);
    }

    private function diagramMappingExists(array $mapping, ?int $ignoreId = null): bool
    {
        return TrsMapActorSop::query()
            ->where('tipe', $mapping['tipe'])
            ->where('sop_id', $mapping['sop_id'])
            ->where('actor_id', $mapping['actor_id'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
