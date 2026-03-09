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
        $validated = $request->validate([
            'initiative_id' => 'required|integer|exists:mst_initiative,id',
            'alias' => 'nullable|string|max:255',
            'useCase_description' => 'nullable|string',
            'value' => 'required|integer|in:1,2,3,4',
            'urgency' => 'required|integer|in:1,2,3,4',
        ]);

        DB::transaction(function () use ($validated): void {
            $initiative = MstInitiative::findOrFail($validated['initiative_id']);

            $scInitiative = TrsScInitiative::create([
                'usecase' => $this->resolveAlias($validated['alias'] ?? null, $initiative),
                'description' => $this->resolveDescription($validated['useCase_description'] ?? null, $initiative),
                'value' => $validated['value'],
                'urgency' => $validated['urgency'],
            ]);

            $scInitiative->mstInitiatives()->sync([$initiative->id]);

            $appendixSourceId = $this->resolveSourceId('appendix', 2);
            if ($appendixSourceId !== null) {
                $initiative->update(['source' => $appendixSourceId]);
            }
        });

        return redirect()
            ->route('program-planning.program-definition.digital-initiatives.appendix.index')
            ->with('success', 'Appendix berhasil ditambahkan.');
    }

    private function resolveSourceId(string $keyword, int $fallbackId): ?int
    {
        $sourceId = DataSource::where('name', 'LIKE', '%' . $keyword . '%')
            ->value('id');

        if ($sourceId) {
            return (int) $sourceId;
        }

        return DataSource::whereKey($fallbackId)->exists() ? $fallbackId : null;
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
