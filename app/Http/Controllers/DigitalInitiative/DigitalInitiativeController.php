<?php

namespace App\Http\Controllers\DigitalInitiative;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DigitalInitiativeController extends Controller
{
    public function index(): Response
    {
        $statusOptions = InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values();

        $baselineStatus = $statusOptions->firstWhere('name', 'baseline');
        $baselineStatusId = (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());

        $initiatives = \App\Models\TrsProject::query()
            ->with([
                'owner',
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
                        'trs_project_charters.background',
                        'trs_project_charters.scope',
                        'trs_project_charters.impact_value',
                        'trs_project_charters.key_personnel',
                        'trs_project_charters.key_items',
                        'trs_project_charters.budget',
                        'trs_project_charters.risks_identified',
                        'trs_project_charters.risk_mitigation',
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
                'charter.milestones',
                'charters' => static fn ($query) => $query
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
                        'trs_project_charters.background',
                        'trs_project_charters.scope',
                        'trs_project_charters.impact_value',
                        'trs_project_charters.key_personnel',
                        'trs_project_charters.key_items',
                        'trs_project_charters.budget',
                        'trs_project_charters.risks_identified',
                        'trs_project_charters.risk_mitigation',
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
                    ->latest('id'),
                'statusRef:id,name',
                'pcStatusImplementations' => static fn ($q) => $q->orderBy('date', 'desc')->orderBy('id', 'desc'),
                'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen',
            ])
            ->where('tipe_inisiative', 1)
            ->orderBy('id', 'asc')
            ->get()
            ->values();

        $totalDigitalInitiatives = MstInitiative::query()
            ->where('tipe_initiative', 1)
            ->count();

        $masterDigitalInitiatives = MstInitiative::query()
            ->select([
                'id',
                'coe_id',
                'tipe_initiative',
                'business_unit',
                'code',
                'name',
                'description',
                'status',
            ])
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
            ])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get()
            ->values();

        // Build statusCounts from mst_initiative + latestStatus (name-based keys)
        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = [];
        foreach ($masterDigitalInitiatives as $initiative) {
            $raw = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }
        $totalDigitalApproved = (int) ($statusCounts['approved'] ?? 0);

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Index', [
            'initiatives' => $initiatives,
            'mstDigitalInitiatives' => $masterDigitalInitiatives,
            'statusOptions' => $statusOptions,
            'completedStatusId' => $baselineStatusId,
            'totalDigitalInitiatives' => $totalDigitalInitiatives,
            'totalDigitalApproved' => $totalDigitalApproved,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Create', [
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'tipe_inisiative' => 'nullable|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
        ]);

        $digitalInitiative = DigitalInitiative::create($validated);

        if ($digitalInitiative->status) {
            $statusModel = InitiativeStatus::find($digitalInitiative->status);
            $statusName = $statusModel ? $statusModel->name : (string) $digitalInitiative->status;

            \App\Models\UcStatusImplementation::create([
                'digital_initiative_id' => $digitalInitiative->id,
                'status' => $statusName,
                'date' => now()->toDateString(),
                'time_start' => now()->toTimeString(),
            ]);
        }

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative created successfully.');
    }

    public function show(\App\Models\TrsProject $digitalInitiative): Response
    {
        $digitalInitiative->load([
            'charter',
            'charter.milestones',
            'charters' => static fn ($query) => $query->latest()->with('milestones'),
            'owner',
            'statusRef:id,name',
            'pcStatusImplementations',
            'mappedInitiatives.coe:id,name',
            'mappedInitiatives.organization:id,name,groub_id',
            'mappedInitiatives.organization.groub:id,name',
            'mappedInitiatives.sourceData:id,name,month,year,created_at',
            'mappedInitiatives.taggings.theme:id,idGoal,code,name,strategic_pillar,theme_code,theme_number,theme_name',
            'mappedInitiatives.taggings.theme.goal:id,code,title',
        ]);

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Show', [
            'initiative' => $digitalInitiative,
        ]);
    }

    public function edit(TrsProject $digitalInitiative): Response
    {
        $digitalInitiative->load([
            'charter',
            'latestPcStatusImplementation',
            'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen'
        ]);

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Edit', [
            'initiative' => $digitalInitiative,
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
        ]);
    }

    public function update(Request $request, TrsProject $digitalInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'project_status_changed_at' => 'nullable|date',
            'project_status_notes' => 'nullable|string',
            'charter_category' => 'nullable|string|max:255',
        ]);

        $oldStatus = $this->resolvedProjectStatusId($digitalInitiative);
        $projectStatusChangedAt = $validated['project_status_changed_at'] ?? null;
        $projectStatusNotes = $validated['project_status_notes'] ?? null;
        $charterCategory = $validated['charter_category'] ?? null;

        $digitalInitiative->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        // Update charter category if provided
        if ($charterCategory !== null) {
            $charter = $digitalInitiative->charter;
            if ($charter) {
                $charter->update(['category' => $charterCategory]);
            } else {
                $digitalInitiative->charters()->create(['category' => $charterCategory]);
            }
        }

        $this->recordProjectStatusHistory($digitalInitiative->fresh(), $oldStatus, $digitalInitiative->status, $projectStatusChangedAt, $projectStatusNotes);

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative updated successfully.');
    }

    public function destroy(TrsProject $digitalInitiative): RedirectResponse
    {
        $digitalInitiative->delete();

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative deleted successfully.');
    }

    public function updateProjectStatusHistory(Request $request, TrsProject $digitalInitiative, ProjectStatusHistory $history): RedirectResponse
    {
        $history->loadMissing('projectCharter:id,project_id');
        abort_unless((int) ($history->projectCharter?->project_id ?? 0) === (int) $digitalInitiative->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $history->update([
            'tanggal' => \Carbon\Carbon::parse($validated['tanggal'])->toDateString(),
            'notes' => filled($validated['notes'] ?? null) ? trim((string) $validated['notes']) : null,
        ]);

        return redirect()->back()->with('success', 'Project status history updated successfully.');
    }

    public function destroyProjectStatusHistory(TrsProject $digitalInitiative, ProjectStatusHistory $history): RedirectResponse
    {
        $history->loadMissing('projectCharter:id,project_id');
        abort_unless((int) ($history->projectCharter?->project_id ?? 0) === (int) $digitalInitiative->id, 404);

        $history->delete();

        $digitalInitiative->unsetRelation('projectStatusHistories');
        $this->resequenceProjectStatusHistories($digitalInitiative);
        $this->syncProjectStatusFromHistory($digitalInitiative);

        return redirect()->back()->with('success', 'Project status history deleted successfully.');
    }

    private function recordProjectStatusHistory(
        TrsProject $project,
        ?int $fromStatusId,
        ?int $toStatusId,
        ?string $changedAt = null,
        ?string $notes = null,
    ): void {
        if (
            ! Schema::hasTable('trs_project_status_history')
            || ! Schema::hasColumn('trs_project_status_history', 'project_charter_id')
            || ! Schema::hasColumn('trs_project_status_history', 'status')
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
        $projectCharterId = $this->resolveProjectStatusHistoryCharterId($project);

        if ($projectCharterId === null) {
            return;
        }

        ProjectStatusHistory::query()->create([
            'project_charter_id' => $projectCharterId,
            'status' => $normalizedToStatusId,
            'version' => $nextVersion,
            'tanggal' => \Carbon\Carbon::parse($changedAt)->toDateString(),
            'notes' => $this->buildProjectStatusHistoryNotes($normalizedFromStatusId, $normalizedToStatusId, $notes),
        ]);
    }

    private function latestProjectStatusHistoryEntry(TrsProject $project): ?ProjectStatusHistory
    {
        if ($project->relationLoaded('projectStatusHistories')) {
            return $project->projectStatusHistories->first();
        }

        return $project->projectStatusHistories()->first();
    }

    private function resolvedProjectStatusId(TrsProject $project): ?int
    {
        $historyStatus = $this->latestProjectStatusHistoryEntry($project)?->status;
        if (is_numeric($historyStatus)) {
            return (int) $historyStatus;
        }

        return 0;
    }

    private function resolveProjectStatusHistoryCharterId(TrsProject $project): ?int
    {
        $projectCharterId = $project->charters()->latest('id')->value('id');
        if ($projectCharterId !== null) {
            return (int) $projectCharterId;
        }

        $charter = $project->charters()->create([
            'version_label' => 'v1',
        ]);

        return (int) $charter->id;
    }

    private function resequenceProjectStatusHistories(TrsProject $project): void
    {
        $project->projectStatusHistories()
            ->orderBy('version')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(static function (ProjectStatusHistory $history, int $index): void {
                $expectedVersion = $index + 1;

                if ((int) $history->version !== $expectedVersion) {
                    $history->update(['version' => $expectedVersion]);
                }
            });
    }

    private function syncProjectStatusFromHistory(TrsProject $project): void
    {
        $project->refresh();
        $resolvedStatusId = $this->resolvedProjectStatusId($project);

        if ((int) $project->status !== $resolvedStatusId) {
            $project->update(['status' => $resolvedStatusId]);
        }
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
