<?php

namespace App\Services\ProgramImplementation\ProjectCharter\DigitalInitiatives;

use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use App\Services\ProgramImplementation\ProjectCharter\ProjectCharterStatusService;

class DigitalInitiativeService
{
    public function __construct(
        private readonly ProjectCharterStatusService $projectCharterStatusService
    ) {}

    public function getIndexProps(): array
    {
        $mstDigitalInitiatives = $this->mstDigitalInitiatives();
        $statusCounts = $this->projectCharterStatusService->buildStatusCounts($mstDigitalInitiatives);

        return [
            'initiatives' => $this->digitalInitiatives(),
            'mstDigitalInitiatives' => $mstDigitalInitiatives,
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'completedStatusId' => $this->projectCharterStatusService->getCompletedStatusId(),
            'totalDigitalInitiatives' => MstInitiative::query()->where('tipe_initiative', 1)->count(),
            'totalDigitalApproved' => (int) ($statusCounts['approved'] ?? 0),
            'statusCounts' => $statusCounts,
        ];
    }

    public function getCreateProps(): array
    {
        return [
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'defaultStatusId' => $this->projectCharterStatusService->getDefaultStatusId(),
        ];
    }

    public function getShowProps(TrsProject $digitalInitiative): array
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

        return [
            'initiative' => $digitalInitiative,
        ];
    }

    public function getEditProps(TrsProject $digitalInitiative): array
    {
        $digitalInitiative->load([
            'charter',
            'latestPcStatusImplementation',
            'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen',
        ]);

        return [
            'initiative' => $digitalInitiative,
            'statusOptions' => $this->projectCharterStatusService->getStatusOptions(),
            'defaultStatusId' => $this->projectCharterStatusService->getDefaultStatusId(),
        ];
    }

    public function createDigitalInitiative(array $payload): TrsProject
    {
        $digitalInitiative = TrsProject::query()->create([
            'code' => trim((string) $payload['code']),
            'name' => trim((string) $payload['name']),
            'owner_name' => filled($payload['owner_name'] ?? null) ? trim((string) $payload['owner_name']) : null,
            'status' => (int) $payload['status'],
            'tipe_inisiative' => 1,
        ]);

        $this->upsertProjectCharter(
            $digitalInitiative,
            $payload['charter_category'] ?? null,
            $payload['owner_name'] ?? null
        );

        $this->projectCharterStatusService->recordProjectStatusHistory(
            $digitalInitiative->fresh(),
            null,
            $digitalInitiative->status,
            $payload['project_status_changed_at'] ?? null,
            $payload['project_status_notes'] ?? null,
        );

        return $digitalInitiative->fresh();
    }

    public function updateDigitalInitiative(TrsProject $digitalInitiative, array $payload): void
    {
        $oldStatus = $this->projectCharterStatusService->resolveProjectStatusId($digitalInitiative);

        $digitalInitiative->update([
            'code' => trim((string) $payload['code']),
            'name' => trim((string) $payload['name']),
            'owner_name' => filled($payload['owner_name'] ?? null) ? trim((string) $payload['owner_name']) : null,
            'status' => (int) $payload['status'],
        ]);

        $this->upsertProjectCharter(
            $digitalInitiative,
            $payload['charter_category'] ?? null,
            $payload['owner_name'] ?? null
        );

        $this->projectCharterStatusService->recordProjectStatusHistory(
            $digitalInitiative->fresh(),
            $oldStatus,
            $digitalInitiative->status,
            $payload['project_status_changed_at'] ?? null,
            $payload['project_status_notes'] ?? null,
        );
    }

    public function deleteDigitalInitiative(TrsProject $digitalInitiative): void
    {
        $digitalInitiative->delete();
    }

    public function updateProjectStatusHistory(
        TrsProject $digitalInitiative,
        ProjectStatusHistory $history,
        array $payload
    ): void {
        $this->projectCharterStatusService->updateProjectStatusHistory($digitalInitiative, $history, $payload);
    }

    public function deleteProjectStatusHistory(TrsProject $digitalInitiative, ProjectStatusHistory $history): void
    {
        $this->projectCharterStatusService->deleteProjectStatusHistory($digitalInitiative, $history);
    }

    private function digitalInitiatives()
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
                'pcStatusImplementations' => static fn ($query) => $query->orderBy('date', 'desc')->orderBy('id', 'desc'),
                'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen',
            ])
            ->where('tipe_inisiative', 1)
            ->orderBy('id')
            ->get()
            ->values();
    }

    private function mstDigitalInitiatives()
    {
        return MstInitiative::query()
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
    }

    private function upsertProjectCharter(TrsProject $digitalInitiative, mixed $charterCategory, mixed $ownerName): void
    {
        $charterPayload = [];

        if (filled($charterCategory)) {
            $charterPayload['category'] = trim((string) $charterCategory);
        }

        if (filled($ownerName)) {
            $charterPayload['owner'] = trim((string) $ownerName);
        }

        if ($charterPayload === []) {
            return;
        }

        $latestCharter = $digitalInitiative->charters()->latest('id')->first();

        if ($latestCharter) {
            $latestCharter->update($charterPayload);

            return;
        }

        $digitalInitiative->charters()->create([
            'version_label' => 'v1',
            ...$charterPayload,
        ]);
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
            'trs_project_charters.scope',
            'trs_project_charters.impact_value',
            'trs_project_charters.key_personnel',
            'trs_project_charters.key_items',
            'trs_project_charters.budget',
            'trs_project_charters.risks_identified',
            'trs_project_charters.risk_mitigation',
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
