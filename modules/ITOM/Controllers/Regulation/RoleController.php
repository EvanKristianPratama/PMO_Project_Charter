<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstRole;
use App\Models\TrsResponsibility;
use App\Services\Regulation\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var \App\Services\Regulation\GeneralPolicyService
     */
    protected $generalPolicyService;

    /**
     * RoleController constructor.
     */
    public function __construct(RoleService $roleService, \App\Services\Regulation\GeneralPolicyService $generalPolicyService)
    {
        $this->roleService = $roleService;
        $this->generalPolicyService = $generalPolicyService;
    }

    /**
     * Display a listing of roles & responsibilities in formal document view mode.
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
     * Display the roles & responsibilities management CRUD view.
     */
    public function manage(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id');
        $data = $this->roleService->getRoleManageData($selectedRegulationId);

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Role/Manage', $data);
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        $this->roleService->createRole($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        $this->roleService->updateRole($role, $validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role.
     */
    public function destroyRole(int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);
        $this->roleService->deleteRole($role);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Store a newly created responsibility.
     */
    public function storeResponsibility(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:mst_roles,id',
            'content' => 'required|string',
        ], [
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        $this->roleService->createResponsibility($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil ditambahkan.');
    }

    /**
     * Update the specified responsibility.
     */
    public function updateResponsibility(Request $request, int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        $this->roleService->updateResponsibility($responsibility, $validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil diperbarui.');
    }

    /**
     * Remove the specified responsibility.
     */
    public function destroyResponsibility(int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);
        $this->roleService->deleteResponsibility($responsibility);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil dihapus.');
    }

    /**
     * Store a newly created role mapping to a master responsible.
     */
    public function storeMappedResponsible(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:mst_roles,id',
            'responsible_id' => 'nullable|integer',
            'responsible_ids' => 'nullable|array',
            'responsible_ids.*' => 'integer',
        ], [
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'responsible_ids.array' => 'Daftar Master Responsible tidak valid.',
        ]);

        $ids = [];
        if (!empty($validated['responsible_ids'])) {
            $ids = $validated['responsible_ids'];
        } elseif (!empty($validated['responsible_id'])) {
            $ids = [$validated['responsible_id']];
        }

        if (empty($ids)) {
            return redirect()
                ->route('itom.policy.roles.manage')
                ->with('error', 'Silakan pilih minimal satu Master Responsible.');
        }

        $role = MstRole::findOrFail($validated['role_id']);
        $result = $this->roleService->storeMappedResponsible($role, $ids);

        if (!$result['success']) {
            return redirect()
                ->route('itom.policy.roles.manage')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', $result['message']);
    }

    /**
     * Remove the specified role mapping from a master responsible.
     */
    public function destroyMappedResponsible(int $roleId, int $responsibleId): RedirectResponse
    {
        $role = MstRole::findOrFail($roleId);
        $this->roleService->destroyMappedResponsible($role, $responsibleId);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Pemetaan Master Responsible berhasil dihapus.');
    }

    /**
     * Update mapping between responsibilities (Bab 3) and policies/objectives (Bab 2).
     */
    public function updateResponsiblePractice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mappings' => 'required|array',
            'mappings.*.responsible_id' => 'required|integer',
            'mappings.*.objective_ids' => 'present|array',
            'mappings.*.objective_ids.*' => 'string',
        ]);

        try {
            $this->roleService->updateResponsiblePractice($validated['mappings']);

            return redirect()
                ->route('itom.policy.roles.index')
                ->with('success', 'Pemetaan Tanggung Jawab vs Kebijakan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[RoleController] Error updating responsible objective mapping: ' . $e->getMessage());
            return redirect()
                ->route('itom.policy.roles.index')
                ->with('error', $e->getMessage() ?: 'Gagal memperbarui pemetaan Tanggung Jawab vs Kebijakan.');
        }
    }

    /**
     * Update mapping of responsibilities for a single objective (row-by-row).
     */
    public function updateObjectiveResponsibles(Request $request, string $objectiveId): RedirectResponse
    {
        $validated = $request->validate([
            'responsible_ids' => 'present|array',
            'responsible_ids.*' => 'integer',
        ]);

        try {
            $this->roleService->updateObjectiveResponsibles($objectiveId, $validated['responsible_ids']);

            return redirect()
                ->route('itom.policy.roles.index')
                ->with('success', 'Pemetaan Tanggung Jawab berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[RoleController] Error updating single objective responsibles mapping: ' . $e->getMessage());
            return redirect()
                ->route('itom.policy.roles.index')
                ->with('error', $e->getMessage() ?: 'Gagal memperbarui pemetaan Tanggung Jawab.');
        }
    }
}
