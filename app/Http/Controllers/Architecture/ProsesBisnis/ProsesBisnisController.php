<?php

namespace App\Http\Controllers\Architecture\ProsesBisnis;

use App\Http\Controllers\Controller;
use App\Models\TrsOrganization;
use App\Models\TrsProsesBisnis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProsesBisnisController extends Controller
{
    /**
     * Display a listing of business processes.
     */
    public function index(): Response
    {
        $prosesBisnis = TrsProsesBisnis::with('organization')
            ->orderBy('organization_id')
            ->orderBy('no')
            ->get();

        $organizations = TrsOrganization::orderBy('name')->get();

        return Inertia::render('Architecture/ProsesBisnis/Index', [
            'prosesBisnis' => $prosesBisnis,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Display a listing of business processes for CRUD management.
     */
    public function manage(): Response
    {
        $prosesBisnis = TrsProsesBisnis::with('organization')
            ->orderBy('organization_id')
            ->orderBy('no')
            ->get();

        $organizations = TrsOrganization::orderBy('name')->get();

        return Inertia::render('Architecture/ProsesBisnis/Manage', [
            'prosesBisnis' => $prosesBisnis,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created business process.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'no' => 'required|string',
            'proses_bisnis' => 'required|string',
            'tugas' => 'required|string',
            'hasil' => 'required|string',
            'status' => 'nullable|string',
        ]);

        TrsProsesBisnis::create($validated);

        return redirect()
            ->route('architecture.proses-bisnis.manage')
            ->with('success', 'Proses Bisnis berhasil ditambahkan.');
    }

    /**
     * Update the specified business process.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $prosesBisnis = TrsProsesBisnis::findOrFail($id);

        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'no' => 'required|string',
            'proses_bisnis' => 'required|string',
            'tugas' => 'required|string',
            'hasil' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $prosesBisnis->update($validated);

        return redirect()
            ->route('architecture.proses-bisnis.manage')
            ->with('success', 'Proses Bisnis berhasil diperbarui.');
    }

    /**
     * Remove the specified business process.
     */
    public function destroy(int $id): RedirectResponse
    {
        $prosesBisnis = TrsProsesBisnis::findOrFail($id);
        $prosesBisnis->delete();

        return redirect()
            ->route('architecture.proses-bisnis.manage')
            ->with('success', 'Proses Bisnis berhasil dihapus.');
    }
}
