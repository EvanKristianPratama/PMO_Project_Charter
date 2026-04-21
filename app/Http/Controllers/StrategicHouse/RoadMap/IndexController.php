<?php

namespace App\Http\Controllers\StrategicHouse\RoadMap;

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
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render(
            'StrategicHouse/RoadMap/Index',
            $this->service->getPageProps()
        );
    }
}
