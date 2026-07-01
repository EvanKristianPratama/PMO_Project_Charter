<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\RjppTagging;
use Modules\ITSP\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateController extends Controller
{
    public function __invoke(Request $request, TrsScInitiative $scInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'initiative_ids' => 'nullable|array',
            'initiative_ids.*' => 'integer|exists:mst_initiative,id',
            'owner' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'usecase' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_id' => 'nullable|integer|exists:mst_sc_source,id',
            'value' => 'nullable|integer|in:1,2,3',
            'urgency' => 'nullable|integer|in:1,2,3',
            'rjpp_tagging_ids' => 'nullable|array',
            'rjpp_tagging_ids.*' => 'integer|exists:trs_themes,id',
            'status' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $scInitiative): void {
            $initiativeIds = $this->uniqueIntCollection($validated['initiative_ids']);
            $themeIds = $this->uniqueIntCollection($validated['rjpp_tagging_ids'] ?? []);
            $rjppScKey = Schema::hasColumn('trs_rjpp', 'sc_id') ? 'sc_id' : 'digital_id';

            $scInitiative->update([
                'owner' => $validated['owner'],
                'coe' => $validated['coe'],
                'usecase' => $validated['usecase'],
                'description' => $validated['description'],
                'source_id' => $validated['source_id'],
                'value' => $validated['value'],
                'urgency' => $validated['urgency'],
                'status' => $validated['status'],
            ]);

            $scInitiative->mstInitiatives()->sync($initiativeIds->all());

            RjppTagging::query()
                ->where($rjppScKey, $scInitiative->id)
                ->delete();

            foreach ($themeIds as $themeId) {
                RjppTagging::create([
                    $rjppScKey => $scInitiative->id,
                    'theme_id' => $themeId,
                ]);
            }
        });

        return redirect()
            ->route('itsp.program-planning.program-definition.digital-initiatives.compendium.index')
            ->with('success', 'Compendium berhasil diperbarui.');
    }

    private function uniqueIntCollection(array $items): Collection
    {
        return collect($items)
            ->map(fn ($item) => (int) $item)
            ->filter(fn (int $item) => $item > 0)
            ->unique()
            ->values();
    }
}
