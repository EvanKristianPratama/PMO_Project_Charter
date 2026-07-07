<?php

namespace Modules\ITOM\Controllers\OperatingModel\ItGovarnence;

use App\Http\Controllers\Controller;
use App\Models\MstBod;
use App\Models\MstCompany;
use App\Models\MstFunction;
use App\Models\MstFunctionalOrganization;
use App\Models\MstRegulation;
use App\Services\OperatingModel\ItGovernance\ItGovernance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItGovarnenceController extends Controller
{
    public function index(ItGovernance $itGovernanceService): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/ItGovernance/Index', [
            // Steering Committee data
            "steeringRows" => Inertia::defer(fn() => $itGovernanceService->getSteeringRows()),
            "organizationOptions" => Inertia::defer(fn() => $itGovernanceService->getOrganizationOptions()),

            // Functional Organization data
            'companies' => Inertia::defer(fn () => MstCompany::with('parent')->orderBy('id', 'asc')->get()->map(fn ($c) => [
                'id' => $c->id,
                'parent_id' => $c->parent_id,
                'parent_name' => $c->parent?->name,
                'name' => $c->name,
                'organization' => $c->organization,
                'singkatan' => $c->singkatan,
                'grup' => $c->grup,
                'level' => $c->level,
            ])->values()->all()),
            'bods' => Inertia::defer(fn () => MstBod::with('company')->orderBy('id', 'asc')->get()->map(fn ($b) => [
                'id' => $b->id,
                'company_id' => $b->company_id,
                'parent_id' => $b->parent_id,
                'company_name' => $b->company?->name,
                'name' => $b->name,
                'nama_jabatan' => $b->nama_jabatan,
                'alias' => $b->alias,
                'sumber' => $b->sumber,
                'pejabat' => $b->pejabat,
                'grup_function' => $b->grup_function,
                'role_function' => $b->role_function,
                'tipe' => $b->tipe,
                'regulation_id' => $b->regulation_id,
                'order' => $b->order,
            ])->values()->all()),
            'regulations' => Inertia::defer(fn () => MstRegulation::select(['id', 'nomor', 'judul'])->orderBy('nomor', 'asc')->get()->map(fn ($r) => [
                'id' => $r->id,
                'nomor' => $r->nomor,
                'judul' => $r->judul,
            ])->values()->all()),
            'functionalOrganizations' => Inertia::defer(fn () => MstFunctionalOrganization::with([
                'company:id,name',
                'regulation:id,nomor,judul',
                'trsFunctionalStructures',
                'trsFunctionalOrganizations.organization.company',
                'trsFunctionalFunctions.functionModel.company'
            ])->orderBy('id', 'asc')->get()->map(function ($f) {
                return [
                    'id' => $f->id,
                    'company_id' => $f->company_id,
                    'company_name' => $f->company?->name,
                    'regulation_id' => $f->regulation_id,
                    'regulation_name' => $f->regulation ? ($f->regulation->nomor ? $f->regulation->nomor . ' - ' . $f->regulation->judul : $f->regulation->judul) : null,
                    'name' => $f->name,
                    'functions' => $f->trsFunctionalStructures->map(fn ($tfs) => [
                        'structure_id' => $tfs->id,
                        'functional_id' => $tfs->functional_id,
                        'parent_id' => $tfs->parent_id,
                        'name' => $tfs->name,
                    ])->values()->all(),
                    'members' => $f->trsFunctionalOrganizations->map(function ($trs) {
                        return [
                            'structure_id'    => $trs->structure_id,
                            'organization_id' => $trs->organization_id,
                            'member_type'     => 'bod',
                            'name'            => $trs->organization?->name,
                            'pejabat'         => $trs->organization?->pejabat,
                            'company_name'    => $trs->organization?->company?->name,
                            'grup_function'   => $trs->organization?->grup_function,
                        ];
                    })->values()->all(),
                    'assigned_functions' => $f->trsFunctionalFunctions->map(function ($trsf) {
                        return [
                            'functional_id' => $trsf->functional_id,
                            'function_id'   => $trsf->function_id,
                            'member_type'   => 'function',
                            'name'          => $trsf->functionModel?->name,
                            'company_name'  => $trsf->functionModel?->company?->name,
                        ];
                    })->values()->all(),
                ];
            })->values()->all()),
            'functions' => Inertia::defer(fn () => MstFunction::orderBy('name')->get()->map(fn ($fun) => [
                'id' => $fun->id,
                'name' => $fun->name,
                'company_id' => $fun->company_id,
            ])->values()->all()),
        ]);
    }

    // ─── Steering Committee CRUD ─────────────────────────────────────────────────

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

