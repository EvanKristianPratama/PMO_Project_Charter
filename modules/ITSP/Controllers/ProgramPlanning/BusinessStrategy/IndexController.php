<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\BusinessStrategy;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\MstBusinessStrategy;
use App\Models\MstMisiBumn;
use App\Models\MstPriorityStrategicInitiative;
use App\Models\MstStrategicHouse;
use App\Models\Theme;
use App\Models\TrsPillarStrategy;
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

        $misiBumn = MstMisiBumn::with('prioritasStrategy')->orderBy('code')->get();
        
        $priorityStrategicInitiatives = MstPriorityStrategicInitiative::with('mapPriorityStrategicInitiative.picPriorityStrategic')->orderBy('no')->get();

        $strategicHousePertamina = MstStrategicHouse::where('id', 4)->first();
        $pertaminaGoals = Goal::where('pilar', 4)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $strategicHouseUpstream = MstStrategicHouse::where('id', 5)->first();
        $upstreamGoals = Goal::where('pilar', 5)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $strategicHouseGas = MstStrategicHouse::where('id', 6)->first();
        $gasGoals = Goal::where('pilar', 6)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $businessStrategyService = app(BusinessStrategyService::class);
        $dualGrowthProps = $businessStrategyService->getPageProps(collect());

        $strategicHouseKPI = MstStrategicHouse::where('id', 7)->first();
        
        $strategyThemes = Theme::where('idGoal', 24)->orderBy('theme_number')->get();
        $perspectiveThemes = Theme::where('idGoal', 25)->orderBy('theme_number')->get();
        $additionalThemes = Theme::where('idGoal', 26)->orderBy('theme_number')->get();
        
        $pillarStrategies = TrsPillarStrategy::whereIn('themes_id', [70, 71, 72, 73])
            ->orderBy('themes_id')
            ->get();

        $strategicHouseCT = MstStrategicHouse::where('id', 8)->first();
        $ctGoals = Goal::where('pilar', 8)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $strategicHouseIML = MstStrategicHouse::where('id', 9)->first();
        $imlGoals = Goal::where('pilar', 9)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->get();

        $strategicHousePNRE = MstStrategicHouse::where('id', 10)->first();
        $pnreGoals = Goal::where('pilar', 10)->with(['themes' => function($query) {
            $query->orderBy('theme_number');
        }, 'themes.pillarThemes'])->orderBy('code')->get();

        return Inertia::render('modules/ITSP/ProgramPlanning/BusinessStrategy/Index', [
            'goals' => $goals,
            'strategies' => $strategies,
            'misiBumn' => $misiBumn,
            'dualGrowthProps' => $dualGrowthProps,
            'priorityStrategicInitiatives' => $priorityStrategicInitiatives,
            'strategicHousePertamina' => $strategicHousePertamina,
            'pertaminaGoals' => $pertaminaGoals,
            'strategicHouseUpstream' => $strategicHouseUpstream,
            'upstreamGoals' => $upstreamGoals,
            'strategicHouseGas' => $strategicHouseGas,
            'gasGoals' => $gasGoals,
            'strategicHouseCT' => $strategicHouseCT,
            'ctGoals' => $ctGoals,
            'strategicHouseKPI' => $strategicHouseKPI,
            'strategyThemes' => $strategyThemes,
            'perspectiveThemes' => $perspectiveThemes,
            'pillarStrategies' => $pillarStrategies,
            'additionalThemes' => $additionalThemes,
            'strategicHouseIML' => $strategicHouseIML,
            'imlGoals' => $imlGoals,
            'strategicHousePNRE' => $strategicHousePNRE,
            'pnreGoals' => $pnreGoals,
        ]);
    }
}
