<?php

namespace App\Http\Controllers\InitiativeRelation;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\MstInitiativeRelation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InitiativeRelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $mstInitiatives = MstInitiative::with([
            'initiativeRelationsRow',
            'initiativeRelationsColumn',
            'coe:id,name',
            'latestStatus',
        ])
            ->get();
        $initiativeRelations = MstInitiativeRelation::query()
            ->with([
                'initiativeRow:id,code,name',
                'initiativeColumn:id,code,name',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(fn (MstInitiativeRelation $relation) => $this->serializeRelation($relation))
            ->values();

        return Inertia::render('ProgramImplementation/InitiativeRelation/InitiativeRelation', [
            'mstInitiatives' => $mstInitiatives,
            'initiativeRelations' => $initiativeRelations,
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response|JsonResponse
    {
        return Inertia::render('ProgramImplementation/InitiativeRelation/Create', [
            'initiativeOptions' => $this->initiativeOptions(),
            'initiativeRelations' => $this->initiativeRelations(),
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateRelationPayload($request);

        $initiativeRelation = new MstInitiativeRelation();
        $initiativeRelation->forceFill($this->buildRelationPayload($validated));
        $initiativeRelation->save();

        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return redirect()
            ->route('initiative-relations.index')
            ->with('success', 'Initiative relation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MstInitiativeRelation $initiativeRelation): JsonResponse
    {
        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return response()->json([
            'data' => $this->serializeRelation($initiativeRelation),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, MstInitiativeRelation $initiativeRelation): Response|JsonResponse
    {
        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return Inertia::render('ProgramImplementation/InitiativeRelation/Edit', [
            'initiativeRelation' => $this->serializeRelation($initiativeRelation),
            'initiativeOptions' => $this->initiativeOptions(),
            'initiativeRelations' => $this->initiativeRelations(),
            'typeRelationOptions' => $this->typeRelationOptions(),
            'modelRelationOptions' => $this->modelRelationOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MstInitiativeRelation $initiativeRelation): RedirectResponse|JsonResponse
    {
        $validated = $this->validateRelationPayload($request, forUpdate: true);

        $initiativeRelation->forceFill($this->buildRelationPayload($validated));
        $initiativeRelation->save();
        $initiativeRelation->load([
            'initiativeRow:id,code,name',
            'initiativeColumn:id,code,name',
        ]);

        return redirect()
            ->route('initiative-relations.index')
            ->with('success', 'Initiative relation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, MstInitiativeRelation $initiativeRelation): RedirectResponse|JsonResponse
    {
        $initiativeRelation->delete();

        return back()->with('success', 'Initiative relation deleted successfully.');
    }

    private function validateRelationPayload(Request $request, bool $forUpdate = false): array
    {
        $rowRules = ['required', 'integer', 'exists:mst_initiative,id'];
        if (! $forUpdate) {
            $rowRules[] = 'different:initiative_code_column';
        }

        return $request->validate([
            'model_relasi' => ['required', 'string', 'max:50'],
            'initiative_code_row' => $rowRules,
            'initiative_code_column' => ['required', 'integer', 'exists:mst_initiative,id'],
            'type_relation' => ['required', 'integer', Rule::in(array_column($this->typeRelationOptions(), 'value'))],
            'justifikasi' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function buildRelationPayload(array $validated): array
    {
        $payload = [
            'model_relasi' => $validated['model_relasi'],
            'initiative_code_row' => (int) $validated['initiative_code_row'],
            'initiative_code_column' => (int) $validated['initiative_code_column'],
            'type_relation' => $validated['type_relation'],
        ];

        $justifikasi = $validated['justifikasi'] ?? $validated['description'] ?? null;

        if (Schema::hasColumn('mst_initiative_relation', 'justifikasi')) {
            $payload['justifikasi'] = $justifikasi;
        }

        if (Schema::hasColumn('mst_initiative_relation', 'description')) {
            $payload['description'] = $justifikasi;
        }

        return $payload;
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
            ->map(fn (MstInitiative $initiative) => [
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
            ->map(fn (MstInitiativeRelation $relation) => $this->serializeRelation($relation))
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
