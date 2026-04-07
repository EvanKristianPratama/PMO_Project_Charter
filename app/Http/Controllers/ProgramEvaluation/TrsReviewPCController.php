<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\TrsProject;
use App\Models\TrsReviewPC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrsReviewPCController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trsReviewPCs = TrsReviewPC::with('initiative')->get();

        // dd($trsReviewPCs->toArray());
        return inertia('ProgramEvaluation/ReviewPC/Index', [
            'trsReviewPCs' => $trsReviewPCs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TrsReviewPC $trsReviewPC)
    {
        $trsReviewPC->load([
            'initiative' => fn ($query) => $query
                ->with('organization')
                ->with('initiativeRelationsRow.initiativeRow')
                ->with('initiativeRelationsRow.initiativeColumn')
                ->with('initiativeRelationsColumn.initiativeRow')
                ->with('initiativeRelationsColumn.initiativeColumn'),
        ]);

        $mappedProject = null;
        $mappedProjects = [];
        $initiativeId = $trsReviewPC->initiative_id;

        if ($initiativeId && Schema::hasTable('trs_pc_initiative')) {
            $tableColumns = Schema::getColumnListing('trs_pc_initiative');
            $projectColumn = collect(['project_id', 'trs_project_id', 'pc_id'])->first(
                static fn ($col) => in_array($col, $tableColumns, true)
            );
            $initiativeColumn = collect(['initiative_id', 'mst_initiative_id', 'useCase_id', 'use_case_id'])->first(
                static fn ($col) => in_array($col, $tableColumns, true)
            );

            if ($projectColumn && $initiativeColumn) {
                $query = DB::table('trs_pc_initiative')->where($initiativeColumn, $initiativeId);
                if (in_array('id', $tableColumns, true)) {
                    $query->orderByDesc('id');
                }

                // Ambil semua project_id yang berelasi dengan initiative ini
                $mappedProjectIds = $query->pluck($projectColumn)->filter()->unique()->values();

                if ($mappedProjectIds->isNotEmpty()) {
                    $mappedProjects = TrsProject::query()
                        ->with([
                            'charters' => static fn ($q) => $q
                                ->select('trs_project_charters.id', 'trs_project_charters.project_id', 'trs_project_charters.version_label', 'trs_project_charters.status', 'trs_project_charters.category', 'trs_project_charters.owner', 'trs_project_charters.tgl_dokumen', 'trs_project_charters.duration', 'trs_project_charters.background', 'trs_project_charters.objectives', 'trs_project_charters.scope', 'trs_project_charters.impact_value', 'trs_project_charters.key_personnel', 'trs_project_charters.key_items', 'trs_project_charters.budget', 'trs_project_charters.risks_identified', 'trs_project_charters.risk_mitigation')
                                ->latest()
                                ->with('milestones'),
                            'owner',
                            'statusRef:id,name',
                            'reviewPcStatusImplementations',
                        ])
                        ->whereIn('id', $mappedProjectIds)
                        ->get()
                        ->map(static function (TrsProject $project): TrsProject {
                            $charter = $project->charters->first() ?? null;

                            if ($charter) {
                                $project->setRelation('charter', $charter);
                                $project->setAttribute('milestones', $charter->milestones ?? collect());
                            } else {
                                $project->setAttribute('milestones', collect());
                            }

                            return $project;
                        })
                        ->values()
                        ->all();

                    // Tetap sediakan mappedProject (pertama) untuk backward compatibility
                    $mappedProject = $mappedProjects[0] ?? null;
                }
            }
        }

        $reviewOptions = TrsReviewPC::query()
            ->select(['id', 'initiative_id'])
            ->with(['initiative:id,code,name'])
            ->orderBy('id')
            ->get()
            ->map(static fn (TrsReviewPC $item) => [
                'id' => (int) $item->id,
                'initiative_id' => $item->initiative_id,
                'initiative' => $item->initiative
                    ? [
                        'id' => (int) $item->initiative->id,
                        'code' => $item->initiative->code,
                        'name' => $item->initiative->name,
                    ]
                    : null,
            ])
            ->values();

        return inertia('ProgramEvaluation/ReviewPC/Show', [
            'trsReviewPC' => $trsReviewPC,
            'reviewOptions' => $reviewOptions,
            'mappedProject' => $mappedProject,
            'mappedProjects' => $mappedProjects,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrsReviewPC $trsReviewPC)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrsReviewPC $trsReviewPC)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrsReviewPC $trsReviewPC)
    {
        //
    }
}
