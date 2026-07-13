<?php

namespace Modules\ITOM\Controllers\Organization\FunctionalOrganization;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstFunctionalOrganization;
use Modules\ITOM\Models\MstRegulation;

use Modules\ITOM\Models\MstCompany;
use Modules\ITOM\Models\MstBod;
use Modules\ITOM\Models\MstFunction;
use Modules\ITOM\Services\Organization\FunctionalOrganization\FunctionalOrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FunctionalOrganizationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/FunctionalOrganization/Index', [
            'companies' => Inertia::defer(fn () => MstCompany::select(['id', 'parent_id', 'name', 'organization', 'singkatan', 'grup', 'level'])
                ->with('parent:id,name')
                ->orderBy('id', 'asc')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'parent_id' => $c->parent_id,
                    'parent_name' => $c->parent?->name,
                    'name' => $c->name,
                    'organization' => $c->organization,
                    'singkatan' => $c->singkatan,
                    'grup' => $c->grup,
                    'level' => $c->level,
                ])->values()->all()),
            'bods' => Inertia::defer(fn () => MstBod::select(['id', 'company_id', 'parent_id', 'name', 'nama_jabatan', 'alias', 'sumber', 'pejabat', 'grup_function', 'role_function', 'tipe', 'regulation_id', 'order'])
                ->with('company:id,name')
                ->orderBy('id', 'asc')
                ->get()
                ->map(fn ($b) => [
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
            'functionalOrganizations' => Inertia::defer(fn () => MstFunctionalOrganization::select(['id', 'company_id', 'regulation_id', 'name'])
                ->with([
                    'company:id,name',
                    'regulation:id,nomor,judul',
                    'trsFunctionalStructures:id,functional_id,parent_id,name',
                    'trsFunctionalOrganizations:structure_id,organization_id',
                    'trsFunctionalOrganizations.organization:id,name,pejabat,company_id,grup_function',
                    'trsFunctionalOrganizations.organization.company:id,name',
                    'trsFunctionalFunctions:functional_id,function_id',
                    'trsFunctionalFunctions.functionModel:id,name,company_id',
                    'trsFunctionalFunctions.functionModel.company:id,name'
                ])
                ->orderBy('id', 'asc')
                ->get()
                ->map(function ($f) {
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
            'functions' => Inertia::defer(fn () => MstFunction::select(['id', 'name', 'company_id'])
                ->orderBy('name')
                ->get()
                ->map(fn ($fun) => [
                    'id' => $fun->id,
                    'name' => $fun->name,
                    'company_id' => $fun->company_id,
                ])->values()->all()),
        ]);
    }

    public function storeFunctional(Request $request, FunctionalOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'name' => 'required|string|max:255',
            'regulation_id' => 'required|integer|exists:mst_regulation,id',
        ]);

        $service->create($validated);

        return redirect()
            ->route('itom.business-process.organization-structure.functional.index')
            ->with('success', 'Organisasi Fungsional berhasil ditambahkan.');
    }

    public function updateFunctional(Request $request, int $id, FunctionalOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'name' => 'required|string|max:255',
            'regulation_id' => 'required|integer|exists:mst_regulation,id',
        ]);

        $service->update($id, $validated);

        return redirect()
            ->route('itom.business-process.organization-structure.functional.index')
            ->with('success', 'Organisasi Fungsional berhasil diperbarui.');
    }

    public function destroyFunctional(int $id, FunctionalOrganizationService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()
            ->route('itom.business-process.organization-structure.functional.index')
            ->with('success', 'Organisasi Fungsional berhasil dihapus.');
    }

    public function storeFunctionalMember(Request $request, FunctionalOrganizationService $service): RedirectResponse
    {
        $memberType = $request->input('member_type', 'bod');

        if ($memberType === 'function') {
            $validated = $request->validate([
                'member_type' => 'required|string|in:bod,function',
                'functional_id' => 'required|integer|exists:mst_functional_organization,id',
                'organization_id' => 'required|integer|exists:mst_function,id',
            ]);

            $service->addMember($validated);

            return redirect()
                ->route('itom.business-process.organization-structure.functional.index')
                ->with('success', 'Anggota Fungsi berhasil ditambahkan.');
        } else {
            $validated = $request->validate([
                'member_type' => 'required|string|in:bod,function',
                'structure_id' => 'required|integer|exists:trs_functional_structure,id',
                'organization_id' => 'required|integer|exists:mst_bod,id',
            ]);

            $service->addMember($validated);

            return redirect()
                ->route('itom.business-process.organization-structure.functional.index')
                ->with('success', 'Anggota BOD berhasil ditambahkan.');
        }
    }

    public function destroyFunctionalMember(Request $request, FunctionalOrganizationService $service): RedirectResponse
    {
        $memberType = $request->input('member_type', 'bod');

        if ($memberType === 'function') {
            $validated = $request->validate([
                'member_type' => 'required|string|in:bod,function',
                'functional_id' => 'required|integer|exists:mst_functional_organization,id',
                'organization_id' => 'required|integer',
            ]);

            $service->removeMember($validated);

            return redirect()
                ->route('itom.business-process.organization-structure.functional.index')
                ->with('success', 'Anggota Fungsi berhasil dihapus.');
        } else {
            $validated = $request->validate([
                'member_type' => 'required|string|in:bod,function',
                'structure_id' => 'required|integer|exists:trs_functional_structure,id',
                'organization_id' => 'required|integer',
            ]);

            $service->removeMember($validated);

            return redirect()
                ->route('itom.business-process.organization-structure.functional.index')
                ->with('success', 'Anggota BOD berhasil dihapus.');
        }
    }

    public function storeFunctionalStructure(Request $request, FunctionalOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'functional_org_id' => 'required|integer|exists:mst_functional_organization,id',
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|integer|exists:trs_functional_structure,id',
        ]);

        $service->addStructure($validated);

        return redirect()
            ->route('itom.business-process.organization-structure.functional.index')
            ->with('success', 'Struktur Fungsi berhasil ditambahkan.');
    }

    public function destroyFunctionalStructure(Request $request, FunctionalOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'structure_id' => 'required|integer|exists:trs_functional_structure,id',
        ]);

        $service->deleteStructure($validated['structure_id']);

        return redirect()
            ->route('itom.business-process.organization-structure.functional.index')
            ->with('success', 'Struktur Fungsi berhasil dihapus.');
    }
}
