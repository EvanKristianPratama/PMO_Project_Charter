<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Services\ProgramImplementation\ResourceManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceManagementController extends Controller
{
    public function __construct(
        private readonly ResourceManagementService $resourceManagementService,
    ) {}

    public function __invoke(Request $request): Response
    {
        return Inertia::render(
            'ProgramImplementation/ResourceManagement/Index',
            $this->resourceManagementService->getIndexProps($request->only(['type', 'status'])),
        );
    }
}
