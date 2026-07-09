<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstGeneralPolicy;
use App\Services\Regulation\GeneralPolicyService;
use App\Services\Regulation\RoleService;
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
     * @var RoleService
     */
    protected $roleService;

    /**
     * GeneralPolicyController constructor.
     */
    public function __construct(GeneralPolicyService $generalPolicyService, RoleService $roleService)
    {
        $this->generalPolicyService = $generalPolicyService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of general policies in document view mode.
     */
    public function index(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        if (!$selectedRegulationId) {
            $selectedRegulationId = null;
        }

        $policyData = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);
        $resolvedRegId = $policyData['selectedRegulationId'] ?? $selectedRegulationId;

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', [
            'selectedRegulationId' => $resolvedRegId,
            'regulations' => Inertia::defer(fn() => $policyData['regulations']),
            'policies' => Inertia::defer(fn() => $policyData['policies']),
            'objectives' => Inertia::defer(fn() => $policyData['objectives']),
            'roles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['roles']),
            'responsibles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['responsibles']),
        ]);
    }

    /**
     * Display a listing of general policies for CRUD management.
     */
    public function manage(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $policyData = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);
        $resolvedRegId = $policyData['selectedRegulationId'] ?? $selectedRegulationId;

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Manage', [
            'selectedRegulationId' => $resolvedRegId,
            'regulations' => Inertia::defer(fn() => $policyData['regulations']),
            'policies' => Inertia::defer(fn() => $policyData['policies']),
        ]);
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
    public function introduction(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        if (!$selectedRegulationId) {
            $selectedRegulationId = null;
        }

        $policyData = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);
        $resolvedRegId = $policyData['selectedRegulationId'] ?? $selectedRegulationId;

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', [
            'selectedRegulationId' => $resolvedRegId,
            'regulations' => Inertia::defer(fn() => $policyData['regulations']),
            'policies' => Inertia::defer(fn() => $policyData['policies']),
            'objectives' => Inertia::defer(fn() => $policyData['objectives']),
            'roles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['roles']),
            'responsibles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['responsibles']),
        ]);
    }

    /**
     * Display the Closing chapter (Bab V).
     */
    public function closing(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        if (!$selectedRegulationId) {
            $selectedRegulationId = null;
        }

        $policyData = $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId);
        $resolvedRegId = $policyData['selectedRegulationId'] ?? $selectedRegulationId;

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index', [
            'selectedRegulationId' => $resolvedRegId,
            'regulations' => Inertia::defer(fn() => $policyData['regulations']),
            'policies' => Inertia::defer(fn() => $policyData['policies']),
            'objectives' => Inertia::defer(fn() => $policyData['objectives']),
            'roles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['roles']),
            'responsibles' => Inertia::defer(fn() => $this->roleService->getRoleIndexData($resolvedRegId)['responsibles']),
        ]);
    }
}
