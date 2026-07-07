<?php

namespace Modules\ITOM\Controllers\Organization\Company;

use App\Http\Controllers\Controller;
use App\Models\MstCompany;
use App\Models\Groub;
use App\Models\TrsOrganization;
use App\Services\Organization\Company\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/Company/Index', [
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
            'groubOptions' => Inertia::defer(fn () => Groub::select(['id', 'company_id', 'name'])
                ->with('company:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'name' => $g->company ? "{$g->company->name} - {$g->name}" : $g->name,
                    'company_id' => $g->company_id,
                    'group_name' => $g->name,
                ])->values()->all()),
        ]);
    }

    public function storeCompany(Request $request, CompanyService $service): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:mst_company,id',
            'name' => 'required|string|max:255|unique:mst_company,name',
            'organization' => 'nullable|string|max:255',
            'singkatan' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'level' => 'nullable|integer',
        ]);

        $service->create($validated);

        return redirect()
            ->route('itom.business-process.organization-structure.company.index')
            ->with('success', 'Company berhasil ditambahkan.');
    }

    public function updateCompany(Request $request, int $id, CompanyService $service): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:mst_company,id|different:id',
            'name' => 'required|string|max:255|unique:mst_company,name,' . $id,
            'organization' => 'nullable|string|max:255',
            'singkatan' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'level' => 'nullable|integer',
        ]);

        $service->update($id, $validated);

        return redirect()
            ->route('itom.business-process.organization-structure.company.index')
            ->with('success', 'Company berhasil diperbarui.');
    }

    public function destroyCompany(int $id, CompanyService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()
            ->route('itom.business-process.organization-structure.company.index')
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
            ->route('itom.business-process.organization-structure.company.index')
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
            ->route('itom.business-process.organization-structure.company.index')
            ->with('success', 'Grup berhasil diperbarui.');
    }

    public function destroyGroup(int $id): RedirectResponse
    {
        $group = Groub::findOrFail($id);

        // Delete all organizations inside the group
        TrsOrganization::where('groub_id', $id)->delete();

        $group->delete();

        return redirect()
            ->route('itom.business-process.organization-structure.company.index')
            ->with('success', 'Grup dan seluruh struktur organisasi di dalamnya berhasil dihapus.');
    }
}
