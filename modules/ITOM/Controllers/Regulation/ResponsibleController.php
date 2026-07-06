<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstResponsible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResponsibleController extends Controller
{
    /**
     * Display the master responsible CRUD management view.
     */
    public function manage(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/RaciAnalysis/Responsible', [
            'responsibles' => Inertia::defer(fn() => MstResponsible::select(['id', 'responsible'])->orderBy('id', 'asc')->get()),
        ]);
    }

    /**
     * Store a newly created responsible.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'responsible' => 'required|string',
        ], [
            'responsible.required' => 'Isi Tanggung Jawab (Responsible) wajib diisi.',
        ]);

        MstResponsible::create($validated);

        return redirect()
            ->route('itom.policy.responsible.manage')
            ->with('success', 'Master Responsible berhasil ditambahkan.');
    }

    /**
     * Update the specified responsible.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $responsible = MstResponsible::findOrFail($id);

        $validated = $request->validate([
            'responsible' => 'required|string',
        ], [
            'responsible.required' => 'Isi Tanggung Jawab (Responsible) wajib diisi.',
        ]);

        $responsible->update($validated);

        return redirect()
            ->route('itom.policy.responsible.manage')
            ->with('success', 'Master Responsible berhasil diperbarui.');
    }

    /**
     * Remove the specified responsible.
     */
    public function destroy(int $id): RedirectResponse
    {
        $responsible = MstResponsible::findOrFail($id);
        $responsible->delete();

        return redirect()
            ->route('itom.policy.responsible.manage')
            ->with('success', 'Master Responsible berhasil dihapus.');
    }
}
