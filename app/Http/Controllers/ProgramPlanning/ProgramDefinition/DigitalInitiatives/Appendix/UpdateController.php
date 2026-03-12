<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\RjppTagging;
use App\Models\TrsScDetails;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function __invoke(Request $request, TrsScInitiative $scInitiative): RedirectResponse
    {
        $validated = $request->validate([
            // TrsScInitiative Fields
            'owner'         => 'nullable|string|max:255',
            'coe'           => 'nullable|string|max:255',
            'usecase'       => 'required|string|max:255',
            'description'   => 'nullable|string',
            'value'         => 'nullable|integer|in:1,2,3',
            'urgency'       => 'nullable|integer|in:1,2,3',
            'status'        => 'nullable|integer',
            'rjpp_tagging_ids'   => 'nullable|array',
            'rjpp_tagging_ids.*' => 'integer',

            // TrsScDetails Fields
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
            'resource_detail'    => 'nullable|string',
            'predecessor'        => 'nullable|string',
            'successor'          => 'nullable|string',
            'otherBU'            => 'nullable|string',
            'sign_by'            => 'nullable|array',
            'sign_by.*'          => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $scInitiative): void {
            // STEP 1: Update TrsScInitiative
            $scInitiative->update([
                'owner'       => $validated['owner'] ?? null,
                'coe'         => $validated['coe'] ?? null,
                'usecase'     => $validated['usecase'],
                'description' => $validated['description'] ?? null,
                'value'       => $validated['value'] ?? null,
                'urgency'     => $validated['urgency'] ?? null,
                'status'      => $validated['status'] ?? $scInitiative->status,
            ]);

            // Sync RJPP taggings
            RjppTagging::where('sc_id', $scInitiative->id)->delete();
            if (!empty($validated['rjpp_tagging_ids'])) {
                foreach (array_filter($validated['rjpp_tagging_ids']) as $themeId) {
                    RjppTagging::create([
                        'sc_id'    => $scInitiative->id,
                        'theme_id' => $themeId,
                    ]);
                }
            }

            // STEP 2: Update TrsScDetails
            $n = fn($v) => ($v === '' || $v === null) ? null : $v;

            // Prepare sign_by array
            $signBy = array_filter(array_map('trim', $validated['sign_by'] ?? []), fn($v) => $v !== '');

            TrsScDetails::updateOrCreate(
                ['sc_id' => $scInitiative->id],
                [
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
                    'resource_detail'    => $n($validated['resource_detail'] ?? null),
                    'predecessor'        => $n($validated['predecessor'] ?? null),
                    'successor'          => $n($validated['successor'] ?? null),
                    'otherBU'            => $n($validated['otherBU'] ?? null),
                    'sign_by'            => !empty($signBy) ? json_encode(array_values($signBy)) : null,
                ]
            );
        });

        return back()->with('success', 'Appendix berhasil diperbarui.');
    }
}
