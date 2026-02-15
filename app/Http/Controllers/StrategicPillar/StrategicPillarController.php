<?php

namespace App\Http\Controllers\StrategicPillar;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class StrategicPillarController extends Controller
{
    public function index(Request $request, $goal = null)
    {
        $strategicPillars = Goal::with(['themes' => function ($query) {
                $query->orderBy('theme_number', 'asc');
            }])
            ->when($goal, function ($query) use ($goal) {
                return $query->where('id', $goal);
            })
            ->orderBy('code', 'asc')
            ->get();

        // Ambil semua goals untuk dropdown filter
        $allGoals = Goal::select('id', 'code', 'title')
            ->orderBy('code', 'asc')
            ->get();

        return Inertia::render('StrategicPillar/Index', [
            'strategicPillars' => $strategicPillars,
            'allGoals' => $allGoals,
            'filters' => [
                'goal_id' => $goal,
            ],
        ]);
    }
}