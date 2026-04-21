<?php

namespace App\Services\ProgramPlanning;

use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\TrsMapItBuilding;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ItBuildingBlockService
{
    public function getCoeOptions(): Collection
    {
        return MstCoe::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (MstCoe $coe): array => [
                'id' => (int) $coe->id,
                'name' => $coe->name,
            ]);
    }

    public function getItInitiativeOptions(): Collection
    {
        return MstInitiative::query()
            ->with([
                'coe:id,name', 
                'organization:id,name,groub_id', 
                'sourceData:id,name',
                'mappedProjects.pcStatusImplementations' => fn($q) => $query = $q->orderBy('year', 'desc')->orderByRaw("CASE 
                    WHEN month = 'Desember' THEN 12
                    WHEN month = 'November' THEN 11
                    WHEN month = 'Oktober' THEN 10
                    WHEN month = 'September' THEN 9
                    WHEN month = 'Agustus' THEN 8
                    WHEN month = 'Juli' THEN 7
                    WHEN month = 'Juni' THEN 6
                    WHEN month = 'Mei' THEN 5
                    WHEN month = 'April' THEN 4
                    WHEN month = 'Maret' THEN 3
                    WHEN month = 'Februari' THEN 2
                    WHEN month = 'Januari' THEN 1
                    ELSE 0 END DESC")->orderBy('id', 'desc')
            ])
            ->where('tipe_initiative', 2)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'description', 'coe_id', 'business_unit', 'tipe_initiative', 'source'])
            ->map(function (MstInitiative $initiative): array {
                // Ambil status terbaru dari semua proyek yang dimapping
                $latestStatus = $initiative->mappedProjects
                    ->flatMap(fn($project) => $project->pcStatusImplementations)
                    ->sortByDesc(fn($status) => $status->year . str_pad($this->monthToNumber($status->month), 2, '0', STR_PAD_LEFT) . str_pad($status->id, 10, '0', STR_PAD_LEFT))
                    ->first();

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'coe_id' => (int) $initiative->coe_id,
                    'coe_name' => $initiative->coe?->name ?: 'No COE',
                    'business_unit' => $initiative->organization?->name ?: '-',
                    'groub_id' => $initiative->organization?->groub_id,
                    'implementation_status' => $latestStatus?->status ?: null,
                    'statuses' => $initiative->mappedProjects
                        ->flatMap(fn($project) => $project->pcStatusImplementations)
                        ->map(fn($s) => [
                            'month' => $s->month,
                            'year' => $s->year,
                            'status' => $s->status,
                        ])->values()->all(),
                    'tipe_initiative' => (int) $initiative->tipe_initiative,
                    'source' => $initiative->source,
                    'source_name' => $initiative->sourceData?->name,
                ];
            });
    }

    private function monthToNumber(?string $month): int
    {
        return match (trim((string)$month)) {
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
            default => 0,
        };
    }

    public function getStatusPeriods(): Collection
    {
        return \App\Models\TrsStatusImplementation::query()
            ->select(['start', 'end', 'year'])
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderByRaw("CASE 
                WHEN start = 'Desember' THEN 12
                WHEN start = 'November' THEN 11
                WHEN start = 'Oktober' THEN 10
                WHEN start = 'September' THEN 9
                WHEN start = 'Agustus' THEN 8
                WHEN start = 'Juli' THEN 7
                WHEN start = 'Juni' THEN 6
                WHEN start = 'Mei' THEN 5
                WHEN start = 'April' THEN 4
                WHEN start = 'Maret' THEN 3
                WHEN start = 'Februari' THEN 2
                WHEN start = 'Januari' THEN 1
                ELSE 0 END DESC")
            ->get()
            ->map(fn ($period): array => [
                'start' => $period->start,
                'end' => $period->end,
                'year' => (int) $period->year,
                'label' => "{$period->start} - {$period->end} {$period->year}",
            ]);
    }

    public function getDigitalInitiativeOptions(): Collection
    {
        return MstInitiative::query()
            ->with(['coe:id,name', 'organization:id,name,groub_id', 'statusImplementations', 'sourceData:id,name'])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'description', 'coe_id', 'business_unit', 'tipe_initiative', 'source'])
            ->map(fn (MstInitiative $initiative): array => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
                'description' => $initiative->description,
                'coe_id' => (int) $initiative->coe_id,
                'coe_name' => $initiative->coe?->name ?: 'No COE',
                'business_unit' => $initiative->organization?->name ?: '-',
                'groub_id' => $initiative->organization?->groub_id,
                'implementation_status' => $initiative->latestStatusImplementation?->review_status ?: null,
                'statuses' => $initiative->statusImplementations->map(fn($s) => [
                    'start' => $s->start,
                    'end' => $s->end,
                    'year' => (int) $s->year,
                    'status' => $s->review_status,
                ])->values()->all(),
                'tipe_initiative' => (int) $initiative->tipe_initiative,
                'source' => $initiative->source,
                'source_name' => $initiative->sourceData?->name,
            ]);
    }

    public function getGroupedMappings(): array
    {
        return TrsMapItBuilding::query()
            ->with([
                'primaryCoe:id,name',
                'secondaryCoe:id,name',
                'initiative:id,code,name,description,coe_id,business_unit,source',
                'initiative.coe:id,name',
                'initiative.organization:id,name,groub_id',
                'initiative.latestStatusImplementation',
                'initiative.sourceData:id,name',
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
                                        'groub_id' => $item->initiative?->organization?->groub_id,
                                        'implementation_status' => $item->initiative?->latestStatusImplementation?->review_status ?: null,
                                        'source' => !is_null($item->initiative?->source) ? (int) $item->initiative->source : null,
                                        'source_name' => $item->initiative?->sourceData?->name,
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

    public function storeMapping(array $data): int
    {
        $initiativeIds = collect($data['initiative_ids'] ?? [])
            ->when(isset($data['initiative_id']), fn ($collection) => $collection->push($data['initiative_id']))
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
            ->where('primary', $data['primary'])
            ->where('secondary', $data['secondary'])
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
                    'primary' => $data['primary'],
                    'secondary' => $data['secondary'],
                    'initiative_id' => $initiativeId,
                ])
                ->all(),
        );

        return $initiativeIdsToCreate->count();
    }

    public function deletePrimary(int $primaryId): void
    {
        TrsMapItBuilding::query()
            ->where('primary', $primaryId)
            ->delete();
    }

    public function deleteSecondary(int $primaryId, int $secondaryId): void
    {
        TrsMapItBuilding::query()
            ->where('primary', $primaryId)
            ->where('secondary', $secondaryId)
            ->delete();
    }

    public function deleteInitiative(int $primaryId, int $secondaryId, int $initiativeId): void
    {
        TrsMapItBuilding::query()
            ->where('primary', $primaryId)
            ->where('secondary', $secondaryId)
            ->where('initiative_id', $initiativeId)
            ->delete();
    }

    public function deleteMultipleMappings(array $removals): void
    {
        collect($removals)
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
    }
}
