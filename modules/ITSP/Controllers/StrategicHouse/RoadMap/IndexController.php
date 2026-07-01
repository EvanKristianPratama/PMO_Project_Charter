<?php

namespace Modules\ITSP\Controllers\StrategicHouse\RoadMap;

use App\Http\Controllers\Controller;
use App\Services\StrategicHouse\RoadMap\ItInitiativeRoadmapService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly ItInitiativeRoadmapService $service
    ) {}

    public function __invoke(): Response|RedirectResponse
    {

        return Inertia::render('modules/ITSP/StrategicHouse/RoadMap/Index',
            $this->service->getPageProps()
        );
    }
}
