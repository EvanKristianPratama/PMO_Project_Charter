<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function __invoke(Request $request, TrsScInitiative $scInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'initiative_ids' => 'required|array',
            'initiative_ids.*' => 'integer|exists:mst_initiative,id',
            'owner' => 'nullable|string|max:255',
            'usecase' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_id' => 'nullable|integer|exists:mst_sc_source,id',
            'value' => 'required|integer|in:1,2,3,4',
            'urgency' => 'required|integer|in:1,2,3,4',
            'status' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $scInitiative): void {
            $scInitiative->update([
                'owner' => $validated['owner'],
                'usecase' => $validated['usecase'],
                'description' => $validated['description'],
                'source_id' => $validated['source_id'],
                'value' => $validated['value'],
                'urgency' => $validated['urgency'],
                'status' => $validated['status'],
            ]);

            $scInitiative->mstInitiatives()->sync($validated['initiative_ids']);

            // Update source for all linked initiatives based on the selected source_id
            if ($validated['source_id']) {
                MstInitiative::whereIn('id', $validated['initiative_ids'])->update(['source' => $validated['source_id']]);
            }
        });

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives.compendium.index')
            ->with('success', 'Compendium berhasil diperbarui.');
    }
}


