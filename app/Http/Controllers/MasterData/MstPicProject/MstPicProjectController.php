<?php

namespace App\Http\Controllers\MasterData\MstPicProject;

use App\Http\Controllers\Controller;
use App\Models\MstPicProject;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MstPicProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $picProjects = MstPicProject::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name', 'asc')->get();

        return Inertia::render('MasterData/MstPicProject/Index', [
            'picProjects' => $picProjects,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'name' => 'required|string|max:255',
        ]);

        MstPicProject::create($validated);

        return redirect()->back()->with('success', 'PIC Project berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MstPicProject $picProject): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'name' => 'required|string|max:255',
        ]);

        $picProject->update($validated);

        return redirect()->back()->with('success', 'PIC Project berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MstPicProject $picProject): RedirectResponse
    {
        $picProject->delete();

        return redirect()->back()->with('success', 'PIC Project berhasil dihapus.');
    }
}
