<?php

namespace App\Http\Controllers\ProgramPlanning\StrategicHouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicHouse\IndexRequest;
use App\Services\ProgramPlanning\StrategicHouse\StrategicHousePageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly StrategicHousePageService $strategicHousePageService
    ) {}

    public function __invoke(IndexRequest $request): Response|RedirectResponse
    {
        if ($request->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render(
            'ProgramPlanning/StrategicHouse/Index',
            $this->strategicHousePageService->getPageProps($request->filters())
        );
    }
}
