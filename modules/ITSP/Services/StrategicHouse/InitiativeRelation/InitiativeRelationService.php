<?php

namespace Modules\ITSP\Services\StrategicHouse\InitiativeRelation;

use Modules\ITSP\Models\MstInitiative;
use Modules\ITSP\Models\MstInitiativeRelation;
use Illuminate\Support\Facades\Cache;

class InitiativeRelationService
{
    public function getIndexProps(): array
    {
        return [
            'mstInitiatives' => Cache::remember('sh_relation_initiatives_v4', 3600, fn () => MstInitiative::query()
                ->select(['id', 'code', 'name', 'coe_id', 'tipe_initiative', 'business_unit', 'status'])
                ->with([
                    'initiativeRelationsRow:id,initiative_code_row,initiative_code_column,type_relation,model_relasi',
                    'initiativeRelationsColumn:id,initiative_code_row,initiative_code_column,type_relation,model_relasi',
                    'coe:id,name',
                    'relationPosition:id,initiative_id,x,y,is_locked',
                    'mappedProjects:id',
                    'mappedProjects.pcStatusImplementations' => fn ($query) => $query
                        ->select(['id', 'project_id', 'month', 'year', 'status']),
                ])
                ->get()
                ->map(function (MstInitiative $initiative): array {
                    $latestStatus = $initiative->mappedProjects
                        ->flatMap(fn ($project) => $project->pcStatusImplementations)
                        ->sort(function ($left, $right): int {
                            $yearComparison = (int) ($right->year ?? 0) <=> (int) ($left->year ?? 0);
                            if ($yearComparison !== 0) {
                                return $yearComparison;
                            }
                            $monthComparison = $this->monthToNumber($right->month ?? null) <=> $this->monthToNumber($left->month ?? null);
                            if ($monthComparison !== 0) {
                                return $monthComparison;
                            }
                            return (int) ($right->id ?? 0) <=> (int) ($left->id ?? 0);
                        })
                        ->first();

                    return [
                        'id' => $initiative->id,
                        'code' => $initiative->code,
                        'name' => $initiative->name,
                        'coe_id' => $initiative->coe_id,
                        'tipe_initiative' => $initiative->tipe_initiative,
                        'business_unit' => $initiative->business_unit,
                        'initiative_relations_row' => $initiative->initiativeRelationsRow,
                        'initiative_relations_column' => $initiative->initiativeRelationsColumn,
                        'coe' => $initiative->coe,
                        'relation_position' => $initiative->relationPosition,
                        'status' => $latestStatus?->status ?? $initiative->status,
                        'project_id' => $initiative->mappedProjects->first()?->id,
                    ];
                })
                ->all()
            ),
            'initiativeRelations' => $this->initiativeRelations(),
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ];
    }

    public function getCreateProps(): array
    {
        return [
            'initiativeOptions' => $this->initiativeOptions(),
            'initiativeRelations' => $this->initiativeRelations(),
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ];
    }

    public function getEditProps(MstInitiativeRelation $initiativeRelation): array
    {
        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return [
            'initiativeRelation' => $this->serializeRelation($initiativeRelation),
            'initiativeOptions' => $this->initiativeOptions(),
            'initiativeRelations' => $this->initiativeRelations(),
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ];
    }

    public function createInitiativeRelation(array $payload): MstInitiativeRelation
    {
        $initiativeRelation = new MstInitiativeRelation;
        $initiativeRelation->forceFill($this->buildRelationPayload($payload));
        $initiativeRelation->save();

        return $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);
    }

    public function updateInitiativeRelation(
        MstInitiativeRelation $initiativeRelation,
        array $payload
    ): MstInitiativeRelation {
        $initiativeRelation->forceFill($this->buildRelationPayload($payload));
        $initiativeRelation->save();

        return $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);
    }

    public function deleteInitiativeRelation(MstInitiativeRelation $initiativeRelation): void
    {
        $initiativeRelation->delete();
    }

    public function getShowPayload(MstInitiativeRelation $initiativeRelation): array
    {
        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return [
            'data' => $this->serializeRelation($initiativeRelation),
        ];
    }

    private function buildRelationPayload(array $payload): array
    {
        $justifikasi = $payload['justifikasi'] ?? $payload['description'] ?? null;

        return [
            'model_relasi' => $payload['model_relasi'],
            'initiative_code_row' => (int) $payload['initiative_code_row'],
            'initiative_code_column' => (int) $payload['initiative_code_column'],
            'type_relation' => $payload['type_relation'],
            'justifikasi' => $justifikasi,
            'description' => $justifikasi,
        ];
    }

    private function serializeRelation(MstInitiativeRelation $initiativeRelation): array
    {
        $justifikasi = $initiativeRelation->getAttribute('justifikasi')
            ?? $initiativeRelation->getAttribute('description');

        return [
            'id' => (int) $initiativeRelation->id,
            'model_relasi' => $initiativeRelation->model_relasi,
            'initiative_code_row' => $initiativeRelation->initiative_code_row,
            'initiative_code_column' => $initiativeRelation->initiative_code_column,
            'type_relation' => $initiativeRelation->type_relation,
            'justifikasi' => $justifikasi,
            'description' => $justifikasi,
            'x' => $initiativeRelation->x,
            'y' => $initiativeRelation->y,
            'initiative_row' => $initiativeRelation->initiativeRow
                ? [
                    'id' => (int) $initiativeRelation->initiativeRow->id,
                    'code' => $initiativeRelation->initiativeRow->code,
                    'name' => $initiativeRelation->initiativeRow->name,
                ]
                : null,
            'initiative_column' => $initiativeRelation->initiativeColumn
                ? [
                    'id' => (int) $initiativeRelation->initiativeColumn->id,
                    'code' => $initiativeRelation->initiativeColumn->code,
                    'name' => $initiativeRelation->initiativeColumn->name,
                ]
                : null,
            'created_at' => $initiativeRelation->created_at,
            'updated_at' => $initiativeRelation->updated_at,
        ];
    }

    private function initiativeOptions(): array
    {
        return MstInitiative::query()
            ->select([
                'id',
                'coe_id',
                'code',
                'name',
                'tipe_initiative',
                'description',
                'status',
                'business_unit',
            ])
            ->with([
                'organization:id,name', 
                'coe:id,name', 
                'relationPosition', 
                'mappedProjects:id',
                'mappedProjects.pcStatusImplementations' => fn ($query) => $query
                    ->select(['id', 'project_id', 'month', 'year', 'status']),
            ])
            ->orderBy('id')
            ->get()
            ->map(function (MstInitiative $initiative): array {
                $latestStatus = $initiative->mappedProjects
                    ->flatMap(fn ($project) => $project->pcStatusImplementations)
                    ->sort(function ($left, $right): int {
                        $yearComparison = (int) ($right->year ?? 0) <=> (int) ($left->year ?? 0);
                        if ($yearComparison !== 0) {
                            return $yearComparison;
                        }
                        $monthComparison = $this->monthToNumber($right->month ?? null) <=> $this->monthToNumber($left->month ?? null);
                        if ($monthComparison !== 0) {
                            return $monthComparison;
                        }
                        return (int) ($right->id ?? 0) <=> (int) ($left->id ?? 0);
                    })
                    ->first();

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'tipe_initiative' => $initiative->tipe_initiative,
                    'description' => $initiative->description,
                    'status' => $latestStatus?->status ?? $initiative->status,
                    'business_unit' => $initiative->business_unit,
                    'business_unit_name' => $initiative->organization?->name,
                    'coe_name' => $initiative->coe?->name,
                    'relation_position' => $initiative->relationPosition,
                ];
            })
            ->values()
            ->all();
    }

    private function initiativeRelations(): array
    {
        return Cache::remember('sh_initiative_relations_v1', 3600, fn () => MstInitiativeRelation::query()
            ->with([
                'initiativeRow:id,code,name',
                'initiativeColumn:id,code,name',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(fn (MstInitiativeRelation $initiativeRelation): array => $this->serializeRelation($initiativeRelation))
            ->values()
            ->all());
    }

    private function typeRelationOptions(): array
    {
        return [
            ['value' => 1, 'label' => 'Predecessor'],
            ['value' => 2, 'label' => 'Successor'],
        ];
    }

    private function modelRelationOptions(): array
    {
        return Cache::remember('sh_model_relation_options_v1', 3600, fn () => MstInitiativeRelation::query()
            ->whereNotNull('model_relasi')
            ->where('model_relasi', '!=', '')
            ->select('model_relasi')
            ->distinct()
            ->orderBy('model_relasi')
            ->pluck('model_relasi')
            ->values()
            ->all());
    }

    private function monthToNumber(?string $month): int
    {
        return match (trim((string) $month)) {
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12,
            default => 0,
        };
    }
}
