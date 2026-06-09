<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstActor;
use App\Models\MstRegulation;
use App\Models\MstSop;
use App\Models\TrsMapActorSop;
use App\Models\TrsOrganization;
use App\Models\TrsSopCategory;
use App\Models\TrsTkoSections;
use App\Models\TrsTkoContent;
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
        $regulations = MstRegulation::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->firstWhere('tipe', 'Procedure');
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        $actorsQuery = MstActor::with('organization');
        if ($selectedRegulation) {
            $actorsQuery->where('regulation_id', $selectedRegulation->id);
        }
        $actors = $actorsQuery->get();

        $categories = [];
        if ($selectedRegulation) {
            $categories = TrsSopCategory::where('regulation_id', $selectedRegulation->id)
                ->orderBy('id')
                ->get();
        }

        $sopQuery = MstSop::with(['category', 'regulation.organization']);
        $flowChartSopsQuery = MstSop::with(['category', 'mapActorSops.actor.organization']);

        if ($selectedRegulation) {
            $sopQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
            $flowChartSopsQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
        } else {
            $sopQuery->whereNull('category_id');
            $flowChartSopsQuery->whereNull('category_id');
        }

        $sop = $sopQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $flowChartSops = $flowChartSopsQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $tkoSections = TrsTkoSections::with(['contents' => function ($q) use ($selectedRegulation) {
            if ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            }
        }])
        ->orderBy('order')
        ->get();

        return Inertia::render('Policy/Procedure/Index', [
            'actors' => $actors,
            'sop' => $sop,
            'flowChartSops' => $flowChartSops,
            'regulations' => $regulations,
            'organizations' => $organizations,
            'selectedRegulationId' => $selectedRegulation?->id,
            'categories' => $categories,
            'tkoSections' => $tkoSections,
        ]);
    }

    /**
     * Display the procedure management CRUD view.
     */
    public function manage(Request $request): Response
    {
        $regulations = MstRegulation::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->firstWhere('tipe', 'Procedure');
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        $actorsQuery = MstActor::with('organization');
        if ($selectedRegulation) {
            $actorsQuery->where('regulation_id', $selectedRegulation->id);
        }
        $actors = $actorsQuery->get();

        $categories = [];
        if ($selectedRegulation) {
            $categories = TrsSopCategory::where('regulation_id', $selectedRegulation->id)
                ->orderBy('id')
                ->get();
        }

        $sopQuery = MstSop::with(['category', 'regulation.organization']);
        $flowChartSopsQuery = MstSop::with(['category', 'mapActorSops.actor.organization']);

        if ($selectedRegulation) {
            $sopQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
            $flowChartSopsQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
        } else {
            $sopQuery->whereNull('category_id');
            $flowChartSopsQuery->whereNull('category_id');
        }

        $sop = $sopQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $flowChartSops = $flowChartSopsQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $tkoSections = TrsTkoSections::with(['contents' => function ($q) use ($selectedRegulation) {
            if ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            }
        }])
        ->orderBy('order')
        ->get();

        return Inertia::render('Policy/Procedure/Manage', [
            'actors' => $actors,
            'sop' => $sop,
            'flowChartSops' => $flowChartSops,
            'regulations' => $regulations,
            'organizations' => $organizations,
            'selectedRegulationId' => $selectedRegulation?->id,
            'categories' => $categories,
            'tkoSections' => $tkoSections,
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
            'regulation_id' => 'required|exists:mst_regulation,id',
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
            'regulation_id' => 'required|exists:mst_regulation,id',
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
     * Store a newly created SOP Category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'regulation_id' => 'required|exists:mst_regulation,id',
            'tipe' => 'required|string|max:255',
        ]);

        TrsSopCategory::create($validated);

        return back()->with('success', 'Kategori SOP berhasil ditambahkan.');
    }

    /**
     * Update the specified SOP Category.
     */
    public function updateCategory(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|string|max:255',
        ]);

        $category = TrsSopCategory::findOrFail($id);
        $category->update($validated);

        return back()->with('success', 'Kategori SOP berhasil diperbarui.');
    }

    /**
     * Remove the specified SOP Category.
     */
    public function destroyCategory($id): RedirectResponse
    {
        $category = TrsSopCategory::findOrFail($id);

        foreach ($category->procedure as $sop) {
            TrsMapActorSop::where('sop_id', $sop->id)->delete();
            $sop->delete();
        }

        $category->delete();

        return back()->with('success', 'Kategori SOP berhasil dihapus.');
    }

    /**
     * Store a newly created SOP item.
     */
    public function storeSop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:trs_sop_category,id',
            'description' => 'required|string',
        ]);

        $sop = MstSop::create($validated);
        $category = TrsSopCategory::findOrFail($validated['category_id']);

        return redirect()
            ->route('policy.procedure.manage', ['regulation_id' => $category->regulation_id])
            ->with('success', 'SOP berhasil ditambahkan.');
    }

    /**
     * Update the specified SOP item.
     */
    public function updateSop(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:trs_sop_category,id',
            'description' => 'required|string',
        ]);

        $sop = MstSop::findOrFail($id);
        $sop->update($validated);

        $category = TrsSopCategory::findOrFail($validated['category_id']);

        return redirect()
            ->route('policy.procedure.manage', ['regulation_id' => $category->regulation_id])
            ->with('success', 'SOP berhasil diperbarui.');
    }

    /**
     * Remove the specified SOP item.
     */
    public function destroySop(int $id): RedirectResponse
    {
        $sop = MstSop::findOrFail($id);
        $regulationId = $sop->category?->regulation_id;

        TrsMapActorSop::where('sop_id', $sop->id)->delete();
        $sop->delete();

        return redirect()
            ->route('policy.procedure.manage', $regulationId ? ['regulation_id' => $regulationId] : [])
            ->with('success', 'SOP berhasil dihapus.');
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

        $sop = MstSop::findOrFail($validated['sop_id']);
        $validated['tipe'] = $sop->category?->tipe ?? 'A';

        TrsMapActorSop::create($validated);

        return redirect()
            ->route('policy.procedure.manage', ['regulation_id' => $sop->category?->regulation_id])
            ->with('success', 'Mapping diagram berhasil ditambahkan.');
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

        $sop = MstSop::findOrFail($validated['sop_id']);
        $validated['tipe'] = $sop->category?->tipe ?? 'A';

        $mapping->update($validated);

        return redirect()
            ->route('policy.procedure.manage', ['regulation_id' => $sop->category?->regulation_id])
            ->with('success', 'Mapping diagram berhasil diperbarui.');
    }

    /**
     * Remove the specified flowchart mapping.
     */
    public function destroyDiagram(int $id): RedirectResponse
    {
        $mapping = TrsMapActorSop::findOrFail($id);
        $sop = MstSop::find($mapping->sop_id);
        $regulationId = $sop ? $sop->category?->regulation_id : null;

        $mapping->delete();

        return redirect()
            ->route('policy.procedure.manage', $regulationId ? ['regulation_id' => $regulationId] : [])
            ->with('success', 'Mapping diagram berhasil dihapus.');
    }

    private function validateDiagramMapping(Request $request): array
    {
        return $request->validate([
            'sop_id' => ['required', Rule::exists('mst_sop', 'id')],
            'actor_id' => ['required', Rule::exists('mst_actor', 'id')],
        ]);
    }

    private function diagramMappingExists(array $mapping, ?int $ignoreId = null): bool
    {
        return TrsMapActorSop::query()
            ->where('sop_id', $mapping['sop_id'])
            ->where('actor_id', $mapping['actor_id'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    /**
     * Store or update TKO content.
     */
    public function storeOrUpdateTkoContent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'regulation_id' => 'required|exists:mst_regulation,id',
            'section_id' => 'required|exists:trs_tko_sections,id',
            'content' => 'nullable|string',
        ]);

        TrsTkoContent::updateOrCreate(
            [
                'regulation_id' => $validated['regulation_id'],
                'section_id' => $validated['section_id'],
            ],
            [
                'content' => $validated['content'],
            ]
        );

        return back()->with('success', 'Konten TKO berhasil disimpan.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        TrsTkoSections::create($validated);

        return back()->with('success', 'Section TKO berhasil ditambahkan.');
    }

    /**
     * Update the specified TKO section.
     */
    public function updateSection(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $section = TrsTkoSections::findOrFail($id);
        $section->update($validated);

        return back()->with('success', 'Section TKO berhasil diperbarui.');
    }

    /**
     * Remove the specified TKO section.
     */
    public function destroySection($id): RedirectResponse
    {
        $section = TrsTkoSections::findOrFail($id);
        
        // Delete associated contents first
        TrsTkoContent::where('section_id', $section->id)->delete();
        
        $section->delete();

        return back()->with('success', 'Section TKO berhasil dihapus.');
    }
}
