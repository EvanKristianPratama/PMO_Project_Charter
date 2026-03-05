<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\ScInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EditController extends Controller
{
    public function __invoke(ScInitiative $scInitiative): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $scInitiative->load([
            'masterInitiative:id,code,name',
            'scDetails' => fn ($query) => $query->latest('id'),
        ]);

        $detail = $scInitiative->scDetails->first();

        $initiativeOptions = MstInitiative::query()
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name'])
            ->map(fn (MstInitiative $initiative) => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
            ])
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Edit', [
            'compendium' => [
                'id' => (int) $scInitiative->id,
                'initiative_id' => $scInitiative->initiative_id ? (int) $scInitiative->initiative_id : null,
                'alias' => $scInitiative->alias,
                'useCase_description' => $scInitiative->useCase_description,
                'value' => (int) $scInitiative->value,
                'urgency' => (int) $scInitiative->urgency,
                'detail_useCase_description' => $detail?->useCase_description ?? '',
                'current_situation' => $detail?->current_situation ?? '',
                'key_functionalities' => $detail?->key_functionalities ?? '',
                'value_detail' => $detail?->value_detail ?? '',
                'urgency_detail' => $detail?->urgency_detail ?? '',
                'ease_implementation' => $detail ? (int) $detail->ease_implementation : 4,
                'ease_detail' => $detail?->ease_detail ?? '',
                'resource_requirement' => $detail ? (int) $detail->resource_requirement : 4,
                'resource_detail' => $detail?->resource_detail ?? '',
                'interpendencies' => $detail?->interpendencies ?? '',
                'sign_by' => $detail?->sign_by ?? '',
            ],
            'initiativeOptions' => $initiativeOptions,
        ]);
    }
}
