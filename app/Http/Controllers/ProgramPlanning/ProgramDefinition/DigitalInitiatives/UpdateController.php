<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives;

use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use App\Models\ScStatusImplementation;
use App\Models\UcStatusImplementation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateController extends Controller
{
    public function __invoke(Request $request, DigitalInitiative $digitalInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'sc_status' => 'nullable|string|max:255',
            'sc_review_status' => ['nullable', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
        ]);

        $oldStatus = $digitalInitiative->status;
        $digitalInitiative->update($validated);

        if ((string) $digitalInitiative->status !== (string) $oldStatus) {
            $statusModel = InitiativeStatus::find($digitalInitiative->status);
            $statusName = $statusModel ? $statusModel->name : (string) $digitalInitiative->status;

            UcStatusImplementation::create([
                'digital_initiative_id' => $digitalInitiative->id,
                'status' => $statusName,
                'date' => now()->toDateString(),
                'time_start' => now()->toTimeString(),
            ]);
        }

        if ($request->filled('sc_status') || $request->filled('sc_review_status')) {
            ScStatusImplementation::updateOrCreate(
                [
                    'digital_initiative_id' => $digitalInitiative->id,
                    'date' => now()->toDateString(),
                ],
                [
                    'status' => $validated['sc_status'],
                    'review_status' => $validated['sc_review_status'],
                    'time_start' => now()->toTimeString(),
                ]
            );
        }

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives')
            ->with('success', 'Digital Initiative updated successfully.');
    }
}
