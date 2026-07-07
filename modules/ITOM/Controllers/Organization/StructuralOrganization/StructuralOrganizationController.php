<?php

namespace Modules\ITOM\Controllers\Organization\StructuralOrganization;

use App\Http\Controllers\Controller;
use App\Models\MstCompany;
use App\Models\Groub;
use App\Models\MstBod;
use App\Models\MstRegulation;
use App\Services\Organization\StructuralOrganization\StructuralOrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StructuralOrganizationController extends Controller
{
    public function index(StructuralOrganizationService $service): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/StrukturalOrganization/Index', [
            'organizationStructureRows' => Inertia::defer(fn () => $service->getOrganizationStructureRows()),
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
            'groubOptions' => Inertia::defer(fn () => Groub::with('company')->orderBy('name')->get()->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->company ? "{$g->company->name} - {$g->name}" : $g->name,
                'company_id' => $g->company_id,
                'group_name' => $g->name,
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
        ]);
    }

    public function store(Request $request, StructuralOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'groub_id' => 'required|integer|exists:trs_groub,id',
            'parent_id' => 'nullable|integer|exists:trs_organization,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
        ]);

        $service->create($validated);

        return redirect()
            ->route('itom.business-process.organization-structure')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function update(Request $request, int $id, StructuralOrganizationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'groub_id' => 'required|integer|exists:trs_groub,id',
            'parent_id' => 'nullable|integer|exists:trs_organization,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
        ]);

        $service->update($id, $validated);

        return redirect()
            ->route('itom.business-process.organization-structure')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(int $id, StructuralOrganizationService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()
            ->route('itom.business-process.organization-structure')
            ->with('success', 'Organisasi berhasil dihapus.');
    }
}
