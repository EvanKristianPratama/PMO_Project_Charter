<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstActor;
use App\Models\MstRegulation;
use App\Models\MstSop;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProcedureController extends Controller
{
    /**
     * Display a listing of procedures.
     */
    public function index(): Response
    {
        $actors = MstActor::with('organization')->get();
        $sop = MstSop::with('regulation.organization')->get();
        $regulations = MstRegulation::with('organization')->orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        return Inertia::render('Procedure/Index', [
            'actors' => $actors,
            'sop' => $sop,
            'regulations' => $regulations,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created actor.
     */
    public function storeActor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:trs_organization,id',
        ]);

        MstActor::create($validated);

        return back()->with('success', 'Aktor berhasil ditambahkan.');
    }

    /**
     * Update the specified actor.
     */
    public function updateActor(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:trs_organization,id',
        ]);

        $actor = MstActor::findOrFail($id);
        $actor->update($validated);

        return back()->with('success', 'Aktor berhasil diperbarui.');
    }

    /**
     * Remove the specified actor.
     */
    public function destroyActor(int $id): RedirectResponse
    {
        $actor = MstActor::findOrFail($id);
        $actor->delete();

        return back()->with('success', 'Aktor berhasil dihapus.');
    }
}
