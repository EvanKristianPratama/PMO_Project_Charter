<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\ITInitiativeIndexRequest;
use App\Http\Requests\ITInitiative\ITInitiativeStoreRequest;
use App\Http\Requests\ITInitiative\ITInitiativeUpdateRequest;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ITInitiativeController extends Controller
{
    public function roadmapIndex(Request $request): Response
    {
        $selectedProjectId = $request->integer('project_id');

        $roadmapScope = static function ($query): void {
            $query->where(function ($builder): void {
                $builder
                    ->whereHas('charter', static function ($charter): void {
                        $charter
                            ->whereNotNull('duration')
                            ->where('duration', '!=', '');
                    })
                    ->orWhereHas('charter.milestones', static function ($milestones): void {
                        $milestones
                            ->whereNotNull('start_date')
                            ->orWhereNotNull('end_date');
                    });
            });
        };

        $projects = TrsProject::query()
            ->select(['id', 'name', 'code'])
            ->where('tipe_inisiative', 2)
            ->where($roadmapScope)
            ->orderBy('name')
            ->get();

        $resolvedProjectId = $projects->contains('id', $selectedProjectId)
            ? $selectedProjectId
            : $projects->first()?->id;

        $selectedProject = $resolvedProjectId
            ? TrsProject::query()
                ->with(['charter', 'charter.milestones', 'owner'])
                ->find($resolvedProjectId)
            : null;

        return Inertia::render('ProgramImplementation/RoadMap/Index', [
            'itInitiatives' => $projects,
            'selectedProject' => $selectedProject,
            'selectedProjectId' => $resolvedProjectId,
        ]);
    }

    public function index(ITInitiativeIndexRequest $request): Response
    {
        $filters = $request->validated();
        $statusOptions = InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values();

        $baselineStatus = $statusOptions->firstWhere('name', 'baseline');
        $baselineStatusId = (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());
        $filterStatus = array_key_exists('status', $filters) && $filters['status'] !== null
            ? (int) $filters['status']
            : null;

        $projects = TrsProject::query()
            ->with([
                'owner',
                'charter',
                'charter.milestones',
                'charters' => static fn ($query) => $query->latest('id')->with('milestones'),
                'statusRef:id,name',
                'pcStatusImplementations',
            ])
            ->where('tipe_inisiative', 2)
            ->when($filterStatus, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('charter', fn ($charter) => $charter->where('owner', 'like', "%{$search}%"));
            }))
            ->when($filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('updated_at', $month))
            ->orderBy('id', 'asc')
            ->get();

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

        $masterItInitiatives = MstInitiative::query()
            ->select($masterSelectColumns)
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
            ])
            ->where('tipe_initiative', 2)
            ->orderBy('code')
            ->get()
            ->values();

        $totalItInitiatives = $masterItInitiatives->count();

        // Build statusCounts from mst_initiative + latestStatus (name-based keys)
        $aliasMap = [
            'draft'   => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = [];
        foreach ($masterItInitiatives as $initiative) {
            $raw       = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }
        $totalItApproved = (int) ($statusCounts['approved'] ?? 0);

        return Inertia::render('ProgramImplementation/ProjectCharter/ITInitiatives/Index', [
            'itInitiatives' => $projects,
            'statusOptions' => $statusOptions,
            'completedStatusId' => $baselineStatusId,
            'totalItInitiatives' => $totalItInitiatives,
            'totalItApproved' => $totalItApproved,
            'masterItInitiatives' => $masterItInitiatives,
            'statusCounts' => $statusCounts,
            'filters'  => [
                'search' => $filters['search'] ?? null,
                'status' => $filterStatus,
                'month' => $filters['month'] ?? null,
            ],
        ]);
    }

    public function create(): Response
    {
        $planningItDefinitions = MstInitiative::query()
            ->select(['id', 'code', 'name', 'description', 'status'])
            ->where('tipe_initiative', 2)
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->values();

        return Inertia::render('ProgramImplementation/ProjectCharter/ITInitiatives/Create', [
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
            'planningItDefinitions' => $planningItDefinitions,
        ]);
    }

    public function store(ITInitiativeStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $owner = trim((string) ($validated['owner'] ?? ''));
        if ($owner === '') {
            $owner = trim((string) ($validated['owner_name'] ?? ''));
        }

        $initiativeIds = collect($validated['initiative_ids'] ?? [])
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        unset($validated['initiative_ids']);
        unset($validated['owner'], $validated['owner_name']);

        $project = TrsProject::create($validated);
        $this->syncProjectInitiativeMappings($project, $initiativeIds);

        if ($owner !== '') {
            $project->charters()->create([
                'owner' => $owner,
                'version_label' => 'v1',
            ]);
        }

        return redirect()->route('it-initiatives.index')->with('success', 'Project created successfully.');
    }

    private function syncProjectInitiativeMappings(TrsProject $project, array $initiativeIds): void
    {
        if (empty($initiativeIds) || !Schema::hasTable('trs_pc_initiative')) {
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

        DB::table('trs_pc_initiative')
            ->where($projectColumn, $project->id)
            ->delete();

        $hasCreatedAt = in_array('created_at', $tableColumns, true);
        $hasUpdatedAt = in_array('updated_at', $tableColumns, true);
        $now = now();

        $rows = collect($initiativeIds)
            ->map(static function (int $initiativeId) use ($project, $projectColumn, $initiativeColumn, $hasCreatedAt, $hasUpdatedAt, $now): array {
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

        if (!empty($rows)) {
            DB::table('trs_pc_initiative')->insert($rows);
        }

        if (Schema::hasColumn('mst_initiative', 'project_id')) {
            MstInitiative::query()
                ->whereIn('id', $initiativeIds)
                ->update(['project_id' => $project->id]);
        }
    }

    public function show(TrsProject $project): Response
    {
        $project = TrsProject::query()
            ->with([
                'charter',
                'charter.milestones',
                'charters' => static fn ($query) => $query->latest()->with('milestones'),
                'owner',
                'statusRef:id,name',
                'pcStatusImplementations',
                'mappedInitiatives:id,code,name',
            ])
            ->findOrFail($project->id);

        // Get all related projects through mapped initiatives
        $relatedProjects = TrsProject::query()
            ->whereIn('id', function ($query) use ($project) {
                $query->select('pc_id')
                    ->from('trs_pc_initiative')
                    ->whereIn('initiative_id', $project->mappedInitiatives->pluck('id'));
            })
            ->with([
                'charter' => static fn ($q) => $q->select('trs_project_charters.id', 'trs_project_charters.project_id', 'trs_project_charters.category', 'trs_project_charters.status', 'trs_project_charters.tgl_dokumen', 'trs_project_charters.duration'),
                'charters' => static fn ($q) => $q
                    ->select('trs_project_charters.id', 'trs_project_charters.project_id', 'trs_project_charters.version_label', 'trs_project_charters.category', 'trs_project_charters.status', 'trs_project_charters.tgl_dokumen', 'trs_project_charters.duration')
                    ->latest(),
                'statusRef:id,name',
                'pcStatusImplementations' => static fn ($q) => $q->orderBy('date', 'desc')->orderBy('time_start', 'desc')->limit(1),
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

        return Inertia::render('ProgramImplementation/ProjectCharter/ITInitiatives/Show', [
            'itInitiative' => $project,
            'relatedProjects' => $relatedProjects,
            'projectOptions' => $projectOptions,
        ]);
    }

    public function edit(TrsProject $project): Response
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

        $planningItDefinitions = MstInitiative::query()
            ->select(['id', 'code', 'name', 'description', 'status'])
            ->where('tipe_initiative', 2)
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('id')
            ->get()
            ->values();

        $mappedIds = [];
        if (Schema::hasTable('trs_pc_initiative')) {
            $tableColumns = Schema::getColumnListing('trs_pc_initiative');
            $projectColumn = collect(['project_id', 'trs_project_id', 'pc_id'])->first(
                static fn ($col) => in_array($col, $tableColumns, true)
            );
            $initiativeColumn = collect(['initiative_id', 'mst_initiative_id', 'useCase_id', 'use_case_id'])->first(
                static fn ($col) => in_array($col, $tableColumns, true)
            );

            if ($projectColumn && $initiativeColumn) {
                $mappedIds = DB::table('trs_pc_initiative')
                    ->where($projectColumn, $project->id)
                    ->pluck($initiativeColumn)
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
            }
        }

        return Inertia::render('ProgramImplementation/ProjectCharter/ITInitiatives/Edit', [
            'itInitiative' => $project,
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
            'planningItDefinitions' => $planningItDefinitions,
            'mappedInitiativeIds' => $mappedIds,
        ]);
    }

    public function update(ITInitiativeUpdateRequest $request, TrsProject $project): RedirectResponse
    {
        $validated = $request->validated();
        $owner = trim((string) ($validated['owner'] ?? ''));
        if ($owner === '') {
            $owner = trim((string) ($validated['owner_name'] ?? ''));
        }

        $oldStatus = $project->status;
        $charterCategory = trim((string) ($validated['charter_category'] ?? ''));
        $projectStatusChangedAt = $validated['project_status_changed_at'] ?? null;
        $projectStatusNotes = $validated['project_status_notes'] ?? null;
        unset($validated['charter_category']);
        unset($validated['project_status_changed_at']);
        unset($validated['project_status_notes']);
        unset($validated['owner'], $validated['owner_name']);

        $project->update($validated);

        if ($charterCategory !== '' || $owner !== '') {
            $latestCharter = $project->charters()->latest('id')->first();
            $charterPayload = [];

            if ($charterCategory !== '') {
                $charterPayload['category'] = $charterCategory;
            }

            if ($owner !== '') {
                $charterPayload['owner'] = $owner;
            }

            if ($latestCharter) {
                $latestCharter->update($charterPayload);
            } else {
                $project->charters()->create($charterPayload);
            }
        }

        $this->recordProjectStatusHistory($project->fresh(), $oldStatus, $project->status, $projectStatusChangedAt, $projectStatusNotes);

        return redirect()->route('it-initiatives.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(TrsProject $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('it-initiatives.index')->with('success', 'Project deleted successfully.');
    }

    public function storeImplementationStatus(Request $request, TrsProject $project): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'review_status' => ['required', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
            'month_year' => 'required|date_format:Y-m',
        ]);

        \App\Models\PcStatusImplementation::create([
            'project_id' => $project->id,
            'review_status' => $validated['review_status'],
            'status' => $validated['status'],
            'date' => \Carbon\Carbon::createFromFormat('Y-m', $validated['month_year'])->startOfMonth()->toDateString(),
            'time_start' => now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Status added successfully.');
    }

    public function updateImplementationStatus(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'review_status' => ['required', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
            'month_year' => 'required|date_format:Y-m',
        ]);

        $status = \App\Models\PcStatusImplementation::findOrFail($id);
        $status->update([
            'status' => $validated['status'],
            'review_status' => $validated['review_status'],
            'date' => \Carbon\Carbon::createFromFormat('Y-m', $validated['month_year'])->startOfMonth()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function destroyImplementationStatus($id): RedirectResponse
    {
        $status = \App\Models\PcStatusImplementation::findOrFail($id);
        $status->delete();

        return redirect()->back()->with('success', 'Status deleted successfully.');
    }

    public function updateMapping(Request $request, TrsProject $project): RedirectResponse
    {
        $validated = $request->validate([
            'initiative_ids' => 'nullable|array',
            'initiative_ids.*' => 'integer|exists:mst_initiative,id',
        ]);

        $initiativeIds = collect($validated['initiative_ids'] ?? [])
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $this->syncProjectInitiativeMappings($project, $initiativeIds);

        return redirect()->back()->with('success', 'Mapping updated successfully.');
    }

    private function recordProjectStatusHistory(
        TrsProject $project,
        mixed $fromStatusId,
        mixed $toStatusId,
        ?string $changedAt = null,
        ?string $notes = null,
    ): void
    {
        if (
            ! Schema::hasTable('trs_project_status_history')
            || ! Schema::hasColumn('trs_project_status_history', 'project_charter_id')
            || ! Schema::hasColumn('trs_project_status_history', 'version')
            || ! Schema::hasColumn('trs_project_status_history', 'tanggal')
            || ! Schema::hasColumn('trs_project_status_history', 'notes')
        ) {
            return;
        }

        $normalizedToStatusId = is_numeric($toStatusId) ? (int) $toStatusId : null;
        $normalizedFromStatusId = is_numeric($fromStatusId) ? (int) $fromStatusId : null;

        if ($normalizedToStatusId === null || $normalizedToStatusId <= 0) {
            return;
        }

        if ($normalizedFromStatusId === $normalizedToStatusId) {
            return;
        }

        if ($changedAt === null || trim($changedAt) === '') {
            return;
        }

        $nextVersion = (int) $project->projectStatusHistories()->max('version') + 1;
        $projectCharterId = $project->charters()->latest('id')->value('id');

        if ($projectCharterId === null) {
            return;
        }

        ProjectStatusHistory::query()->create([
            'project_charter_id' => $projectCharterId,
            'version' => $nextVersion,
            'tanggal' => \Carbon\Carbon::createFromFormat('Y-m-d', $changedAt)->toDateString(),
            'notes' => $this->buildProjectStatusHistoryNotes($normalizedFromStatusId, $normalizedToStatusId, $notes),
        ]);
    }

    private function buildProjectStatusHistoryNotes(?int $fromStatusId, ?int $toStatusId, ?string $notes = null): string
    {
        $manualNotes = trim((string) $notes);
        if ($manualNotes !== '') {
            return $manualNotes;
        }

        $statusNames = InitiativeStatus::query()
            ->whereIn('id', array_values(array_filter([$fromStatusId, $toStatusId])))
            ->pluck('name', 'id');

        $formatStatus = static function (?int $statusId) use ($statusNames): string {
            $name = $statusId !== null ? $statusNames->get($statusId) : null;

            if ($name === null) {
                return 'Unknown';
            }

            return ucwords(str_replace('_', ' ', (string) $name));
        };

        if ($fromStatusId === null) {
            return 'Status project charter menjadi '.$formatStatus($toStatusId).'.';
        }

        return 'Status project charter berubah dari '.$formatStatus($fromStatusId).' menjadi '.$formatStatus($toStatusId).'.';
    }
}
