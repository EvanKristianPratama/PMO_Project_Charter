<?php

namespace App\Http\Controllers\ProgramPlanning\BusinessStrategy;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\MstBusinessStrategy;
use App\Models\MstMisiBumn;
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

        return Inertia::render('ProgramPlanning/BusinessStrategy/Index', [
            'goals' => $goals,
            'strategies' => $strategies,
            'misiBumn' => $misiBumn,
        ]);
    }
}
