<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\TrsMasterMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MilestoneController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        TrsMasterMilestone::query()->create($this->validatedPayload($request));

        return back()->with('success', 'Master milestone berhasil ditambahkan.');
    }

    public function update(Request $request, TrsMasterMilestone $masterMilestone): RedirectResponse
    {
        $masterMilestone->update($this->validatedPayload($request, $masterMilestone));

        return back()->with('success', 'Master milestone berhasil diperbarui.');
    }

    public function destroy(TrsMasterMilestone $masterMilestone): RedirectResponse
    {
        $masterMilestone->delete();

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives.roadmap.index')
            ->with('success', 'Master milestone berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?TrsMasterMilestone $masterMilestone = null): array
    {
        $validator = Validator::make($request->all(), [
            'initiative_id' => ['required', 'integer', Rule::exists('mst_initiative', 'id')],
            'activity' => ['required', 'string'],
            'startYear' => ['required', 'integer', 'between:2000,2100'],
            'startQ' => ['required', 'string'],
            'endYear' => ['required', 'integer', 'between:2000,2100'],
            'endQ' => ['required', 'string'],
            'version' => ['nullable', 'string', 'max:100'],
        ], [
            'initiative_id.required' => 'Initiative wajib dipilih.',
            'initiative_id.exists' => 'Initiative yang dipilih tidak valid.',
            'activity.required' => 'Activity wajib diisi.',
            'startYear.required' => 'Start year wajib diisi.',
            'endYear.required' => 'End year wajib diisi.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            $startQuarter = $this->normalizeQuarter($request->input('startQ'));
            $endQuarter = $this->normalizeQuarter($request->input('endQ'));

            if ($startQuarter === null) {
                $validator->errors()->add('startQ', 'Start quarter harus Q1 sampai Q4.');
            }

            if ($endQuarter === null) {
                $validator->errors()->add('endQ', 'End quarter harus Q1 sampai Q4.');
            }

            if ($startQuarter === null || $endQuarter === null) {
                return;
            }

            $startYear = (int) $request->input('startYear');
            $endYear = (int) $request->input('endYear');

            $startOrder = ($startYear * 10) + (int) substr($startQuarter, 1);
            $endOrder = ($endYear * 10) + (int) substr($endQuarter, 1);

            if ($endOrder < $startOrder) {
                $validator->errors()->add('endQ', 'Periode akhir harus lebih besar atau sama dengan periode awal.');
            }
        });

        $validated = $validator->validate();

        $payload = [
            'initiative_id' => (int) $validated['initiative_id'],
            'activity' => trim((string) $validated['activity']),
            'startYear' => (int) $validated['startYear'],
            'startQ' => $this->normalizeQuarter($validated['startQ']),
            'endYear' => (int) $validated['endYear'],
            'endQ' => $this->normalizeQuarter($validated['endQ']),
        ];

        if (array_key_exists('version', $validated)) {
            $version = trim((string) ($validated['version'] ?? ''));
            $payload['version'] = $version !== '' ? $version : null;
        } elseif ($masterMilestone === null) {
            $payload['version'] = null;
        }

        return $payload;
    }

    private function normalizeQuarter(mixed $value): ?string
    {
        $raw = strtoupper(trim((string) $value));

        if (preg_match('/^Q?([1-4])$/', $raw, $matches) === 1) {
            return sprintf('Q%d', (int) $matches[1]);
        }

        return null;
    }
}
