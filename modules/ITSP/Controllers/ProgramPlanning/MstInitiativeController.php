<?php

namespace Modules\ITSP\Controllers\ProgramPlanning;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MstInitiativeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tipe_initiative' => 'required|integer|in:1,2',
            'coe_id' => 'nullable|integer|exists:mst_coe,id',
            'business_unit' => 'nullable|integer|exists:trs_organization,id',
            'status' => 'nullable|string|max:100',
        ]);

        MstInitiative::create($validated);

        return redirect()
            ->route('master-data.index')
            ->with('success', 'Master Initiative berhasil ditambahkan.');
    }

    public function update(Request $request, MstInitiative $mstInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tipe_initiative' => 'required|integer|in:1,2',
            'coe_id' => 'nullable|integer|exists:mst_coe,id',
            'business_unit' => 'nullable|integer|exists:trs_organization,id',
            'status' => 'nullable|string|max:100',
        ]);

        $mstInitiative->update($validated);

        return redirect()
            ->route('master-data.index')
            ->with('success', 'Master Initiative berhasil diperbarui.');
    }

    public function destroy(MstInitiative $mstInitiative): RedirectResponse
    {
        $mstInitiative->delete();

        return redirect()
            ->route('master-data.index')
            ->with('success', 'Master Initiative berhasil dihapus.');
    }
}
