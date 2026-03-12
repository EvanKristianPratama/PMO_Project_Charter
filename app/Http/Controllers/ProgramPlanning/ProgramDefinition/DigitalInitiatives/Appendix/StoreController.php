<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\RjppTagging;
use App\Models\TrsMapSc;
use App\Models\TrsScDependency;
use App\Models\TrsScDetails;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * The ID of the newly created TrsScInitiative, stored as a class property
     * so it can be reused across the creation steps.
     */
    protected int $appendixInitiativeId;

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // --- Step 1: TrsScInitiative ---
            'compendium_id' => 'required|integer|exists:trs_sc_initiative,id',
            'owner'         => 'nullable|string|max:255',
            'coe'           => 'nullable|string|max:255',
            'usecase'       => 'required|string|max:255',
            'description'   => 'nullable|string',
            'source_id'     => 'required|integer|exists:mst_sc_source,id',
            'value'         => 'nullable|integer|in:1,2,3',
            'urgency'       => 'nullable|integer|in:1,2,3',
            'status'        => 'nullable|integer',
            'initiative_ids'     => 'nullable|array',
            'initiative_ids.*'   => 'integer',
            'rjpp_tagging_ids'   => 'nullable|array',
            'rjpp_tagging_ids.*' => 'integer',

            // --- Step 3: TrsScDetails ---
            'organization'       => 'nullable|string|max:255',
            'situation'          => 'nullable|string',
            'key_functionalities'=> 'nullable|string',
            'value_rationale'    => 'nullable|string',
            'value_matrics'      => 'nullable|string',
            'urgency_rationale'  => 'nullable|string',
            'urgency_expected'   => 'nullable|string',
            'ease'               => 'nullable|integer|in:1,2,3',
            'ease_rationale'     => 'nullable|string',
            'ease_detail'        => 'nullable|string',
            'resource'           => 'nullable|integer|in:1,2,3',
            'resource_rationale' => 'nullable|string',
            'resource_retionale' => 'nullable|string',
            'predecessor'        => 'nullable|string',
            'successor'          => 'nullable|string',
            'otherBU'            => 'nullable|string',
            'sign_by'            => 'nullable|array',
            'sign_by.*'          => 'string|max:255',
        ]);

        DB::transaction(function () use ($validated): void {
            // =========================================================
            // STEP 1: Create TrsScInitiative (the new appendix initiative)
            // =========================================================
            $scInitiative = TrsScInitiative::create([
                'owner'       => $validated['owner'] ?? null,
                'coe'         => $validated['coe'] ?? null,
                'usecase'     => $validated['usecase'],
                'description' => $validated['description'] ?? null,
                'source_id'   => $validated['source_id'],
                'value'       => $validated['value'] ?? null,
                'urgency'     => $validated['urgency'] ?? null,
                'status'      => $validated['status'] ?? 1,
            ]);

            // Store the new initiative ID in the class property for reuse
            $this->appendixInitiativeId = $scInitiative->id;

            // Sync initiative mappings (trs_map_sc)
            if (!empty($validated['initiative_ids'])) {
                foreach (array_filter($validated['initiative_ids']) as $initId) {
                    TrsMapSc::create([
                        'sc_id'         => $this->appendixInitiativeId,
                        'initiative_id' => $initId,
                    ]);
                }
            }

            // Sync RJPP taggings (trs_rjpp: sc_id + theme_id)
            if (!empty($validated['rjpp_tagging_ids'])) {
                foreach (array_filter($validated['rjpp_tagging_ids']) as $themeId) {
                    RjppTagging::create([
                        'sc_id'    => $this->appendixInitiativeId,
                        'theme_id' => $themeId,
                    ]);
                }
            }

            // =========================================================
            // STEP 2: Create TrsScDependency mapping
            //         (links the selected compendium → new appendix initiative)
            // =========================================================
            TrsScDependency::create([
                'compendium_id' => $validated['compendium_id'],
                'appendix_id'   => $this->appendixInitiativeId,
            ]);

            // =========================================================
            // STEP 3: Create TrsScDetails for the new appendix initiative
            // =========================================================
            $n = fn($v) => ($v === '' || $v === null) ? null : $v;

            TrsScDetails::create([
                'sc_id'              => $this->appendixInitiativeId,
                'organization'       => $n($validated['organization'] ?? null),
                'situation'          => $n($validated['situation'] ?? null),
                'key_functionalities'=> $n($validated['key_functionalities'] ?? null),
                'value_rationale'    => $n($validated['value_rationale'] ?? null),
                'value_matrics'      => $n($validated['value_matrics'] ?? null),
                'urgency_rationale'  => $n($validated['urgency_rationale'] ?? null),
                'urgency_expected'   => $n($validated['urgency_expected'] ?? null),
                'ease'               => $validated['ease'] ?? null,
                'ease_rationale'     => $n($validated['ease_rationale'] ?? null),
                'ease_detail'        => $n($validated['ease_detail'] ?? null),
                'resource'           => $validated['resource'] ?? null,
                'resource_rationale' => $n($validated['resource_rationale'] ?? null),
                'resource_detail'    => $n($validated['resource_retionale'] ?? null),
                'predecessor'        => $n($validated['predecessor'] ?? null),
                'successor'          => $n($validated['successor'] ?? null),
                'otherBU'            => $n($validated['otherBU'] ?? null),
                'sign_by'            => !empty($validated['sign_by']) ? json_encode($validated['sign_by']) : null,
            ]);
        });

        return back()->with('success', 'Appendix berhasil ditambahkan.');
    }
}
