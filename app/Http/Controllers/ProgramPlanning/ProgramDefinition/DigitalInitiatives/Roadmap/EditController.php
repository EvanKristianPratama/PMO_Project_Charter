<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Http\Controllers\Controller;
use App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EditController extends Controller
{
    public function __construct(
        private readonly MasterMilestonePageService $pageService,
    ) {}

    public function __invoke(): Response|RedirectResponse
    {

        $selectedInitiativeId = request()->filled('initiative_id')
            ? (int) request()->input('initiative_id')
            : null;

        $selectedMilestoneId = request()->filled('milestone_id')
            ? (int) request()->input('milestone_id')
            : null;

        return Inertia::render(
            'ProgramPlanning/ProgramDefinition/DigitalInitiatives/Roadmap/Edit',
            $this->pageService->getEditPageProps($selectedInitiativeId, $selectedMilestoneId),
        );
    }
}
