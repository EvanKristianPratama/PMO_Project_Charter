<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Services\ProgramImplementation\ResourceManagementPageService;
use Inertia\Inertia;
use Inertia\Response;

class ResourceManagementController extends Controller
{
    public function __construct(
        private readonly ResourceManagementPageService $pageService,
    ) {}

    public function __invoke(): Response
    {
        return Inertia::render(
            'ResourcesManagement/Index',
            $this->pageService->getIndexProps(),
        );
    }
}
