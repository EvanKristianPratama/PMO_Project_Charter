<?php

namespace Modules\ITOM\Controllers\Organization\SDM;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstResource;
use App\Models\TrsOrganization;
use Modules\ITOM\Services\Organization\SDM\SdmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SdmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/SDM/Index', [
            'resources' => Inertia::defer(fn () => MstResource::select([
                'id',
                'name',
                'jabatan',
                'internal_id',
                'sk',
                'start',
                'end',
            ])
            ->with([
                'organization:id,name,jabatan'
            ])
            ->orderBy('id', 'desc')
            ->get()),
            'organizations' => Inertia::defer(fn () => TrsOrganization::select([
                'id',
                'parent_id',
                'code',
                'jabatan',
                'name',
            ])
            ->orderBy('name', 'asc')
            ->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, SdmService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'jabatan' => 'required|integer|exists:trs_organization,id',
            'internal_id' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $service->create($validated);

        return redirect()->back()->with('success', 'Resource berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id, SdmService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'jabatan' => 'required|integer|exists:trs_organization,id',
            'internal_id' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $service->update($id, $validated);

        return redirect()->back()->with('success', 'Resource berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, SdmService $service): RedirectResponse
    {
        $service->delete($id);

        return redirect()->back()->with('success', 'Resource berhasil dihapus.');
    }
}
