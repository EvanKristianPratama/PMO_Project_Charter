<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Http\Controllers\Controller;
use App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __construct(
        private readonly MasterMilestonePageService $pageService,
    ) {}

    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $selectedInitiativeId = request()->filled('initiative_id')
            ? (int) request()->input('initiative_id')
            : null;

        return Inertia::render(
            'ProgramPlanning/ProgramDefinition/DigitalInitiatives/Roadmap/Create',
            $this->pageService->getCreatePageProps($selectedInitiativeId),
        );
    }
}
