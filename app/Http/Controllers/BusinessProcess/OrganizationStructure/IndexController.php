<?php

namespace App\Http\Controllers\BusinessProcess\OrganizationStructure;

use App\Http\Controllers\Controller;
use App\Services\Architecture\OrganizationStructureService;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(OrganizationStructureService $organizationStructureService): Response
    {
        return Inertia::render('OrganizationStructure/Index', [
            'organizationStructureRows' => $organizationStructureService->getOrganizationStructureRows(),
        ]);
    }
}
