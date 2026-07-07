<?php

namespace Modules\ITOM\Controllers\Organization\BOD;

use App\Http\Controllers\Controller;
use App\Models\MstCompany;
use App\Models\MstBod;
use App\Services\Organization\BOD\BodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BodController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/BOD/Index', [
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
        ]);
    }

    public function storeBod(Request $request, BodService $service): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|integer|exists:mst_bod,id',
            'name' => 'required|string|max:255',
            'nama_jabatan' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'grup_function' => 'nullable|string|max:255',
            'role_function' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'regulation_id' => 'nullable|integer|exists:mst_regulation,id',
            'order' => 'nullable|integer',
        ]);

        $service->create($validated);

        return redirect()
            ->route('itom.business-process.organization-structure.bod.index')
            ->with('success', 'Anggota BOD berhasil ditambahkan.');
    }

    public function updateBod(Request $request, int $id, BodService $service): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|integer|exists:mst_bod,id|different:id',
            'name' => 'required|string|max:255',
            'nama_jabatan' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'pejabat' => 'nullable|string|max:255',
            'grup_function' => 'nullable|string|max:255',
            'role_function' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'regulation_id' => 'nullable|integer|exists:mst_regulation,id',
            'order' => 'nullable|integer',
        ]);

        $service->update($id, $validated);

        return redirect()
            ->route('itom.business-process.organization-structure.bod.index')
            ->with('success', 'Anggota BOD berhasil diperbarui.');
    }

    public function destroyBod(int $id, BodService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()
            ->route('itom.business-process.organization-structure.bod.index')
            ->with('success', 'Anggota BOD berhasil dihapus.');
    }
}
