<?php

namespace App\Http\Controllers\StrategicPillar;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\MstInitiative;
use App\Models\Theme;
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

        // Initiative Tagging data
        $taggings = InitiativeTagging::with(['initiative:id,name,code,status', 'initiative.latestStatus', 'theme:id,name,idGoal'])
            ->orderBy('created_at', 'desc')
            ->get();

        // All initiatives for dropdown
        $allInitiatives = MstInitiative::select('id', 'code', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // All themes for dropdown
        $allThemes = Theme::with('goal:id,code,title')
            ->select('id', 'name', 'theme_number', 'idGoal')
            ->orderBy('theme_number', 'asc')
            ->get();

        return Inertia::render('StrategicPillar/Index', [
            'strategicPillars' => $strategicPillars,
            'allGoals' => $allGoals,
            'taggings' => $taggings,
            'allInitiatives' => $allInitiatives,
            'allThemes' => $allThemes,
            'filters' => [
                'goal_id' => $goal,
            ],
        ]);
    }
}