<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

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

        $appendixItems = ScInitiative::query()
            ->with(['masterInitiative:id,code,name'])
            ->orderBy('id')
            ->get([
                'id',
                'initiative_id',
                'alias',
                'useCase_description',
                'value',
                'urgency',
            ])
            ->map(fn (ScInitiative $item) => [
                'id' => (int) $item->id,
                'initiative_id' => $item->initiative_id ? (int) $item->initiative_id : null,
                'initiative_code' => $item->masterInitiative?->code,
                'initiative_name' => $item->masterInitiative?->name,
                'alias' => $item->alias,
                'use_case_description' => $item->useCase_description,
                'value' => (int) $item->value,
                'urgency' => (int) $item->urgency,
            ])
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Index', [
            'appendixItems' => $appendixItems,
            'totalAppendixItems' => $appendixItems->count(),
        ]);
    }
}
