<?php

namespace Modules\ITOM\Controllers\BusinessProcess\OrganizationStructure;

use App\Http\Controllers\Controller;
use App\Services\BusinessProcess\OrganizationStructureService;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(OrganizationStructureService $organizationStructureService): Response
    {
        return Inertia::render('modules/ITOM/OrganizationStructure/Index', [
            'organizationStructureRows' => $organizationStructureService->getOrganizationStructureRows(),
        ]);
    }
}
