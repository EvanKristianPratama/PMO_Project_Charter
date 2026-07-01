<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Http\Controllers\Controller;
use App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly MasterMilestonePageService $pageService,
    ) {}

    public function __invoke(): Response|RedirectResponse
    {

        return Inertia::render('modules/ITSP/ProgramPlanning/ProgramDefinition/DigitalInitiatives/Roadmap/Index', $this->pageService->getIndexPageProps());
    }
}
