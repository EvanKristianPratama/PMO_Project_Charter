<?php

namespace App\Http\Controllers\Architecture\OrganizationStructure;

use App\Http\Controllers\Controller;
use App\Models\Groub;
use App\Models\TrsOrganization;
use App\Services\Architecture\OrganizationStructureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\MstCompany;
use App\Models\MstBod;
use App\Models\MstSkOrganization;
use App\Models\MstFunctionalOrganization;
use App\Models\TrsFunctionalOrganization;
use App\Models\TrsFunctionalStructure;
use App\Models\MstFunction;

class OrganizationController extends Controller
{
    public function index(OrganizationStructureService $organizationStructureService): Response
    {
        return Inertia::render('Architecture/OrganizationStructure/Index', [
            'organizationStructureRows' => $organizationStructureService->getOrganizationStructureRows(),
            'groubOptions' => Groub::with('company')->orderBy('name')->get()->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->company ? "{$g->company->name} - {$g->name}" : $g->name,
                'company_id' => $g->company_id,
                'group_name' => $g->name,
            ])->values()->all(),
            'companies' => MstCompany::with('parent')->orderBy('id', 'asc')->get()->map(fn ($c) => [
                'id' => $c->id,
                'parent_id' => $c->parent_id,
                'parent_name' => $c->parent?->name,
                'name' => $c->name,
                'organization' => $c->organization,
                'singkatan' => $c->singkatan,
                'grup' => $c->grup,
                'level' => $c->level,
            ])->values()->all(),
            'bods' => MstBod::with('company')->orderBy('id', 'asc')->get()->map(fn ($b) => [
                'id' => $b->id,
                'company_id' => $b->company_id,
                'parent_id' => $b->parent_id,
                'company_name' => $b->company?->name,
                'name' => $b->name,
                'alias' => $b->alias,
                'sumber' => $b->sumber,
                'pejabat' => $b->pejabat,
                'grup_function' => $b->grup_function,
                'tipe' => $b->tipe,
                'sk_id' => $b->sk_id,
                'order' => $b->order,
            ])->values()->all(),
            'skOrganizations' => MstSkOrganization::orderBy('id', 'asc')->get()->map(fn ($s) => [
                'id' => $s->id,
                'sk' => $s->sk,
                'deskripsi' => $s->deskripsi,
            ])->values()->all(),
            'functionalOrganizations' => MstFunctionalOrganization::with(['skOrganization', 'company', 'trsFunctionalStructures', 'trsFunctionalOrganizations.organization'])->orderBy('id', 'asc')->get()->map(fn ($f) => [
                'id' => $f->id,
                'company_id' => $f->company_id,
                'company_name' => $f->company?->name,
                'sk_id' => $f->sk_id,
                'sk_name' => $f->skOrganization?->sk,
                'name' => $f->name,
                'functions' => $f->trsFunctionalStructures->map(fn ($tfs) => [
                    'structure_id' => $tfs->id,
                    'functional_id' => $tfs->functional_id,
                    'parent_id' => $tfs->parent_id,
                    'name' => $tfs->name,
                ])->values()->all(),
                'members' => $f->trsFunctionalOrganizations->map(fn ($trs) => [
                    'structure_id' => $trs->structure_id,
                    'organization_id' => $trs->organization_id,
                    'name' => $trs->organization?->name,
                    'pejabat' => $trs->organization?->pejabat,
                ])->values()->all(),
            ])->values()->all(),
            'functions' => MstFunction::orderBy('name')->get()->map(fn ($fun) => [
                'id' => $fun->id,
                'name' => $fun->name,
                'company_id' => $fun->company_id,
            ])->values()->all(),
        ]);
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:mst_company,id',
            'name' => 'required|string|max:255|unique:mst_company,name',
            'organization' => 'nullable|string|max:255',
            'singkatan' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'level' => 'nullable|integer',
        ]);

        MstCompany::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'organization' => $validated['organization'] ?? null,
            'singkatan' => $validated['singkatan'] ?? null,
            'grup' => $validated['grup'] ?? null,
            'level' => $validated['level'] ?? null,
        ]);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Company berhasil ditambahkan.');
    }

    public function store(Request $request): RedirectResponse
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

        TrsOrganization::create($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $organization = TrsOrganization::findOrFail($id);

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

        $organization->update($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $organization = TrsOrganization::findOrFail($id);
        $organization->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi berhasil dihapus.');
    }

    public function updateCompany(Request $request, int $id): RedirectResponse
    {
        $company = MstCompany::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:mst_company,id|different:id',
            'name' => 'required|string|max:255|unique:mst_company,name,' . $id,
            'organization' => 'nullable|string|max:255',
            'singkatan' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'level' => 'nullable|integer',
        ]);

        $company->update($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Company berhasil diperbarui.');
    }

    public function destroyCompany(int $id): RedirectResponse
    {
        $company = MstCompany::findOrFail($id);

        // Delete all groups and organizations associated with this company
        $groups = Groub::where('company_id', $id)->get();
        foreach ($groups as $group) {
            TrsOrganization::where('groub_id', $group->id)->delete();
            $group->delete();
        }

        $company->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Company dan seluruh organisasi di dalamnya berhasil dihapus.');
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'name' => 'required|string|max:255',
        ]);

        Groub::create($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Grup berhasil ditambahkan.');
    }

    public function updateGroup(Request $request, int $id): RedirectResponse
    {
        $group = Groub::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Grup berhasil diperbarui.');
    }

    public function destroyGroup(int $id): RedirectResponse
    {
        $group = Groub::findOrFail($id);

        // Delete all organizations inside the group
        TrsOrganization::where('groub_id', $id)->delete();

        $group->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Grup dan seluruh struktur organisasi di dalamnya berhasil dihapus.');
    }

    public function storeBod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|integer|exists:mst_bod,id',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'grup_function' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'sk_id' => 'nullable|integer|exists:mst_sk_organization,id',
            'order' => 'nullable|integer',
        ]);

        MstBod::create($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Anggota BOD berhasil ditambahkan.');
    }

    public function updateBod(Request $request, int $id): RedirectResponse
    {
        $bod = MstBod::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|integer|exists:mst_bod,id|different:id',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'grup_function' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'sk_id' => 'nullable|integer|exists:mst_sk_organization,id',
            'order' => 'nullable|integer',
        ]);

        $bod->update($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Anggota BOD berhasil diperbarui.');
    }

    public function destroyBod(int $id): RedirectResponse
    {
        $bod = MstBod::findOrFail($id);
        $bod->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Anggota BOD berhasil dihapus.');
    }

    public function storeSk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        MstSkOrganization::create($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'SK Organisasi berhasil ditambahkan.');
    }

    public function updateSk(Request $request, int $id): RedirectResponse
    {
        $sk = MstSkOrganization::findOrFail($id);

        $validated = $request->validate([
            'sk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $sk->update($validated);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'SK Organisasi berhasil diperbarui.');
    }

    public function destroySk(int $id): RedirectResponse
    {
        $sk = MstSkOrganization::findOrFail($id);
        $sk->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'SK Organisasi berhasil dihapus.');
    }

    public function storeFunctional(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'name' => 'required|string|max:255',
            'sk_id' => 'required|integer|exists:mst_sk_organization,id',
        ]);

        MstFunctionalOrganization::create([
            'company_id' => $validated['company_id'],
            'name' => $validated['name'],
            'sk_id' => $validated['sk_id'],
        ]);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi Fungsional berhasil ditambahkan.');
    }

    public function updateFunctional(Request $request, int $id): RedirectResponse
    {
        $functional = MstFunctionalOrganization::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'name' => 'required|string|max:255',
            'sk_id' => 'required|integer|exists:mst_sk_organization,id',
        ]);

        $functional->update([
            'company_id' => $validated['company_id'],
            'name' => $validated['name'],
            'sk_id' => $validated['sk_id'],
        ]);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi Fungsional berhasil diperbarui.');
    }

    public function destroyFunctional(int $id): RedirectResponse
    {
        $functional = MstFunctionalOrganization::findOrFail($id);

        $structures = TrsFunctionalStructure::where('functional_id', $functional->id)->get();
        foreach ($structures as $structure) {
            TrsFunctionalOrganization::where('structure_id', $structure->id)->delete();
            $structure->delete();
        }

        $functional->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Organisasi Fungsional berhasil dihapus.');
    }

    public function storeFunctionalMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'structure_id' => 'required|integer|exists:trs_functional_structure,id',
            'organization_id' => 'required|integer|exists:mst_bod,id',
        ]);

        TrsFunctionalOrganization::firstOrCreate([
            'structure_id' => $validated['structure_id'],
            'organization_id' => $validated['organization_id'],
        ]);

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Anggota berhasil ditambahkan ke Organisasi Fungsional.');
    }

    public function destroyFunctionalMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'structure_id' => 'required|integer|exists:trs_functional_structure,id',
            'organization_id' => 'required|integer|exists:mst_bod,id',
        ]);

        TrsFunctionalOrganization::where('structure_id', $validated['structure_id'])
            ->where('organization_id', $validated['organization_id'])
            ->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Anggota berhasil dihapus dari Organisasi Fungsional.');
    }

    public function storeFunctionalStructure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'functional_org_id' => 'required|integer|exists:mst_functional_organization,id',
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|integer|exists:trs_functional_structure,id',
        ]);

        TrsFunctionalStructure::firstOrCreate(
            [
                'functional_id' => $validated['functional_org_id'],
                'name'          => $validated['name'],
                'parent_id'     => $validated['parent_id'] ?? null,
            ]
        );

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Struktur Fungsi berhasil ditambahkan.');
    }

    public function destroyFunctionalStructure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'structure_id' => 'required|integer|exists:trs_functional_structure,id',
        ]);

        $structure = TrsFunctionalStructure::findOrFail($validated['structure_id']);

        TrsFunctionalOrganization::where('structure_id', $structure->id)->delete();
        $structure->delete();

        return redirect()
            ->route('architecture.organization-structure')
            ->with('success', 'Struktur Fungsi berhasil dihapus.');
    }
}
