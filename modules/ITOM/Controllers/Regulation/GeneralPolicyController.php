<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstGeneralPolicy;
use App\Services\Regulation\GeneralPolicyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GeneralPolicyController extends Controller
{
    /**
     * @var GeneralPolicyService
     */
    protected $generalPolicyService;

    /**
     * GeneralPolicyController constructor.
     *
     * @param GeneralPolicyService $generalPolicyService
     */
    public function __construct(GeneralPolicyService $generalPolicyService)
    {
        $this->generalPolicyService = $generalPolicyService;
    }

    /**
     * Display a listing of general policies in document view mode.
     */
    public function index(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $data = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', $data);
    }

    /**
     * Display a listing of general policies for CRUD management.
     */
    public function manage(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $data = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/General/Manage', $data);
    }

    /**
     * Store a newly created general policy.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'regulation_id' => 'required|integer|exists:mst_regulation,id',
            'number' => 'required|integer',
            'description' => 'required|string',
        ], [
            'regulation_id.required' => 'Regulasi wajib dipilih.',
            'number.required' => 'Nomor Kebijakan wajib diisi.',
            'number.integer' => 'Nomor Kebijakan harus berupa angka.',
            'description.required' => 'Deskripsi Kebijakan wajib diisi.',
        ]);

        $this->generalPolicyService->createGeneralPolicy($validated);

        return redirect()
            ->route('itom.policy.general.manage', ['regulation_id' => $request->regulation_id])
            ->with('success', 'Kebijakan Umum berhasil ditambahkan.');
    }

    /**
     * Update the specified general policy.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $policy = MstGeneralPolicy::findOrFail($id);

        $validated = $request->validate([
            'regulation_id' => 'required|integer|exists:mst_regulation,id',
            'number' => 'required|integer',
            'description' => 'required|string',
        ], [
            'regulation_id.required' => 'Regulasi wajib dipilih.',
            'number.required' => 'Nomor Kebijakan wajib diisi.',
            'number.integer' => 'Nomor Kebijakan harus berupa angka.',
            'description.required' => 'Deskripsi Kebijakan wajib diisi.',
        ]);

        $this->generalPolicyService->updateGeneralPolicy($policy, $validated);

        return redirect()
            ->route('itom.policy.general.manage', ['regulation_id' => $request->regulation_id])
            ->with('success', 'Kebijakan Umum berhasil diperbarui.');
    }

    /**
     * Remove the specified general policy.
     */
    public function destroy(int $id): RedirectResponse
    {
        $policy = MstGeneralPolicy::findOrFail($id);
        $regulationId = $policy->regulation_id;
        $this->generalPolicyService->deleteGeneralPolicy($policy);

        return redirect()
            ->route('itom.policy.general.manage', ['regulation_id' => $regulationId])
            ->with('success', 'Kebijakan Umum berhasil dihapus.');
    }

    /**
     * Display the Introduction chapter (Bab I).
     */
    public function introduction(): Response
    {
        $data = $this->generalPolicyService->getGuidanceChapterData();

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', $data);
    }

    /**
     * Display the Closing chapter (Bab V).
     */
    public function closing(): Response
    {
        $data = $this->generalPolicyService->getGuidanceChapterData();

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', $data);
    }
}
