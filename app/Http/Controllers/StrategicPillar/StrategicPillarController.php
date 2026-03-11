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

        return Inertia::render('StrategicPillar/Index', [
            // --- Filter-dependent (selalu dihitung ulang) ---
            'strategicPillars' => fn () => Goal::with(['themes' => fn ($q) => $q->orderBy('theme_number', 'asc')])
                ->orderBy('code', 'asc')
                ->get(),

            'taggings' => fn () => InitiativeTagging::with([
                    'initiative:id,name,code,status,business_unit',
                    'initiative.latestStatus',
                    'initiative.organization:id,name',
                    'initiative.mapSc:id,sc_id,initiative_id',
                    'initiative.mappedProjects:id',
                    'theme:id,name,idGoal',
                ])
                ->orderBy('created_at', 'desc')
                ->get(),

            'filters' => [
                'goal_id' => $goal,
                'org_id'  => $orgId,
            ],

            // --- Data statis untuk dropdown (tidak dihitung ulang saat partial reload) ---
            'allGoals'         => fn () => Goal::select('id', 'code', 'title')->orderBy('code')->get(),
            'allOrganizations' => fn () => \App\Models\TrsOrganization::has('initiatives')->select('id', 'name')->orderBy('name')->get(),
            'allInitiatives'   => fn () => MstInitiative::select('id', 'code', 'name', 'business_unit')
                ->with('organization:id,name')
                ->orderBy('code')
                ->get()
                ->map(fn ($i) => [
                    'id'           => $i->id,
                    'code'         => $i->code,
                    'name'         => $i->name,
                    'organization' => $i->organization ? ['id' => $i->organization->id, 'name' => $i->organization->name] : null,
                ])
                ->values(),
            'allThemes' => fn () => Theme::with('goal:id,code,title')->select('id', 'name', 'theme_number', 'idGoal')->orderBy('theme_number')->get(),
        ]);
    }
}