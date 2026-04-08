<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Services\ProgramImplementation\ResourceManagementService;
use Inertia\Inertia;
use Inertia\Response;

class ResourceManagementController extends Controller
{
    public function __construct(
        private readonly ResourceManagementService $resourceManagementService,
    ) {}

    public function __invoke(): Response
    {
        return Inertia::render(
            'ProgramImplementation/ResourceManagement/Index',
            $this->resourceManagementService->getIndexProps(),
        );
    }
}
