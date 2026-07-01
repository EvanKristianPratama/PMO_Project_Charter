<?php

namespace App\Services\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Models\MstInitiative;
use Modules\ITSP\Models\TrsPcStatusImplementation;
use App\Models\ProjectStatusHistory;
use Modules\ITSP\Models\TrsProject;
use App\Services\ProgramImplementation\ProjectCharter\ProjectCharterStatusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Modules\ITSP\Models\TrsReviewPc;

class ITInitiativeService
{
    public function __construct(
        private readonly ProjectCharterStatusService $projectCharterStatusService
    ) {}

    public function getIndexProps(): array
    {
        $masterItInitiatives = $this->masterItInitiatives();
        $statusCounts = $this->projectCharterStatusService->buildStatusCounts($masterItInitiatives);

        return [
            'itInitiatives' => $this->itProjects(),
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'completedStatusId' => $this->projectCharterStatusService->getCompletedStatusId(),
            'totalItInitiatives' => $masterItInitiatives->count(),
            'totalItApproved' => (int) ($statusCounts['approved'] ?? 0),
            'masterItInitiatives' => $masterItInitiatives,
            'statusCounts' => $statusCounts,
        ];
    }

    public function getCreateProps(): array
    {
        return [
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'defaultStatusId' => $this->projectCharterStatusService->getDefaultStatusId(),
            'planningItDefinitions' => $this->planningItDefinitions(),
        ];
    }

    public function createItInitiative(array $payload): TrsProject
    {
        $ownerName = $this->resolveOwnerName($payload);
        $initiativeIds = $this->normalizedInitiativeIds($payload['initiative_ids'] ?? []);

        $project = TrsProject::query()->create([
            'code' => trim((string) $payload['code']),
            'name' => trim((string) $payload['name']),
            'owner_name' => $ownerName,
            'status' => (int) $payload['status'],
            'tipe_inisiative' => 2,
        ]);

        $this->syncProjectInitiativeMappings($project, $initiativeIds);

        $charterPayload = ['version_label' => 'v1'];

        if ($ownerName !== null) {
            $charterPayload['owner'] = $ownerName;
        }

        if (filled($payload['charter_category'] ?? null)) {
            $charterPayload['category'] = trim((string) $payload['charter_category']);
        }

        $project->charters()->create($charterPayload);

        $this->projectCharterStatusService->recordProjectStatusHistory(
            $project->fresh(),
            null,
            $project->status,
            $payload['project_status_changed_at'] ?? null,
            $payload['project_status_notes'] ?? null,
        );

        return $project->fresh();
    }

    public function getShowProps(TrsProject $project): array
    {
        $project = TrsProject::query()
            ->with([
                'charter',
                'charter.milestones',
                'mapPicProject.sponsorOrganization',
                'mapPicProject.ownerOrganization',
                'mapPicProject.leaderOrganization',
                'mapCrossFunctions.organization',
                'charters' => static fn ($query) => $query->latest('id')->with('milestones'),
                'versionAnalysis',
                'owner',
                'statusRef:id,name',
                'pcStatusImplementations',
                'mappedInitiatives:id,code,name',
            ])
            ->findOrFail($project->id);

        $initiativeIds = $project->mappedInitiatives->pluck('id');

        $review = TrsReviewPc::query()
            ->whereIn('initiative_id', $initiativeIds)
            ->latest('id')
            ->first();

        $allOrganizations = \App\Models\TrsOrganization::orderBy('name')->get();

        $relatedProjects = TrsProject::query()
            ->whereIn('id', function ($query) use ($initiativeIds) {
                $query->select('pc_id')
                    ->from('trs_pc_initiative')
                    ->whereIn('initiative_id', $initiativeIds);
            })
            ->with([
                'charter' => static fn ($query) => $query->select([
                    'trs_project_charters.id',
                    'trs_project_charters.project_id',
                    'trs_project_charters.category',
                    'trs_project_charters.status',
                    'trs_project_charters.tgl_dokumen',
                    'trs_project_charters.duration',
                ]),
                'charters' => static fn ($query) => $query
                    ->select([
                        'trs_project_charters.id',
                        'trs_project_charters.project_id',
                        'trs_project_charters.version_label',
                        'trs_project_charters.category',
                        'trs_project_charters.status',
                        'trs_project_charters.tgl_dokumen',
                        'trs_project_charters.duration',
                    ])
                    ->latest('id'),
                'statusRef:id,name',
                'pcStatusImplementations' => static fn ($query) => $query->orderBy('year', 'asc')->orderBy('id', 'asc'),
            ])
            ->orderBy('code')
            ->get();

        $projectOptions = TrsProject::query()
            ->select(['id', 'code', 'name'])
            ->with(['charter'])
            ->orderBy('id')
            ->get()
            ->map(static fn (TrsProject $item): array => [
                'id' => (int) $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->charter?->category,
            ])
            ->values();

        return [
            'itInitiative' => $project,
            'relatedProjects' => $relatedProjects,
            'projectOptions' => $projectOptions,
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'review' => $review,
            'allOrganizations' => $allOrganizations,
        ];
    }

    public function getEditProps(TrsProject $project): array
    {
        $relations = [
            'pcStatusImplementations',
            'charter',
        ];

        if (
            Schema::hasTable('trs_project_status_history')
            && Schema::hasColumn('trs_project_status_history', 'project_charter_id')
        ) {
            $relations[] = 'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen';
        }

        $project->load($relations);

        return [
            'itInitiative' => $project,
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'defaultStatusId' => $this->projectCharterStatusService->getDefaultStatusId(),
            'planningItDefinitions' => $this->planningItDefinitions(),
            'mappedInitiativeIds' => $this->mappedInitiativeIds($project),
        ];
    }

    public function updateItInitiative(TrsProject $project, array $payload): void
    {
        $oldStatus = $this->projectCharterStatusService->resolveProjectStatusId($project);
        $ownerName = $this->resolveOwnerName($payload);

        $project->update([
            'code' => trim((string) $payload['code']),
            'name' => trim((string) $payload['name']),
            'owner_name' => $ownerName,
            'status' => (int) $payload['status'],
        ]);

        $charterPayload = [];

        if (filled($payload['charter_category'] ?? null)) {
            $charterPayload['category'] = trim((string) $payload['charter_category']);
        }

        if ($ownerName !== null) {
            $charterPayload['owner'] = $ownerName;
        }

        if ($charterPayload !== []) {
            $latestCharter = $project->charters()->latest('id')->first();

            if ($latestCharter) {
                $latestCharter->update($charterPayload);
            } else {
                $project->charters()->create($charterPayload);
            }
        }

        $this->projectCharterStatusService->recordProjectStatusHistory(
            $project->fresh(),
            $oldStatus,
            $project->status,
            $payload['project_status_changed_at'] ?? null,
            $payload['project_status_notes'] ?? null,
        );
    }

    public function deleteItInitiative(TrsProject $project): void
    {
        $project->delete();
    }

    public function storeImplementationStatus(TrsProject $project, array $payload): void
    {
        TrsPcStatusImplementation::query()->create([
            'project_id' => $project->id,
            ...$this->normalizeImplementationStatusPayload($payload),
        ]);
    }

    public function updateImplementationStatus(int $statusId, array $payload): void
    {
        $implementationStatus = TrsPcStatusImplementation::query()->findOrFail($statusId);

        $implementationStatus->update($this->normalizeImplementationStatusPayload($payload));
    }

    public function deleteImplementationStatus(int $statusId): void
    {
        TrsPcStatusImplementation::query()->findOrFail($statusId)->delete();
    }

    public function updateProjectStatusHistory(TrsProject $project, ProjectStatusHistory $history, array $payload): void
    {
        $this->projectCharterStatusService->updateProjectStatusHistory($project, $history, $payload);
    }

    public function deleteProjectStatusHistory(TrsProject $project, ProjectStatusHistory $history): void
    {
        $this->projectCharterStatusService->deleteProjectStatusHistory($project, $history);
    }

    public function updateMapping(TrsProject $project, array $payload): void
    {
        $initiativeIds = $this->normalizedInitiativeIds($payload['initiative_ids'] ?? []);

        $this->syncProjectInitiativeMappings($project, $initiativeIds);
    }

    private function itProjects()
    {
        $projectCharterColumns = $this->projectCharterColumns();
        $milestoneColumns = $this->milestoneColumns();

        return TrsProject::query()
            ->with([
                'owner',
                'charter' => fn ($charterQuery) => $charterQuery
                    ->select($projectCharterColumns)
                    ->with([
                        'milestones' => fn ($milestoneQuery) => $milestoneQuery
                            ->select($milestoneColumns)
                            ->orderBy('trs_milestones.order')
                            ->orderBy('trs_milestones.id'),
                    ]),
                'charter.milestones',
                'charters' => fn ($query) => $query
                    ->select($projectCharterColumns)
                    ->with([
                        'milestones' => fn ($milestoneQuery) => $milestoneQuery
                            ->select($milestoneColumns)
                            ->orderBy('trs_milestones.order')
                            ->orderBy('trs_milestones.id'),
                    ])
                    ->latest('id'),
                'statusRef:id,name',
                'pcStatusImplementations' => fn ($query) => $query->orderBy('year', 'asc')->orderBy('id', 'asc'),
                'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen',
            ])
            ->where('tipe_inisiative', 2)
            ->orderBy('id')
            ->get()
            ->values();
    }

    private function masterItInitiatives()
    {
        $masterSelectColumns = [
            'id',
            'coe_id',
            'tipe_initiative',
            'business_unit',
            'code',
            'name',
            'description',
            'status',
        ];

        if (Schema::hasColumn('mst_initiative', 'project_id')) {
            $masterSelectColumns[] = 'project_id';
        }

        $projectCharterColumns = $this->projectCharterColumns();
        $milestoneColumns = $this->milestoneColumns();

        return MstInitiative::query()
            ->select($masterSelectColumns)
            ->whereHas('mappedProjects')
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
                'mappedProjects' => fn ($query) => $query
                    ->select(['trs_projects.id', 'trs_projects.code', 'trs_projects.name', 'trs_projects.status'])
                    ->with([
                        'owner:id,name',
                        'statusRef:id,name',
                        'pcStatusImplementations' => fn ($query) => $query->orderBy('year', 'asc')->orderBy('id', 'asc'),
                        'charter' => fn ($charterQuery) => $charterQuery->select($projectCharterColumns),
                        'charters' => fn ($chartersQuery) => $chartersQuery
                            ->select($projectCharterColumns)
                            ->with([
                                'milestones' => fn ($milestoneQuery) => $milestoneQuery
                                    ->select($milestoneColumns)
                                    ->orderBy('trs_milestones.order')
                                    ->orderBy('trs_milestones.id'),
                            ])
                            ->latest('id'),
                    ]),
            ])
            ->where('tipe_initiative', 2)
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->map(static function (MstInitiative $initiative): MstInitiative {
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

                $initiative->setRelation('mappedProjects', $projects);
                $initiative->setRelation('projects', $projects);

                return $initiative;
            })
            ->values();
    }

    private function planningItDefinitions()
    {
        return MstInitiative::query()
            ->select(['id', 'code', 'name', 'description', 'status'])
            ->where('tipe_initiative', 2)
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->values();
    }

    private function mappedInitiativeIds(TrsProject $project): array
    {
        if (! Schema::hasTable('trs_pc_initiative')) {
            return [];
        }

        $tableColumns = Schema::getColumnListing('trs_pc_initiative');
        $projectColumn = collect(['project_id', 'trs_project_id', 'pc_id'])->first(
            static fn ($column) => in_array($column, $tableColumns, true)
        );
        $initiativeColumn = collect(['initiative_id', 'mst_initiative_id', 'useCase_id', 'use_case_id'])->first(
            static fn ($column) => in_array($column, $tableColumns, true)
        );

        if (! $projectColumn || ! $initiativeColumn) {
            return [];
        }

        return DB::table('trs_pc_initiative')
            ->where($projectColumn, $project->id)
            ->pluck($initiativeColumn)
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function normalizedInitiativeIds(array $initiativeIds): array
    {
        return collect($initiativeIds)
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function resolveOwnerName(array $payload): ?string
    {
        $ownerName = trim((string) ($payload['owner_name'] ?? ''));

        if ($ownerName !== '') {
            return $ownerName;
        }

        $owner = trim((string) ($payload['owner'] ?? ''));

        return $owner !== '' ? $owner : null;
    }

    private function syncProjectInitiativeMappings(TrsProject $project, array $initiativeIds): void
    {
        if (! Schema::hasTable('trs_pc_initiative')) {
            return;
        }

        $tableColumns = Schema::getColumnListing('trs_pc_initiative');
        $projectColumn = collect(['project_id', 'trs_project_id', 'pc_id'])->first(
            static fn ($column) => in_array($column, $tableColumns, true)
        );
        $initiativeColumn = collect(['initiative_id', 'mst_initiative_id', 'useCase_id', 'use_case_id'])->first(
            static fn ($column) => in_array($column, $tableColumns, true)
        );

        if ($projectColumn === null || $initiativeColumn === null) {
            return;
        }

        $currentInitiativeIds = DB::table('trs_pc_initiative')
            ->where($projectColumn, $project->id)
            ->pluck($initiativeColumn)
            ->map(static fn ($initiativeId) => (int) $initiativeId)
            ->values();

        DB::table('trs_pc_initiative')
            ->where($projectColumn, $project->id)
            ->delete();

        $hasCreatedAt = in_array('created_at', $tableColumns, true);
        $hasUpdatedAt = in_array('updated_at', $tableColumns, true);
        $now = now();

        $rows = collect($initiativeIds)
            ->map(static function (int $initiativeId) use (
                $project,
                $projectColumn,
                $initiativeColumn,
                $hasCreatedAt,
                $hasUpdatedAt,
                $now
            ): array {
                $row = [
                    $projectColumn => $project->id,
                    $initiativeColumn => $initiativeId,
                ];

                if ($hasCreatedAt) {
                    $row['created_at'] = $now;
                }

                if ($hasUpdatedAt) {
                    $row['updated_at'] = $now;
                }

                return $row;
            })
            ->values()
            ->all();

        if ($rows !== []) {
            DB::table('trs_pc_initiative')->insert($rows);
        }

        if (Schema::hasColumn('mst_initiative', 'project_id')) {
            $removedInitiativeIds = $currentInitiativeIds
                ->diff($initiativeIds)
                ->values()
                ->all();

            if ($removedInitiativeIds !== []) {
                MstInitiative::query()
                    ->whereIn('id', $removedInitiativeIds)
                    ->where('project_id', $project->id)
                    ->update(['project_id' => null]);
            }

            if ($initiativeIds === []) {
                return;
            }

            MstInitiative::query()
                ->whereIn('id', $initiativeIds)
                ->update(['project_id' => $project->id]);
        }
    }

    private function projectCharterColumns(): array
    {
        return [
            'trs_project_charters.id',
            'trs_project_charters.project_id',
            'trs_project_charters.version_label',
            'trs_project_charters.status',
            'trs_project_charters.category',
            'trs_project_charters.owner',
            'trs_project_charters.tgl_dokumen',
            'trs_project_charters.duration',
            'trs_project_charters.objectives',
            'trs_project_charters.background',
            'trs_project_charters.impact_value',
            'trs_project_charters.key_personnel',
            'trs_project_charters.key_items',
            'trs_project_charters.budget',
            'trs_project_charters.risks_identified',
            'trs_project_charters.risk_mitigation',
        ];
    }

    private function normalizeImplementationStatusPayload(array $payload): array
    {
        $target = $payload['target'] ?? '';
        $progress = $payload['progress'] ?? '';
        $year = $payload['year'] ?? '';
        $month = trim((string) ($payload['month'] ?? ''));
        $status = trim((string) ($payload['status'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        return [
            'target' => $target !== '' ? $target : null,
            'progress' => $progress !== '' ? $progress : null,
            'month' => $month !== '' ? $month : null,
            'year' => $year !== '' ? $year : null,
            'status' => $status !== '' ? $status : null,
            'description' => $description !== '' ? $description : null,
        ];
    }

    private function milestoneColumns(): array
    {
        return [
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
        ];
    }
}
