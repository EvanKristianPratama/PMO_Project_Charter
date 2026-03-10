<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\RjppTagging;
use App\Models\TrsMapSc;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'initiative_ids' => 'nullable|array',
            'initiative_ids.*' => 'integer|exists:mst_initiative,id',
            'owner' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'usecase' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_id' => 'required|integer|exists:mst_sc_source,id',
            'value' => 'nullable|integer|in:1,2,3',
            'urgency' => 'nullable|integer|in:1,2,3',
            'rjpp_tagging_ids' => 'nullable|array',
            'rjpp_tagging_ids.*' => 'integer|exists:trs_themes,id',
            'status' => 'required|integer|in:1,2,3,4,5',
        ]);

        DB::transaction(function () use ($validated): void {
            $initiativeIds = $this->uniqueIntCollection($validated['initiative_ids']);
            $themeIds = $this->uniqueIntCollection($validated['rjpp_tagging_ids'] ?? []);
            $sourceId = (int) $validated['source_id'];

            $scInitiative = TrsScInitiative::create([
                'owner' => $validated['owner'] ?? null,
                'coe' => $validated['coe'] ?? null,
                'usecase' => $validated['usecase'],
                'description' => $validated['description'] ?? null,
                'source_id' => $sourceId,
                'value' => $validated['value'],
                'urgency' => $validated['urgency'],
                'status' => $validated['status'],
            ]);

            $hasPivotTimestamps =
                Schema::hasColumn('trs_map_sc', 'created_at')
                && Schema::hasColumn('trs_map_sc', 'updated_at');

            $rows = $initiativeIds
                ->map(function (int $initiativeId) use ($scInitiative, $hasPivotTimestamps): array {
                    $row = [
                        'sc_id' => $scInitiative->id,
                        'initiative_id' => $initiativeId,
                    ];

                    if ($hasPivotTimestamps) {
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                    }

                    return $row;
                })
                ->all();

            if (!empty($rows)) {
                TrsMapSc::insert($rows);
            }

            if ($themeIds->isNotEmpty()) {
                $digitalKey = Schema::hasColumn('trs_rjpp', 'sc_id') ? 'sc_id' : 'digital_id';

                foreach ($themeIds as $themeId) {
                    RjppTagging::create([
                        $digitalKey => $scInitiative->id,
                        'theme_id' => $themeId,
                    ]);
                }
            }
        });

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives.compendium.index')
            ->with('success', 'Compendium berhasil ditambahkan.');
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


