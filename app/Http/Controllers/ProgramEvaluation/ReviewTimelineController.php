<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsReviewPCStatusImplementation;
use App\Models\TrsProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ReviewTimelineController extends Controller
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
                        'reviewPcStatusImplementations' => static fn ($reviewQuery) => $reviewQuery
                            ->orderByDesc('year')
                            ->orderByDesc('id'),
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

        return inertia('ProgramEvaluation/ReviewTimeline/Index', [
            'initiatives' => $initiatives,
        ]);
    }

    public function storeReviewStatusImplementation(Request $request, TrsProject $project): RedirectResponse
    {
        $payload = $this->validateReviewStatusPayload($request);

        TrsReviewPCStatusImplementation::query()->create([
            'project_id' => $project->id,
            'start' => $payload['start'],
            'end' => $payload['end'] ?? null,
            'year' => $payload['year'] ?? null,
            'review_status' => $payload['review_status'],
            'status' => $payload['status'],
        ]);

        return back()->with('success', 'Review status implementation berhasil ditambahkan.');
    }

    public function updateReviewStatusImplementation(Request $request, int $statusId): RedirectResponse
    {
        $payload = $this->validateReviewStatusPayload($request);

        $statusImplementation = TrsReviewPCStatusImplementation::query()->findOrFail($statusId);
        $statusImplementation->update([
            'start' => $payload['start'],
            'end' => $payload['end'] ?? null,
            'year' => $payload['year'] ?? null,
            'review_status' => $payload['review_status'],
            'status' => $payload['status'],
        ]);

        return back()->with('success', 'Review status implementation berhasil diperbarui.');
    }

    private function validateReviewStatusPayload(Request $request): array
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $request->validate([
            'start' => ['required', 'in:' . implode(',', $months)],
            'end' => ['nullable', 'in:' . implode(',', $months)],
            'year' => ['nullable', 'string', 'max:4'],
            'review_status' => ['required', 'in:On Track,Done,At Risk,Not Started,Not Signed'],
            'status' => ['required', 'string'],
        ]);
    }
}
