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
        $orgId = $request->query('org_id');

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
        $taggings = InitiativeTagging::with([
                'initiative:id,name,code,status,business_unit',
                'initiative.latestStatus',
                'initiative.organization:id,name',
                'initiative.mapSc.ScopeCharter',
                'initiative.mappedProjects:id',
                'theme:id,name,idGoal',
            ])
            ->when($orgId, function ($query) use ($orgId) {
                return $query->whereHas('initiative', function ($q) use ($orgId) {
                    $q->where('business_unit', $orgId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all organizations that have initiatives for the filter dropdown
        $allOrganizations = \App\Models\TrsOrganization::has('initiatives')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // All initiatives for dropdown
        $allInitiatives = MstInitiative::select('id', 'code', 'name', 'business_unit')
            ->with('organization:id,name')
            ->orderBy('code', 'asc')
            ->get()
            ->map(fn ($i) => [
                'id'           => $i->id,
                'code'         => $i->code,
                'name'         => $i->name,
                'organization' => $i->organization ? ['id' => $i->organization->id, 'name' => $i->organization->name] : null,
            ])
            ->values();

        // All themes for dropdown
        $allThemes = Theme::with('goal:id,code,title')
            ->select('id', 'name', 'theme_number', 'idGoal')
            ->orderBy('theme_number', 'asc')
            ->get();

        return Inertia::render('StrategicPillar/Index', [
            'strategicPillars' => $strategicPillars,
            'allGoals' => $allGoals,
            'allOrganizations' => $allOrganizations,
            'taggings' => $taggings,
            'allInitiatives' => $allInitiatives,
            'allThemes' => $allThemes,
            'filters' => [
                'goal_id' => $goal,
                'org_id' => $orgId,
            ],
        ]);
    }
}