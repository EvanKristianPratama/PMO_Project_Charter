<?php

namespace Modules\ITSP\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsProject;
use App\Models\TrsReviewPC;
use App\Models\TrsOrganization;
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
        $latestReviewsByInitiative = TrsReviewPC::query()
            ->select(['id', 'initiative_id', 'month', 'year', 'kesimpulan'])
            ->orderByDesc('id')
            ->get()
            ->unique('initiative_id')
            ->keyBy(static fn (TrsReviewPC $review) => (int) $review->initiative_id);

        $trsReviewPCs = MstInitiative::query()
            ->select(['id', 'code', 'name', 'description', 'status', 'tipe_initiative', 'coe_id'])
            ->whereHas('mappedProjects')
            ->with([
                'coe' => static fn ($q) => $q->select(['id', 'name', 'tipe']),
                'itBuildingMapping.primaryCoe:id,name,tipe',
                'mappedProjects' => static fn ($query) => $query
                    ->select(['trs_projects.id', 'trs_projects.code', 'trs_projects.name', 'trs_projects.status'])
                    ->with([
                        'projectCharters' => static fn ($charterQuery) => $charterQuery
                            ->orderByDesc('id'),
                        'reviewPcStatusImplementations' => static fn ($reviewQuery) => $reviewQuery
                            ->orderByDesc('year')
                            ->orderByDesc('id'),
                    ])
                    ->orderBy('trs_projects.code')
                    ->orderBy('trs_projects.id'),
            ])
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->map(static function (MstInitiative $initiative, int $index) use ($latestReviewsByInitiative): array {
                $projects = ($initiative->mappedProjects ?? collect())->values();
                $latestReviewStatus = $projects
                    ->flatMap(static fn (TrsProject $project) => $project->reviewPcStatusImplementations ?? collect())
                    ->sortBy([
                        ['year', 'desc'],
                        ['id', 'desc'],
                    ])
                    ->first();

                // Get owner and leader from the latest project charter
                $latestCharter = $projects
                    ->flatMap(fn ($p) => $p->projectCharters ?? collect())
                    ->sortByDesc('id')
                    ->first();

                $projectOwner = $latestCharter?->owner ?? '-';
                $projectLeader = $latestCharter?->leader ?? '-';

                // Logic for IT Building Block (must be CoE tipe 2)
                $coe = $initiative->coe;
                $itBuildingBlock = null;

                if ($coe && $coe->tipe == 2) {
                    $itBuildingBlock = $coe;
                } else {
                    // For Digital Initiatives (tipe 1) or those with non-tipe 2 CoE, fetch from mapping
                    $mapping = $initiative->itBuildingMapping;
                    if ($mapping && $mapping->primaryCoe && $mapping->primaryCoe->tipe == 2) {
                        $itBuildingBlock = $mapping->primaryCoe;
                    }
                }

                $coeName = $itBuildingBlock?->name ?? ($coe?->name ?: 'CoE Not Identified');
                $latestReviewPc = $latestReviewsByInitiative->get((int) $initiative->id);

                return [
                    'id' => $latestReviewPc?->id,
                    'no' => $index + 1,
                    'initiative_id' => (int) $initiative->id,
                    'month' => $latestReviewPc?->month,
                    'year' => $latestReviewPc?->year,
                    'kesimpulan' => $latestReviewPc?->kesimpulan,
                    'initiative' => [
                        'id' => (int) $initiative->id,
                        'code' => $initiative->code,
                        'name' => $initiative->name,
                        'description' => $initiative->description,
                        'status' => $initiative->status,
                        'tipe_initiative' => $initiative->tipe_initiative,
                        'coe_id' => $initiative->coe_id,
                        'coe' => $coe
                            ? [
                                'id' => (int) $coe->id,
                                'name' => $coe->name,
                                'tipe' => $coe->tipe,
                            ]
                            : null,
                        'coe_name' => $coeName,
                        'it_building_block' => $itBuildingBlock
                            ? [
                                'id' => (int) $itBuildingBlock->id,
                                'name' => $itBuildingBlock->name,
                                'tipe' => $itBuildingBlock->tipe,
                            ]
                            : null,
                    ],
                    'projects' => $projects,
                    'project_owner' => $projectOwner,
                    'project_leader' => $projectLeader,
                    'latest_review_status' => $latestReviewStatus?->review_status ?? 'Belum Ada Status',
                    'latest_review_status_implementation' => $latestReviewStatus,
                ];
            })
            ->values();

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
        $validated = $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'month' => 'nullable|string',
            'year' => 'nullable|integer|min:1901|max:2155',
            'kesimpulan' => 'nullable|string',
            'detail_kesimpulan' => 'nullable|string',
            'penjelasan' => 'nullable|string',
            'why' => 'nullable|string',
            'what' => 'nullable|string',
            'how' => 'nullable|string',
            'project_profile' => 'nullable|string',
            'key_milestone' => 'nullable|string',
            'risk_impact' => 'nullable|string',
        ]);

        $trsReviewPC = TrsReviewPC::create($validated);

        return redirect()->route('itsp.program-evaluation.show', $trsReviewPC->id)->with('success', 'Review PC berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrsReviewPC $trsReviewPC)
    {
        $trsReviewPC->load([
            'initiative' => fn ($query) => $query
                ->with([
                    'organization',
                    'coe:id,name,tipe',
                    'itBuildingMapping.primaryCoe:id,name,tipe',
                    'initiativeRelationsRow.initiativeRow',
                    'initiativeRelationsRow.initiativeColumn',
                    'initiativeRelationsColumn.initiativeRow',
                    'initiativeRelationsColumn.initiativeColumn'
                ]),
        ]);

        // Same logic for IT Building Block as index()
        $initiative = $trsReviewPC->initiative;
        $coe = $initiative?->coe;
        $itBuildingBlock = null;

        if ($coe && $coe->tipe == 2) {
            $itBuildingBlock = $coe;
        } else {
            $mapping = $initiative?->itBuildingMapping;
            if ($mapping && $mapping->primaryCoe && $mapping->primaryCoe->tipe == 2) {
                $itBuildingBlock = $mapping->primaryCoe;
            }
        }

        if ($initiative) {
            $initiative->setAttribute('coe_name', $itBuildingBlock?->name ?? ($coe?->name ?: 'CoE Not Identified'));
            $initiative->setAttribute('it_building_block', $itBuildingBlock);
        }

        $initiativeReviews = TrsReviewPC::query()
            ->with([
                'initiative' => fn ($query) => $query
                    ->select(['id', 'code', 'name', 'tipe_initiative', 'coe_id'])
                    ->with([
                        'coe:id,name,tipe',
                        'itBuildingMapping.primaryCoe:id,name,tipe',
                    ])
            ])
            ->where('initiative_id', $trsReviewPC->initiative_id)
            ->get()
            ->map(static function (TrsReviewPC $review) {
                $initiative = $review->initiative;
                $coe = $initiative?->coe;
                $itBuildingBlock = null;

                if ($coe && $coe->tipe == 2) {
                    $itBuildingBlock = $coe;
                } else {
                    $mapping = $initiative?->itBuildingMapping;
                    if ($mapping && $mapping->primaryCoe && $mapping->primaryCoe->tipe == 2) {
                        $itBuildingBlock = $mapping->primaryCoe;
                    }
                }

                if ($initiative) {
                    $initiative->setAttribute('coe_name', $itBuildingBlock?->name ?? ($coe?->name ?: 'CoE Not Identified'));
                    $initiative->setAttribute('it_building_block', $itBuildingBlock);
                }

                $monthName = strtolower(trim((string) $review->month));
                $monthOrder = match ($monthName) {
                    'januari' => 1,
                    'februari' => 2,
                    'maret' => 3,
                    'april' => 4,
                    'mei' => 5,
                    'juni' => 6,
                    'juli' => 7,
                    'agustus' => 8,
                    'september' => 9,
                    'oktober' => 10,
                    'november' => 11,
                    'desember' => 12,
                    default => 0,
                };

                return array_merge(
                    $review->toArray(),
                    ['month_order' => $monthOrder]
                );
            })
            ->sort(function (array $left, array $right) {
                $yearComparison = (int) ($right['year'] ?? 0) <=> (int) ($left['year'] ?? 0);
                if ($yearComparison !== 0) {
                    return $yearComparison;
                }

                $monthComparison = (int) ($right['month_order'] ?? 0) <=> (int) ($left['month_order'] ?? 0);
                if ($monthComparison !== 0) {
                    return $monthComparison;
                }

                return (int) ($right['id'] ?? 0) <=> (int) ($left['id'] ?? 0);
            })
            ->values();

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
                                ->select(
                                    'trs_project_charters.id',
                                    'trs_project_charters.project_id',
                                    'trs_project_charters.version_label',
                                    'trs_project_charters.status',
                                    'trs_project_charters.category',
                                    'trs_project_charters.owner',
                                    'trs_project_charters.tgl_dokumen',
                                    'trs_project_charters.duration',
                                    'trs_project_charters.background',
                                    'trs_project_charters.objectives',
                                    'trs_project_charters.impact_value',
                                    'trs_project_charters.key_personnel',
                                    'trs_project_charters.key_items',
                                    'trs_project_charters.budget',
                                    'trs_project_charters.risks_identified',
                                    'trs_project_charters.risk_mitigation',
                                    'trs_project_charters.sponsor',
                                    'trs_project_charters.leader',
                                    'trs_project_charters.notes',
                                    'trs_project_charters.metadata',
                                    'trs_project_charters.target_kpi'
                                )
                                ->latest()
                                ->with('milestones'),
                            'owner',
                            'statusRef:id,name',
                            'reviewPcStatusImplementations',
                            'mapPicProject',
                            'mapCrossFunctions',
                        ])
                        ->whereIn('id', $mappedProjectIds)
                        ->get()
                        ->map(static function (TrsProject $project): TrsProject {
                            foreach ($project->charters as $charter) {
                                $charter->pic_sponsor_id = $project->mapPicProject?->project_sponsor;
                                $charter->pic_owner_id = $project->mapPicProject?->project_owner;
                                $charter->pic_leader_id = $project->mapPicProject?->project_leader;

                                $crossFunctionStatus = (int) $charter->status === 4 ? 2 : 1;
                                $charter->pic_cross_function_ids = $project->mapCrossFunctions
                                    ? $project->mapCrossFunctions
                                        ->where('status', $crossFunctionStatus)
                                        ->pluck('organization_id')
                                        ->toArray()
                                    : [];
                            }

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
            ->with([
                'initiative' => fn ($query) => $query
                    ->select(['id', 'code', 'name', 'coe_id'])
                    ->with('coe:id,name')
            ])
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
                        'coe_name' => $item->initiative->coe?->name,
                    ]
                    : null,
            ])
            ->values();

        $allOrganizations = TrsOrganization::query()
            ->select(['id', 'jabatan'])
            ->get();

        return inertia('ProgramEvaluation/ReviewPC/Show', [
            'trsReviewPC' => $trsReviewPC,
            'initiativeReviews' => $initiativeReviews,
            'reviewOptions' => $reviewOptions,
            'mappedProject' => $mappedProject,
            'mappedProjects' => $mappedProjects,
            'allOrganizations' => $allOrganizations,
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
        $validated = $request->validate([
            'month' => 'nullable|string',
            'year' => 'nullable|integer|min:1901|max:2155',
            'kesimpulan' => 'nullable|string',
            'detail_kesimpulan' => 'nullable|string',
            'detail_penjelasan' => 'nullable|string',
            'penjelasan' => 'nullable|string',
            'why' => 'nullable|string',
            'what' => 'nullable|string',
            'how' => 'nullable|string',
            'project_profile' => 'nullable|string',
            'key_milestone' => 'nullable|string',
            'risk_impact' => 'nullable|string',
        ]);

        $trsReviewPC->update($validated);

        return back()->with('success', 'Review PC berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrsReviewPC $trsReviewPC)
    {
        //
    }
}
