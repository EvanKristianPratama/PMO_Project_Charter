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

class OrganizationController extends Controller
{
    public function index(OrganizationStructureService $organizationStructureService): Response
    {
        return Inertia::render('Architecture/OrganizationStructure/Index', [
            'organizationStructureRows' => $organizationStructureService->getOrganizationStructureRows(),
            'groubOptions' => Groub::with('company')->orderBy('name')->get()->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->company ? "{$g->company->name} - {$g->name}" : $g->name,
            ])->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'groub_id' => 'required|integer|exists:trs_groub,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
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
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
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
}
