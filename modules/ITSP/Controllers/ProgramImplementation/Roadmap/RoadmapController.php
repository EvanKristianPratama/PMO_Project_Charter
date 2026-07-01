<?php

namespace Modules\ITSP\Controllers\ProgramImplementation\Roadmap;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\Roadmap\RoadmapPageRequest;
use App\Models\MstInitiative;
use App\Services\ProgramImplementation\Roadmap\RoadmapPageService;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function __construct(
        private readonly RoadmapPageService $roadmapPageService,
    ) {}

    public function index(RoadmapPageRequest $request): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/RoadMap/Index', $this->roadmapPageService->getOverviewPageProps(
            $request->projectCharterId(),
            $request->legacyProjectId(),
        ));
    }

    public function add(RoadmapPageRequest $request): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/RoadMap/Create', $this->roadmapPageService->getEditorPageProps(
            $request->projectCharterId(),
            $request->legacyProjectId(),
        ));
    }

    public function edit(RoadmapPageRequest $request): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/RoadMap/Edit', $this->roadmapPageService->getEditorPageProps(
            $request->projectCharterId(),
            $request->legacyProjectId(),
        ));
    }

    public function show(MstInitiative $initiative): Response
    {
        return Inertia::render('modules/ITSP/ProgramImplementation/RoadMap/Show', $this->roadmapPageService->getProgramPageProps($initiative));
    }
}
