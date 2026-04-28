<?php

namespace App\Http\Controllers\StrategicHouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicHouse\IndexRequest;
use App\Services\StrategicHouse\StrategicHousePageService;
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

        $filters = $request->filters();
        $selectedProps = $this->getPropsForView(
            $filters['view'] ?? 'mapping',
            $filters['roadmap'] ?? 'it',
        );
        $props = $this->strategicHousePageService->getPageProps($filters, $selectedProps);

        return Inertia::render('StrategicHouse/Index', $props);
    }

    private function getPropsForView(string $view, string $roadmapMode = 'it'): array
    {
        return match ($view) {
            'mapping' => [
                'summary',
                'technologyCards',
                'strategyCards',
                'foundationCard',
                'architectureCard',
                'tbcCard',
                'unassignedInitiatives',
                'mappingBusinessStrategyGroups',
                'mappingBusinessStrategyColumns',
                'mappingBusinessStrategyOrganizationOptions',
            ],
            'business-strategy' => [
                'businessStrategyPage',
                'businessStrategySummary',
                'businessStrategyHeaderGoals',
                'businessStrategyEnablerGoals',
                'businessStrategyGroups',
                'businessStrategyColumns',
                'businessStrategyOrganizationOptions',
            ],
            'dual-growth' => ['dualGrowthGoals'],
            'digital-transformation-initiatives' => ['digitalInitiativeOptions'],
            'it-building-blocs' => ['itBuildingBlockMatrix', 'itInitiativeOptions'],
            'it-initiatives' => ['itInitiativeOptions'],
            'initiative-support' => ['initiativeSupportGroups', 'initiativeSupportDigitalOptions', 'initiativeSupportItOptions'],
            'map-technology' => ['mapTechnologies', 'mapTechnologyCoeOptions', 'mapTechnologyInitiativeOptions'],
            'initiative-relation' => ['mstInitiatives', 'initiativeRelations', 'modelRelationOptions', 'typeRelationOptions'],
            'roadmap' => match ($roadmapMode) {
                'digital' => [
                    'digitalRoadmapGroups',
                    'digitalRoadmapTotalCount',
                    'digitalRoadmapStartYear',
                    'digitalRoadmapEndYear',
                ],
                'all' => [
                    'itRoadmapGroups',
                    'itRoadmapStartYear',
                    'itRoadmapEndYear',
                    'itRoadmapTotalCount',
                    'itRoadmapMilestoneTypeOptions',
                    'digitalRoadmapGroups',
                    'digitalRoadmapTotalCount',
                    'digitalRoadmapStartYear',
                    'digitalRoadmapEndYear',
                ],
                default => [
                    'itRoadmapGroups',
                    'itRoadmapStartYear',
                    'itRoadmapEndYear',
                    'itRoadmapTotalCount',
                    'itRoadmapMilestoneTypeOptions',
                ],
            },
            'strategic-pillars' => [
                'strategicPillars',
                'allGoals',
                'taggings',
                'allInitiatives',
                'allThemes',
                'matrixInitiatives',
                'allOrganizations',
                'pilarOptions',
                'pillarFilters',
            ],
            default => [
                'summary',
                'technologyCards',
                'strategyCards',
                'foundationCard',
                'architectureCard',
                'tbcCard',
                'unassignedInitiatives',
                'mappingBusinessStrategyGroups',
                'mappingBusinessStrategyColumns',
                'mappingBusinessStrategyOrganizationOptions',
            ],
        };
    }

    private function getCacheKey(array $filters): string
    {
        // Sort keys to ensure deterministic key regardless of filter order
        ksort($filters);
        $filterString = http_build_query($filters);

        return 'strategic_house_props:' . md5($filterString);
    }
}
