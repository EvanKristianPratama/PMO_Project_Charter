<?php

namespace Modules\ITOM\Controllers\OperatingModel\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstRegulation;
use App\Services\Regulation\GeneralPolicyService;
use App\Services\Regulation\RoleService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PolicyController extends Controller
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
     * PolicyController constructor.
     */
    public function __construct(GeneralPolicyService $generalPolicyService, RoleService $roleService)
    {
        $this->generalPolicyService = $generalPolicyService;
        $this->roleService = $roleService;
    }

    /**
     * Display the view-only operating model policy page with deferred loading.
     */
    public function index(Request $request): Response
    {
        $selectedRegulationId = $request->integer('regulation_id', 1);

        // Enforce only Regulation ID 1 (Pedoman Tata Kelola) and 4 (Pedoman Pengelolaan IT)
        if (! in_array($selectedRegulationId, [1, 4])) {
            $selectedRegulationId = 1;
        }

        $activeChapter = $request->input('chapter', 'bab1');
        if (! in_array($activeChapter, ['bab1', 'bab2', 'bab3', 'bab4'])) {
            $activeChapter = 'bab1';
        }

        return Inertia::render('modules/ITOM/OperatingModel/Policy/Index', [
            'selectedRegulationId' => $selectedRegulationId,
            'activeChapter' => $activeChapter,

            // Defer all database-bound data
            'regulations' => Inertia::defer(fn () => MstRegulation::select(['id', 'judul', 'nomor', 'tipe', 'owner', 'revisi', 'berlaku'])
                ->whereIn('id', [1, 4])
                ->orderBy('id', 'asc')
                ->get()),
            'policies' => Inertia::defer(fn () => $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId)['policies']),
            'objectives' => Inertia::defer(fn () => $this->generalPolicyService->getGeneralPolicyData($selectedRegulationId)['objectives']),
            'roles' => Inertia::defer(fn () => $this->roleService->getRoleIndexData($selectedRegulationId)['roles']),
            'responsibles' => Inertia::defer(fn () => $this->roleService->getRoleIndexData($selectedRegulationId)['responsibles']),
        ]);
    }
}
