<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\TrsMapItBuilding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ItBuildingBlockController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('ProgramImplementation/ItBuildingBlocks/Index', [
            'groups' => $this->groupedMappings(),
            'coeOptions' => MstCoe::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (MstCoe $coe): array => [
                    'id' => (int) $coe->id,
                    'name' => $coe->name,
                ])
                ->values(),
            'initiativeOptions' => MstInitiative::query()
                ->where('tipe_initiative', 1)
                ->orderBy('code')
                ->orderBy('name')
                ->get(['id', 'code', 'name'])
                ->map(fn (MstInitiative $initiative): array => [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                ])
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary' => ['required', 'integer', 'exists:mst_coe,id'],
            'secondary' => ['required', 'integer', 'exists:mst_coe,id', 'different:primary'],
            'initiative_id' => ['nullable', 'integer', 'exists:mst_initiative,id'],
            'initiative_ids' => ['nullable', 'array', 'min:1'],
            'initiative_ids.*' => ['required', 'integer', 'exists:mst_initiative,id'],
        ]);

        $initiativeIds = collect($validated['initiative_ids'] ?? [])
            ->when(isset($validated['initiative_id']), fn ($collection) => $collection->push($validated['initiative_id']))
            ->map(fn ($initiativeId) => (int) $initiativeId)
            ->filter(fn (int $initiativeId) => $initiativeId > 0)
            ->unique()
            ->values();

        if ($initiativeIds->isEmpty()) {
            throw ValidationException::withMessages([
                'initiative_ids' => 'Pilih minimal satu initiative.',
            ]);
        }

        $existingInitiativeIds = TrsMapItBuilding::query()
            ->where('primary', $validated['primary'])
            ->where('secondary', $validated['secondary'])
            ->whereIn('initiative_id', $initiativeIds->all())
            ->pluck('initiative_id')
            ->map(fn ($initiativeId) => (int) $initiativeId);

        $initiativeIdsToCreate = $initiativeIds
            ->reject(fn (int $initiativeId) => $existingInitiativeIds->contains($initiativeId))
            ->values();

        if ($initiativeIdsToCreate->isEmpty()) {
            throw ValidationException::withMessages([
                'initiative_ids' => 'Semua initiative yang dipilih sudah termapping pada kombinasi Primary dan Secondary ini.',
            ]);
        }

        TrsMapItBuilding::query()->insert(
            $initiativeIdsToCreate
                ->map(fn (int $initiativeId): array => [
                    'primary' => $validated['primary'],
                    'secondary' => $validated['secondary'],
                    'initiative_id' => $initiativeId,
                ])
                ->all(),
        );

        $createdCount = $initiativeIdsToCreate->count();

        return back()->with('success', $createdCount > 1
            ? "{$createdCount} mapping IT Building Block berhasil ditambahkan."
            : 'Mapping IT Building Block berhasil ditambahkan.');
    }

    public function destroyPrimary(MstCoe $primary): RedirectResponse
    {
        TrsMapItBuilding::query()
            ->where('primary', $primary->id)
            ->delete();

        return back()->with('success', 'Primary IT Building Block berhasil dihapus.');
    }

    public function destroySecondary(MstCoe $primary, MstCoe $secondary): RedirectResponse
    {
        TrsMapItBuilding::query()
            ->where('primary', $primary->id)
            ->where('secondary', $secondary->id)
            ->delete();

        return back()->with('success', 'Secondary IT Building Block berhasil dihapus.');
    }

    public function destroyInitiative(MstCoe $primary, MstCoe $secondary, MstInitiative $initiative): RedirectResponse
    {
        TrsMapItBuilding::query()
            ->where('primary', $primary->id)
            ->where('secondary', $secondary->id)
            ->where('initiative_id', $initiative->id)
            ->delete();

        return back()->with('success', 'Mapping initiative berhasil dihapus.');
    }

    public function destroyInitiatives(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'removals' => ['required', 'array', 'min:1'],
            'removals.*.primary' => ['required', 'integer', 'exists:mst_coe,id'],
            'removals.*.secondary' => ['required', 'integer', 'exists:mst_coe,id'],
            'removals.*.initiative_id' => ['required', 'integer', 'exists:mst_initiative,id'],
        ]);

        collect($validated['removals'])
            ->unique(fn (array $item): string => implode(':', [
                $item['primary'],
                $item['secondary'],
                $item['initiative_id'],
            ]))
            ->each(function (array $item): void {
                TrsMapItBuilding::query()
                    ->where('primary', $item['primary'])
                    ->where('secondary', $item['secondary'])
                    ->where('initiative_id', $item['initiative_id'])
                    ->delete();
            });

        return back()->with('success', 'Penghapusan initiative berhasil disimpan.');
    }

    private function groupedMappings(): array
    {
        return TrsMapItBuilding::query()
            ->with([
                'primaryCoe:id,name',
                'secondaryCoe:id,name',
                'initiative:id,code,name,description,coe_id,business_unit',
                'initiative.coe:id,name',
                'initiative.organization:id,name',
                'initiative.latestStatusImplementation',
            ])
            ->get(['primary', 'secondary', 'initiative_id'])
            ->filter(fn (TrsMapItBuilding $item) => filled($item->initiative?->name))
            ->groupBy(fn (TrsMapItBuilding $item) => (string) ($item->primary ?? '0'))
            ->map(function ($primaryRows, $primaryId): array {
                $firstRow = $primaryRows->first();
                $primaryName = $firstRow?->primaryCoe?->name ?: 'Unmapped Primary';

                return [
                    'primary_id' => (int) $primaryId,
                    'primary' => $primaryName,
                    'secondary_groups' => $primaryRows
                        ->groupBy(fn (TrsMapItBuilding $item) => (string) ($item->secondary ?? '0'))
                        ->map(function ($secondaryRows, $secondaryId): array {
                            $firstSecondaryRow = $secondaryRows->first();
                            $secondaryName = $firstSecondaryRow?->secondaryCoe?->name ?: 'Unmapped Secondary';

                            return [
                                'secondary_id' => (int) $secondaryId,
                                'secondary' => $secondaryName,
                                'initiatives' => $secondaryRows
                                    ->map(fn (TrsMapItBuilding $item): array => [
                                        'map_key' => implode('-', [
                                            (string) ($item->primary ?? 'na'),
                                            (string) ($item->secondary ?? 'na'),
                                            (string) ($item->initiative_id ?? 'na'),
                                        ]),
                                        'initiative_id' => (int) ($item->initiative_id ?? 0),
                                        'code' => $item->initiative?->code,
                                        'name' => $item->initiative?->name,
                                        'description' => $item->initiative?->description,
                                        'coe_id' => (int) ($item->initiative?->coe_id ?? 0),
                                        'coe_name' => $item->initiative?->coe?->name ?: 'No COE',
                                        'business_unit' => $item->initiative?->organization?->name ?: '-',
                                        'implementation_status' => $item->initiative?->latestStatusImplementation?->review_status ?: null,
                                    ])
                                    ->unique('map_key')
                                    ->sortBy('name')
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('primary')
            ->values()
            ->all();
    }
}
