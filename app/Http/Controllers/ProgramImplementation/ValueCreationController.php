<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Services\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeService;
use Inertia\Inertia;
use Inertia\Response;

class ValueCreationController extends Controller
{
    public function __construct(
        private readonly ITInitiativeService $itInitiativeService
    ) {}

    public function index(): Response
    {
        $props = $this->itInitiativeService->getIndexProps();
        $props['tableMode'] = 'value_creation';
        
        // Fetch initiatives with their impact_value from charters and their primary CoE
        $valueCreationData = MstInitiative::query()
            ->where('tipe_initiative', 2)
            ->with([
                'coe:id,name',
                'organization:id,name',
                'mappedProjects.charter' => function ($query) {
                    $query->select('trs_project_charters.id', 'trs_project_charters.project_id', 'trs_project_charters.impact_value', 'trs_project_charters.version_label');
                }
            ])
            ->get()
            ->map(function ($initiative) {
                // Get impact_value from the first project's charter (latest is preferred)
                $project = $initiative->mappedProjects->first();
                $impactValue = $project?->charter?->impact_value ?? '-';
                
                return [
                    'id' => $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'impact_value' => $impactValue,
                    'coe_name' => $initiative->coe?->name ?? 'Unassigned',
                ];
            });

        $props['valueCreationData'] = $valueCreationData;
        
        return Inertia::render(
            'ProgramImplementation/ProjectCharter/ITInitiatives/Index',
            $props
        );
    }
}
