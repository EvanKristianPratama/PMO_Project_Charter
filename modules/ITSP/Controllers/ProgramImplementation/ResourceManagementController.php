<?php

namespace Modules\ITSP\Controllers\ProgramImplementation;

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
        $props = $this->resourceManagementService->getIndexProps();
        $props['tableMode'] = 'resource_management';

        return Inertia::render('modules/ITSP/ProgramImplementation/ProjectCharter/ITInitiatives/Index',
            $props
        );
    }
}
