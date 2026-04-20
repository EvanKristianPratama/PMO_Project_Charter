<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\ProgramImplementation\ProgramImplementationPageService;
use Inertia\Inertia;
use Inertia\Response;

class ProgramImplementationController extends Controller
{
    public function __construct(
        private readonly ProgramImplementationPageService $pageService,
    ) {}

    public function __invoke(): Response|RedirectResponse
    {
        return Inertia::render(
            'ProgramImplementation/Index',
            $this->pageService->getOverviewPageProps(),
        );
    }
}
