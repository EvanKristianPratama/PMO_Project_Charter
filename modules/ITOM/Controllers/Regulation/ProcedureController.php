<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstActor;
use App\Models\MstSop;
use App\Models\TrsMapActorSop;
use App\Models\TrsSopCategory;
use App\Models\TrsTkoSections;
use App\Models\MstFunction;
use App\Services\Regulation\ProcedureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProcedureController extends Controller
{
    /**
     * @var ProcedureService
     */
    protected $procedureService;

    /**
     * ProcedureController constructor.
     *
     * @param ProcedureService $procedureService
     */
    public function __construct(ProcedureService $procedureService)
    {
        $this->procedureService = $procedureService;
    }

    /**
     * Display a listing of procedures.
     */
    public function index(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $data = $this->procedureService->getProcedureData($selectedRegulationId);

        return Inertia::render('modules/ITOM/Regulation/Procedure/Index', $data);
    }

    /**
     * Display the procedure management CRUD view.
     */
    public function manage(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $data = $this->procedureService->getProcedureData($selectedRegulationId);
        
        $data['functions'] = MstFunction::orderBy('name')->get();

        return Inertia::render('modules/ITOM/Regulation/Procedure/Manage', $data);
    }

    /**
     * Store a newly created actor.
     */
    public function storeActor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'organization_id' => 'nullable|exists:trs_organization,id',
            'regulation_id' => 'required|exists:mst_regulation,id',
            'function_ids' => 'nullable|array',
            'function_ids.*' => 'exists:mst_function,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:trs_organization,id',
        ]);

        $this->procedureService->createActor($validated);

        return back()->with('success', 'Aktor berhasil ditambahkan.');
    }

    /**
     * Update the specified actor.
     */
    public function updateActor(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'organization_id' => 'nullable|exists:trs_organization,id',
            'regulation_id' => 'required|exists:mst_regulation,id',
            'function_ids' => 'nullable|array',
            'function_ids.*' => 'exists:mst_function,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:trs_organization,id',
        ]);

        $actor = MstActor::findOrFail($id);
        $this->procedureService->updateActor($actor, $validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Aktor berhasil diperbarui.']);
        }

        return back()->with('success', 'Aktor berhasil diperbarui.');
    }

    /**
     * Remove the specified actor.
     */
    public function destroyActor(int $id): RedirectResponse
    {
        $actor = MstActor::findOrFail($id);
        $this->procedureService->deleteActor($actor);

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

        $this->procedureService->createCategory($validated);

        return back()->with('success', 'Kategori SOP berhasil ditambahkan.');
    }

    /**
     * Update the specified SOP Category.
     */
    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'tipe' => 'required|string|max:255',
        ]);

        $category = TrsSopCategory::findOrFail($id);
        $this->procedureService->updateCategory($category, $validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kategori SOP berhasil diperbarui.']);
        }

        return back()->with('success', 'Kategori SOP berhasil diperbarui.');
    }

    /**
     * Remove the specified SOP Category.
     */
    public function destroyCategory($id): RedirectResponse
    {
        $category = TrsSopCategory::findOrFail($id);
        $this->procedureService->deleteCategory($category);

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

        $this->procedureService->createSop($validated);
        $category = TrsSopCategory::findOrFail($validated['category_id']);

        return redirect()
            ->route('itom.policy.procedure.manage', ['regulation_id' => $category->regulation_id])
            ->with('success', 'SOP berhasil ditambahkan.');
    }

    /**
     * Update the specified SOP item.
     */
    public function updateSop(Request $request, int $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:trs_sop_category,id',
            'description' => 'required|string',
        ]);

        $sop = MstSop::findOrFail($id);
        $this->procedureService->updateSop($sop, $validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'SOP berhasil diperbarui.']);
        }

        $category = TrsSopCategory::findOrFail($validated['category_id']);

        return redirect()
            ->route('itom.policy.procedure.manage', ['regulation_id' => $category->regulation_id])
            ->with('success', 'SOP berhasil diperbarui.');
    }

    /**
     * Remove the specified SOP item.
     */
    public function destroySop(int $id): RedirectResponse
    {
        $sop = MstSop::findOrFail($id);
        $regulationId = $sop->category?->regulation_id;

        $this->procedureService->deleteSop($sop);

        return redirect()
            ->route('itom.policy.procedure.manage', $regulationId ? ['regulation_id' => $regulationId] : [])
            ->with('success', 'SOP berhasil dihapus.');
    }

    /**
     * Store a newly created flowchart mapping.
     */
    public function storeDiagram(Request $request): RedirectResponse
    {
        $validated = $this->validateDiagramMapping($request);

        $mapping = $this->procedureService->createDiagram($validated);

        if (!$mapping) {
            return back()
                ->withErrors(['actor_id' => 'Mapping aktor dan SOP untuk kategori ini sudah ada.'])
                ->withInput();
        }

        $sop = MstSop::findOrFail($validated['sop_id']);

        return redirect()
            ->route('itom.policy.procedure.manage', ['regulation_id' => $sop->category?->regulation_id])
            ->with('success', 'Mapping diagram berhasil ditambahkan.');
    }

    /**
     * Update the specified flowchart mapping.
     */
    public function updateDiagram(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateDiagramMapping($request);
        $mapping = TrsMapActorSop::findOrFail($id);

        $updated = $this->procedureService->updateDiagram($mapping, $validated);

        if (!$updated) {
            return back()
                ->withErrors(['actor_id' => 'Mapping aktor dan SOP untuk kategori ini sudah ada.'])
                ->withInput();
        }

        $sop = MstSop::findOrFail($validated['sop_id']);

        return redirect()
            ->route('itom.policy.procedure.manage', ['regulation_id' => $sop->category?->regulation_id])
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

        $this->procedureService->deleteDiagram($mapping);

        return redirect()
            ->route('itom.policy.procedure.manage', $regulationId ? ['regulation_id' => $regulationId] : [])
            ->with('success', 'Mapping diagram berhasil dihapus.');
    }

    private function validateDiagramMapping(Request $request): array
    {
        return $request->validate([
            'sop_id' => ['required', Rule::exists(MstSop::class, 'id')],
            'actor_id' => ['required', Rule::exists(MstActor::class, 'id')],
        ]);
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

        $this->procedureService->storeOrUpdateTkoContent($validated);

        return back()->with('success', 'Konten TKO berhasil disimpan.');
    }

    /**
     * Store a new TKO section.
     */
    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $this->procedureService->createSection($validated);

        return back()->with('success', 'Section TKO berhasil ditambahkan.');
    }

    /**
     * Update the specified TKO section.
     */
    public function updateSection(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $section = TrsTkoSections::findOrFail($id);
        $this->procedureService->updateSection($section, $validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Section TKO berhasil diperbarui.']);
        }

        return back()->with('success', 'Section TKO berhasil diperbarui.');
    }

    /**
     * Remove the specified TKO section.
     */
    public function destroySection($id): RedirectResponse
    {
        $section = TrsTkoSections::findOrFail($id);
        $this->procedureService->deleteSection($section);

        return back()->with('success', 'Section TKO berhasil dihapus.');
    }

    /**
     * Store or update structured TKO document sections and content.
     */
    public function saveStructuredDocument(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $validated = $request->validate([
            'regulation_id' => 'required|exists:mst_regulation,id',
            'sections' => 'required|array',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.order' => 'required|integer',
            'sections.*.content' => 'nullable|string',
        ]);

        $this->procedureService->saveStructuredDocument($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen TKO berhasil disimpan.'
            ]);
        }

        return back()->with('success', 'Dokumen TKO berhasil disimpan.');
    }
}
