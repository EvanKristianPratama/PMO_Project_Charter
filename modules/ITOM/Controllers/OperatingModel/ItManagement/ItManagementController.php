<?php

namespace Modules\ITOM\Controllers\OperatingModel\ItManagement;

use App\Http\Controllers\Controller;
use Modules\ITOM\Services\OperatingModel\ItManagement\ItManagement;
use Inertia\Inertia;
use Inertia\Response;

class ItManagementController extends Controller
{
    public function index(ItManagement $itManagementService): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/ItManagement/Index', [
            "organizationStructureRows" => Inertia::defer(fn() => $itManagementService->getOrganizationStructureRows()),
        ]);
    }
}
