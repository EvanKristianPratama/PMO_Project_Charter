<?php

namespace App\Http\Controllers\ResourceManagement;

use App\Http\Controllers\Controller;
use App\Models\MstResource;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $resources = MstResource::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name', 'asc')->get();

        return Inertia::render('ResourceManagement/Index', [
            'resources' => $resources,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'jabatan' => 'required|integer|exists:trs_organization,id',
            'internal_id' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
        ]);

        MstResource::create($validated);

        return redirect()->back()->with('success', 'Resource berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $resource = MstResource::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'jabatan' => 'required|integer|exists:trs_organization,id',
            'internal_id' => 'nullable|string|max:255',
            'sk' => 'nullable|string|max:255',
        ]);

        $resource->update($validated);

        return redirect()->back()->with('success', 'Resource berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $resource = MstResource::findOrFail($id);
        $resource->delete();

        return redirect()->back()->with('success', 'Resource berhasil dihapus.');
    }
}
