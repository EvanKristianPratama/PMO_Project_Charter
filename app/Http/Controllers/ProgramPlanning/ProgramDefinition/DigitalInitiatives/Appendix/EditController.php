<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EditController extends Controller
{
    public function __invoke(TrsScInitiative $scInitiative): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $scInitiative->load('mstInitiatives:id,code,name');

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name'])
            ->map(fn (MstInitiative $initiative) => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
            ])
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Edit', [
            'appendix' => [
                'id' => (int) $scInitiative->id,
                'initiative_id' => $scInitiative->mstInitiatives->first()?->id,
                'alias' => $scInitiative->usecase,
                'useCase_description' => $scInitiative->description,
                'value' => (int) $scInitiative->value,
                'urgency' => (int) $scInitiative->urgency,
            ],
            'initiativeOptions' => $initiativeOptions,
        ]);
    }
}
