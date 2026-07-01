<?php

namespace Modules\ITSP\Controllers\StrategicHouse\RoadMap;

use App\Http\Controllers\Controller;
use App\Services\StrategicHouse\RoadMap\RoadmapSummaryService;
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

        return Inertia::render('modules/ITSP/StrategicHouse/RoadMap/Summary',
            $this->service->getPageProps(),
        );
    }
}
