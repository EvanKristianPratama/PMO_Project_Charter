<?php

namespace Modules\ITOM\Controllers\OperatingModel;

use App\Http\Controllers\Controller;
use App\Services\OperatingModel\ItGovernance\ItGovernance;
use App\Services\OperatingModel\ItManagement\ItManagement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperatingModelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("modules/ITOM/OperatingModel/Framework/Index");
    }

    public function framework(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/Framework/Index');
    }

    public function itGovernance(ItGovernance $itGovernanceService): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/ItGovernance/Index', [
            "steeringRows" => $itGovernanceService->getSteeringRows(),
            "organizationOptions" => $itGovernanceService->getOrganizationOptions(),
        ]);
    }

    public function itManagement(ItManagement $itManagementService): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/ItManagement/Index', [
            "organizationStructureRows" => $itManagementService->getOrganizationStructureRows(),
        ]);
    }

    public function storeSteering(Request $request, ItGovernance $itGovernanceService)
    {
        $validated = $request->validate([
            "organization_id" => "required|exists:trs_organization,id",
            "code" => "required|string|size:8",
        ]);

        $itGovernanceService->storeSteering($validated);

        return redirect()
            ->back()
            ->with("success", "Data Steering Committee berhasil ditambahkan.");
    }

    public function updateSteering(Request $request, $id, ItGovernance $itGovernanceService)
    {
        $validated = $request->validate([
            "organization_id" => "required|exists:trs_organization,id",
            "code" => "required|string|size:8",
        ]);

        $itGovernanceService->updateSteering((int) $id, $validated);

        return redirect()
            ->back()
            ->with("success", "Data Steering Committee berhasil diperbarui.");
    }

    public function destroySteering($id, ItGovernance $itGovernanceService)
    {
        $itGovernanceService->destroySteering((int) $id);

        return redirect()
            ->back()
            ->with("success", "Data Steering Committee berhasil dihapus.");
    }
}
