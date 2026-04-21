<?php

namespace App\Http\Controllers\StrategicHouse\BusinessStrategy;

use App\Http\Controllers\Controller;
use App\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly BusinessStrategyService $service
    ) {}

    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render(
            'StrategicHouse/BusinessStrategy/Index',
            $this->service->getPageProps()
        );
    }
}
