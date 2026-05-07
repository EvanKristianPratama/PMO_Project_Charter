<?php

namespace App\Http\Controllers\ProgramPlanning\BusinessStrategy;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\MstBusinessStrategy;
use App\Models\MstMisiBumn;
use App\Models\MstPriorityStrategicInitiative;
use App\Models\MstStrategicHouse;
use App\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke()
    {
        // Get goals for pillar = 3
        $goals = Goal::where('pilar', 3)->orderBy('code')->get();

        $strategies = MstBusinessStrategy::whereIn('goal_id', $goals->pluck('id'))
            ->orderBy('code')
            ->get()
            ->groupBy('goal_id')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'strategy' => $item->strategy,
                        'goal_id' => $item->goal_id,
                    ];
                })->values();
            });

        $misiBumn = MstMisiBumn::with('prioritasStrategy')->orderBy('code')->get()->keyBy('id');
        
        $priorityStrategicInitiatives = MstPriorityStrategicInitiative::with('mapPriorityStrategicInitiative.picPrioriyStrategic')->orderBy('no')->get();

        $strategicHousePertamina = MstStrategicHouse::where('id', 4)->first();
        $pertaminaGoals = Goal::where('pilar', 4)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $strategicHouseUpstream = MstStrategicHouse::where('id', 5)->first();
        $upstreamGoals = Goal::where('pilar', 5)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $businessStrategyService = app(BusinessStrategyService::class);
        $dualGrowthProps = $businessStrategyService->getPageProps(collect());

        return Inertia::render('ProgramPlanning/BusinessStrategy/Index', [
            'goals' => $goals,
            'strategies' => $strategies,
            'misiBumn' => $misiBumn,
            'dualGrowthProps' => $dualGrowthProps,
            'priorityStrategicInitiatives' => $priorityStrategicInitiatives,
            'strategicHousePertamina' => $strategicHousePertamina,
            'pertaminaGoals' => $pertaminaGoals,
            'strategicHouseUpstream' => $strategicHouseUpstream,
            'upstreamGoals' => $upstreamGoals,
        ]);
    }
}
