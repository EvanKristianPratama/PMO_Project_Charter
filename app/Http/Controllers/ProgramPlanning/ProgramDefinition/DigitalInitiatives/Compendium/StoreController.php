<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use App\Models\MstInitiative;
use App\Models\ScDetail;
use App\Models\ScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'initiative_id' => 'required|integer|exists:mst_initiative,id',
            'alias' => 'nullable|string|max:255',
            'useCase_description' => 'nullable|string',
            'value' => 'required|integer|min:1|max:5',
            'urgency' => 'required|integer|min:1|max:5',
            'detail_useCase_description' => 'required|string|max:255',
            'current_situation' => 'required|string|max:255',
            'key_functionalities' => 'required|string|max:255',
            'value_detail' => 'required|string|max:255',
            'urgency_detail' => 'required|string|max:255',
            'ease_implementation' => 'required|integer|min:1|max:5',
            'ease_detail' => 'required|string|max:255',
            'resource_requirement' => 'required|integer|min:1|max:5',
            'resource_detail' => 'required|string|max:255',
            'interpendencies' => 'required|string|max:255',
            'sign_by' => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($validated): void {
            $initiative = MstInitiative::query()->findOrFail($validated['initiative_id']);

            $scopeInitiative = ScInitiative::query()
                ->where('initiative_id', $initiative->id)
                ->latest('id')
                ->first();

            if (!$scopeInitiative) {
                $scopeInitiative = ScInitiative::create([
                    'initiative_id' => $initiative->id,
                    'alias' => $this->resolveAlias($validated['alias'] ?? null, $initiative),
                    'useCase_description' => $this->resolveDescription($validated['useCase_description'] ?? null, $initiative),
                    'value' => $validated['value'],
                    'urgency' => $validated['urgency'],
                ]);
            } else {
                $scopeInitiative->update([
                    'alias' => $this->resolveAlias($validated['alias'] ?? null, $initiative),
                    'useCase_description' => $this->resolveDescription($validated['useCase_description'] ?? null, $initiative),
                    'value' => $validated['value'],
                    'urgency' => $validated['urgency'],
                ]);
            }

            $scopeDetail = ScDetail::query()
                ->where('digital_id', $scopeInitiative->id)
                ->latest('id')
                ->first();

            $detailPayload = [
                'digital_id' => $scopeInitiative->id,
                'useCase_description' => $validated['detail_useCase_description'],
                'current_situation' => $validated['current_situation'],
                'key_functionalities' => $validated['key_functionalities'],
                'value_detail' => $validated['value_detail'],
                'urgency_detail' => $validated['urgency_detail'],
                'ease_implementation' => $validated['ease_implementation'],
                'ease_detail' => $validated['ease_detail'],
                'resource_requirement' => $validated['resource_requirement'],
                'resource_detail' => $validated['resource_detail'],
                'interpendencies' => $validated['interpendencies'],
                'sign_by' => $validated['sign_by'],
            ];

            if (!$scopeDetail) {
                ScDetail::create($detailPayload);
            } else {
                $scopeDetail->update($detailPayload);
            }

            $compendiumSourceId = $this->resolveSourceId('compendium', 1);
            if ($compendiumSourceId !== null) {
                $initiative->update(['source' => $compendiumSourceId]);
            }
        });

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives.compendium.index')
            ->with('success', 'Compendium berhasil ditambahkan.');
    }

    private function resolveSourceId(string $keyword, int $fallbackId): ?int
    {
        $sourceId = DataSource::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
            ->value('id');

        if ($sourceId) {
            return (int) $sourceId;
        }

        return DataSource::query()->whereKey($fallbackId)->exists() ? $fallbackId : null;
    }

    private function resolveAlias(?string $alias, MstInitiative $initiative): string
    {
        $cleanAlias = trim((string) $alias);
        if ($cleanAlias !== '') {
            return $cleanAlias;
        }

        $code = trim((string) ($initiative->code ?? ''));
        if ($code !== '') {
            return $code;
        }

        return trim((string) $initiative->name) ?: '-';
    }

    private function resolveDescription(?string $description, MstInitiative $initiative): string
    {
        $cleanDescription = trim((string) $description);
        if ($cleanDescription !== '') {
            return $cleanDescription;
        }

        $initiativeDescription = trim((string) ($initiative->description ?? ''));
        if ($initiativeDescription !== '') {
            return $initiativeDescription;
        }

        return trim((string) $initiative->name) ?: '-';
    }
}
