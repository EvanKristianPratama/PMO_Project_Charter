<?php
namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\RjppTagging;
use Modules\ITSP\Models\TrsScDetails;
use Modules\ITSP\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function __invoke(Request $request, TrsScInitiative $scInitiative): RedirectResponse
    {
        $validated = $request->validate([
            // TrsScInitiative Fields
            'owner' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'usecase' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|integer|in:1,2,3',
            'urgency' => 'nullable|integer|in:1,2,3',
            'status' => 'nullable|integer',
            'initiative_ids' => 'nullable|array',
            'initiative_ids.*' => 'integer|exists:mst_initiative,id',
            'compendium_ids' => 'nullable|array',
            'compendium_ids.*' => 'integer|exists:trs_sc_initiative,id',
            'rjpp_tagging_ids' => 'nullable|array',
            'rjpp_tagging_ids.*' => 'integer',

            // TrsScDetails Fields
            'organization' => 'nullable|string|max:255',
            'update_doc' => 'nullable|date',
            'situation' => 'nullable|string',
            'key_functionalities' => 'nullable|string',
            'value_rationale' => 'nullable|string',
            'value_matrics' => 'nullable|string',
            'urgency_rationale' => 'nullable|string',
            'urgency_expected' => 'nullable|string',
            'ease' => 'nullable|integer|in:1,2,3',
            'ease_rationale' => 'nullable|string',
            'ease_detail' => 'nullable|string',
            'resource' => 'nullable|integer|in:1,2,3',
            'resource_rationale' => 'nullable|string',
            'resource_detail' => 'nullable|string',
            'predecessor' => 'nullable|string',
            'successor' => 'nullable|string',
            'otherBU' => 'nullable|string',
            'sign_by' => 'nullable|array',
            'sign_by.*' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $scInitiative): void {
            $initiativeIds = $this->uniqueIntCollection($validated['initiative_ids'] ?? []);
            $compendiumIds = $this->uniqueIntCollection($validated['compendium_ids'] ?? []);

            // 1. Update TrsScInitiative
            $scInitiative->update([
                'owner' => $validated['owner'] ?? null,
                'coe' => $validated['coe'] ?? null,
                'usecase' => $validated['usecase'],
                'description' => $validated['description'] ?? null,
                'value' => $validated['value'] ?? null,
                'urgency' => $validated['urgency'] ?? null,
                'status' => $validated['status'] ?? $scInitiative->status,
            ]);

            // 2. Sync Relationships (Many-to-Many)
            $scInitiative->mstInitiatives()->sync($initiativeIds->all());
            $scInitiative->compendiums()->sync($compendiumIds->all());
            $scInitiative->rjpps()->sync(array_filter($validated['rjpp_tagging_ids'] ?? []));

            // 3. Update TrsScDetails via Relationship
            $nullIfEmpty = fn ($value) => ($value === '' || $value === null) ? null : $value;

            // Prepare sign_by array
            $signBy = array_filter(array_map('trim', $validated['sign_by'] ?? []), fn ($v) => $v !== '');

            $scInitiative->scDetails()->updateOrCreate(
                ['sc_id' => $scInitiative->id],
                [
                    'organization' => $nullIfEmpty($validated['organization'] ?? null),
                    'update_doc' => $nullIfEmpty($validated['update_doc'] ?? null),
                    'situation' => $nullIfEmpty($validated['situation'] ?? null),
                    'key_functionalities' => $nullIfEmpty($validated['key_functionalities'] ?? null),
                    'value_rationale' => $nullIfEmpty($validated['value_rationale'] ?? null),
                    'value_matrics' => $nullIfEmpty($validated['value_matrics'] ?? null),
                    'urgency_rationale' => $nullIfEmpty($validated['urgency_rationale'] ?? null),
                    'urgency_expected' => $nullIfEmpty($validated['urgency_expected'] ?? null),
                    'ease' => $validated['ease'] ?? null,
                    'ease_rationale' => $nullIfEmpty($validated['ease_rationale'] ?? null),
                    'ease_detail' => $nullIfEmpty($validated['ease_detail'] ?? null),
                    'resource' => $validated['resource'] ?? null,
                    'resource_rationale' => $nullIfEmpty($validated['resource_rationale'] ?? null),
                    'resource_detail' => $nullIfEmpty($validated['resource_detail'] ?? null),
                    'predecessor' => $nullIfEmpty($validated['predecessor'] ?? null),
                    'successor' => $nullIfEmpty($validated['successor'] ?? null),
                    'otherBU' => $nullIfEmpty($validated['otherBU'] ?? null),
                    'sign_by' => ! empty($signBy) ? json_encode(array_values($signBy)) : null,
                ]
            );
        });

        return back()->with('success', 'Appendix berhasil diperbarui.');
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
