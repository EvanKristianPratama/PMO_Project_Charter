<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\ScInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $compendiumItems = ScInitiative::query()
            ->with([
                'masterInitiative:id,code,name',
                'scDetails' => fn ($query) => $query->latest('id'),
            ])
            ->whereHas('scDetails')
            ->orderBy('id')
            ->get([
                'id',
                'initiative_id',
                'alias',
                'useCase_description',
                'value',
                'urgency',
            ])
            ->map(function (ScInitiative $item): array {
                $detail = $item->scDetails->first();

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $item->initiative_id ? (int) $item->initiative_id : null,
                    'initiative_code' => $item->masterInitiative?->code,
                    'initiative_name' => $item->masterInitiative?->name,
                    'alias' => $item->alias,
                    'use_case_description' => $item->useCase_description,
                    'value' => (int) $item->value,
                    'urgency' => (int) $item->urgency,
                    'detail' => $detail ? [
                        'use_case_description' => $detail->useCase_description,
                        'current_situation' => $detail->current_situation,
                        'key_functionalities' => $detail->key_functionalities,
                        'value_detail' => $detail->value_detail,
                        'urgency_detail' => $detail->urgency_detail,
                        'ease_implementation' => (int) $detail->ease_implementation,
                        'ease_detail' => $detail->ease_detail,
                        'resource_requirement' => (int) $detail->resource_requirement,
                        'resource_detail' => $detail->resource_detail,
                        'interpendencies' => $detail->interpendencies,
                        'sign_by' => $detail->sign_by,
                    ] : null,
                ];
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Index', [
            'compendiumItems' => $compendiumItems,
            'totalCompendiumItems' => $compendiumItems->count(),
        ]);
    }
}
