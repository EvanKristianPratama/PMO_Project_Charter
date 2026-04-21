<?php

namespace App\Http\Controllers\ProgramPlanning\StrategicHouse\RoadMap;

use App\Http\Controllers\Controller;
use App\Services\ProgramPlanning\StrategicHouse\RoadMap\RoadmapSummaryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SummaryController extends Controller
{
    public function __construct(
        private readonly RoadmapSummaryService $service,
    ) {}

    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render(
            'ProgramPlanning/StrategicHouse/RoadMap/Summary',
            $this->service->getPageProps(),
        );
    }
}
