<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use App\Models\MstInitiative;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // First if sc_id is provided, we use it. Otherwise, we validate and create TrsScInitiative
        $isNewInitiative = empty($request->input('sc_id'));

        $initiativeRules = [];
        if ($isNewInitiative) {
            $initiativeRules = [
                'owner' => 'nullable|string|max:255',
                'coe' => 'nullable|string|max:255',
                'usecase' => 'required|string|max:255',
                'description' => 'nullable|string',
                'source_id' => 'required|integer|exists:mst_sc_source,id',
                'value' => 'nullable|integer|in:1,2,3',
                'urgency' => 'nullable|integer|in:1,2,3',
            ];
        }

        $validated = $request->validate(array_merge([
            'sc_id' => 'nullable|integer|exists:trs_sc_initiative,id',
            'current_situation' => 'nullable|string',
            'key_functionalities' => 'nullable|string',
            'value_rationale' => 'nullable|string',
            'value_matrics' => 'nullable|string',
            'value_detail' => 'nullable|string',
            'urgency_detail' => 'nullable|string',
            'ease_implementation' => 'nullable|integer|in:1,2,3',
            'ease_detail' => 'nullable|string',
            'resource_requirement' => 'nullable|integer|in:1,2,3',
            'resource_detail' => 'nullable|string',
            'interpendencies' => 'nullable|string',
            'sign_by' => 'nullable|string|max:255',
        ], $initiativeRules));

        DB::transaction(function () use ($validated, $isNewInitiative, $request): void {
            $scId = $validated['sc_id'] ?? null;

            if ($isNewInitiative) {
                // Create the ScInitiative directly using Eloquent
                $scInitiative = \App\Models\TrsScInitiative::create([
                    'owner' => $validated['owner'] ?? null,
                    'coe' => $validated['coe'] ?? null,
                    'usecase' => $validated['usecase'],
                    'description' => $validated['description'] ?? null,
                    'source_id' => $validated['source_id'],
                    'value' => $validated['value'] ?? null,
                    'urgency' => $validated['urgency'] ?? null,
                    'status' => 1, // Default Drafting
                ]);

                // Sync Initiative Mappings
                if ($request->has('initiative_ids')) {
                    $initiativeIds = collect($request->input('initiative_ids'))->filter()->toArray();
                    foreach ($initiativeIds as $initId) {
                        \App\Models\TrsMapSc::create([
                            'sc_id' => $scInitiative->id,
                            'initiative_id' => $initId,
                        ]);
                    }
                }

                // Sync RJPP Taggings
                if ($request->has('rjpp_tagging_ids')) {
                    $rjppIds = collect($request->input('rjpp_tagging_ids'))->filter()->toArray();
                    $scInitiative->rjppTaggings()->sync($rjppIds);
                }

                $scId = $scInitiative->id;
            }

            if ($scId) {
                \App\Models\TrsScDetails::create([
                    'sc_id' => $scId,
                    'current_situation' => $validated['current_situation'] ?? null,
                    'key_functionalities' => $validated['key_functionalities'] ?? null,
                    'value_rationale' => $validated['value_rationale'] ?? null,
                    'value_matrics' => $validated['value_matrics'] ?? null,
                    'value_detail' => $validated['value_detail'] ?? null,
                    'urgency_detail' => $validated['urgency_detail'] ?? null,
                    'ease_implementation' => $validated['ease_implementation'] ?? null,
                    'ease_detail' => $validated['ease_detail'] ?? null,
                    'resource_requirement' => $validated['resource_requirement'] ?? null,
                    'resource_detail' => $validated['resource_detail'] ?? null,
                    'interpendencies' => $validated['interpendencies'] ?? null,
                    'sign_by' => $validated['sign_by'] ?? null,
                ]);
            }
        });

        // Always return back to stay on the same page (modal)
        return back()->with('success', 'Appendix berhasil ditambahkan.');
    }
}
