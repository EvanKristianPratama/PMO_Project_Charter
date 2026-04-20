<?php

namespace App\Services\ProgramPlanning\InitiativeRelation;

use App\Models\MstInitiative;
use App\Models\MstInitiativeRelation;
use Illuminate\Support\Facades\Schema;

class InitiativeRelationService
{
    public function getIndexProps(): array
    {
        return [
            'mstInitiatives' => MstInitiative::query()
                ->with([
                    'initiativeRelationsRow',
                    'initiativeRelationsColumn',
                    'coe:id,name',
                    'latestStatusImplementation',
                    'latestStatus',
                ])
                ->get(),
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
        $relationPayload = [
            'model_relasi' => $payload['model_relasi'],
            'initiative_code_row' => (int) $payload['initiative_code_row'],
            'initiative_code_column' => (int) $payload['initiative_code_column'],
            'type_relation' => $payload['type_relation'],
        ];

        if (Schema::hasColumn('mst_initiative_relation', 'justifikasi')) {
            $relationPayload['justifikasi'] = $justifikasi;
        }

        if (Schema::hasColumn('mst_initiative_relation', 'description')) {
            $relationPayload['description'] = $justifikasi;
        }

        return $relationPayload;
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
            ->with(['organization:id,name', 'coe:id,name'])
            ->orderBy('id')
            ->get()
            ->map(fn (MstInitiative $initiative): array => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
                'tipe_initiative' => $initiative->tipe_initiative,
                'description' => $initiative->description,
                'status' => $initiative->status,
                'business_unit' => $initiative->business_unit,
                'business_unit_name' => $initiative->organization?->name,
                'coe_name' => $initiative->coe?->name,
            ])
            ->values()
            ->all();
    }

    private function initiativeRelations(): array
    {
        return MstInitiativeRelation::query()
            ->with([
                'initiativeRow:id,code,name',
                'initiativeColumn:id,code,name',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(fn (MstInitiativeRelation $initiativeRelation): array => $this->serializeRelation($initiativeRelation))
            ->values()
            ->all();
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
        return MstInitiativeRelation::query()
            ->whereNotNull('model_relasi')
            ->where('model_relasi', '!=', '')
            ->select('model_relasi')
            ->distinct()
            ->orderBy('model_relasi')
            ->pluck('model_relasi')
            ->values()
            ->all();
    }
}
