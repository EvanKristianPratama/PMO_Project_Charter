<?php

namespace App\Http\Controllers\StrategicHouse\StrategicPillars;

use App\Http\Controllers\Controller;
use App\Services\StrategicHouse\StrategicPillars\StrategicPillarPageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly StrategicPillarPageService $strategicPillarPageService
    ) {}

    public function __invoke(Request $request, ?string $goal = null): Response
    {
        return Inertia::render(
            'StrategicHouse/StrategicPillar/Index',
            $this->strategicPillarPageService->getPageProps(
                $goal,
                $request->query('org_id'),
                $request->query('pilar')
            )
        );
    }
}
