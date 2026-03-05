<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

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

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Create', [
            'initiativeOptions' => $initiativeOptions,
        ]);
    }
}
