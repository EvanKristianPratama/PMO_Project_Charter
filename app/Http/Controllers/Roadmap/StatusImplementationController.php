<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsProject;
use Inertia\Response;

class StatusImplementationController extends Controller
{
    public function index(): Response
    {
        $initiatives = MstInitiative::query()
            ->select(['id', 'code', 'name', 'description', 'status', 'tipe_initiative'])
            ->whereHas('mappedProjects')
            ->with([
                'mappedProjects' => static fn ($query) => $query
                    ->select(['trs_projects.id', 'trs_projects.code', 'trs_projects.name', 'trs_projects.status'])
                    ->with([
                        'owner:id,name',
                        'statusRef:id,name',
                        'pcStatusImplementations',
                        'charter' => static fn ($charterQuery) => $charterQuery
                            ->select([
                                'trs_project_charters.id',
                                'trs_project_charters.project_id',
                                'trs_project_charters.version_label',
                                'trs_project_charters.status',
                                'trs_project_charters.category',
                                'trs_project_charters.owner',
                                'trs_project_charters.tgl_dokumen',
                                'trs_project_charters.duration',
                                'trs_project_charters.objectives',
                            ])
                            ->with([
                                'milestones' => static fn ($milestoneQuery) => $milestoneQuery
                                    ->select([
                                        'trs_milestones.id',
                                        'trs_milestones.pc_id',
                                        'trs_milestones.version',
                                        'trs_milestones.title',
                                        'trs_milestones.output',
                                        'trs_milestones.start_date',
                                        'trs_milestones.end_date',
                                        'trs_milestones.type',
                                        'trs_milestones.milestone_type',
                                        'trs_milestones.order',
                                    ])
                                    ->orderBy('trs_milestones.order')
                                    ->orderBy('trs_milestones.id'),
                            ]),
                        'charters' => static fn ($chartersQuery) => $chartersQuery
                            ->select([
                                'trs_project_charters.id',
                                'trs_project_charters.project_id',
                                'trs_project_charters.version_label',
                                'trs_project_charters.status',
                                'trs_project_charters.category',
                                'trs_project_charters.owner',
                                'trs_project_charters.tgl_dokumen',
                                'trs_project_charters.duration',
                                'trs_project_charters.objectives',
                            ])
                            ->with([
                                'milestones' => static fn ($milestoneQuery) => $milestoneQuery
                                    ->select([
                                        'trs_milestones.id',
                                        'trs_milestones.pc_id',
                                        'trs_milestones.version',
                                        'trs_milestones.title',
                                        'trs_milestones.output',
                                        'trs_milestones.start_date',
                                        'trs_milestones.end_date',
                                        'trs_milestones.type',
                                        'trs_milestones.milestone_type',
                                        'trs_milestones.order',
                                    ])
                                    ->orderBy('trs_milestones.order')
                                    ->orderBy('trs_milestones.id'),
                            ])
                            ->latest(),
                    ])
                    ->orderBy('trs_projects.code')
                    ->orderBy('trs_projects.id'),
            ])
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->map(static function (MstInitiative $initiative): array {
                $projects = ($initiative->mappedProjects ?? collect())
                    ->map(static function (TrsProject $project): TrsProject {
                        $charters = collect($project->charters ?? [])->sortByDesc('id')->values();
                        $latestCharter = $charters->first() ?? $project->charter;

                        if ($latestCharter) {
                            $project->setRelation('charter', $latestCharter);
                        }

                        return $project;
                    })
                    ->values();

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'status' => $initiative->status,
                    'tipe_initiative' => $initiative->tipe_initiative,
                    'projects' => $projects,
                ];
            })
            ->values();

        return inertia('ProgramImplementation/RoadMap/StatusImplementation', [
            'initiatives' => $initiatives,
        ]);
    }
}
