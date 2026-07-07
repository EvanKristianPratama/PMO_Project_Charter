<?php

namespace Modules\ITOM\Controllers\Organization\StrukturalOrganization;

use App\Http\Controllers\Controller;
use App\Services\Organization\StrukturalOrganization\StrukturalOrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StrukturalOrganizationController extends Controller
{
    public function store(Request $request, StrukturalOrganizationService $service): RedirectResponse
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

    public function update(Request $request, int $id, StrukturalOrganizationService $service): RedirectResponse
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

    public function destroy(int $id, StrukturalOrganizationService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()
            ->route('itom.business-process.organization-structure')
            ->with('success', 'Organisasi berhasil dihapus.');
    }
}
