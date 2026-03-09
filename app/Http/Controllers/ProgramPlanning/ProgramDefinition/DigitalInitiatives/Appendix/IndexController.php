<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\TrsScInitiative;
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

        $appendixItems = TrsScInitiative::with(['mstInitiatives:id,code,name'])
            ->orderBy('id')
            ->get([
                'id',
                'usecase',
                'description',
                'value',
                'urgency',
            ])
            ->map(function (TrsScInitiative $item) {
                $firstMst = $item->mstInitiatives->first();

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $firstMst?->id,
                    'initiative_code' => $firstMst?->code,
                    'initiative_name' => $firstMst?->name,
                    'alias' => $item->usecase,
                    'use_case_description' => $item->description,
                    'value' => (int) $item->value,
                    'urgency' => (int) $item->urgency,
                ];
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Index', [
            'appendixItems' => $appendixItems,
            'totalAppendixItems' => $appendixItems->count(),
        ]);
    }
}
