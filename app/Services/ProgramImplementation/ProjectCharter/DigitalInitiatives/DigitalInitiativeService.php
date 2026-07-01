<?php

namespace App\Services\ProgramImplementation\ProjectCharter\DigitalInitiatives;

use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use Modules\ITSP\Models\TrsProject;
use App\Models\TrsOrganization;
use App\Models\TrsStatusImplementation;
use App\Services\ProgramImplementation\ProjectCharter\ProjectCharterStatusService;
use Illuminate\Support\Collection;

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
            'implementationStatuses' => $this->implementationStatusRows(),
            'implementationOrganizations' => $this->implementationOrganizations(),
            'implementationInitiatives' => $this->implementationInitiatives(),
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

    public function storeImplementationStatus(array $payload): void
    {
        $initiative = MstInitiative::query()
            ->whereKey((int) $payload['initiative_id'])
            ->where('tipe_initiative', 1)
            ->firstOrFail();

        TrsStatusImplementation::query()->create([
            'initiative_id' => $initiative->id,
            'review_status' => trim((string) $payload['review_status']),
            'pic' => filled($payload['pic'] ?? null) ? trim((string) $payload['pic']) : null,
            'start' => $this->databaseMonthLabel($payload['start_month']),
            'end' => $this->databaseMonthLabel($payload['end_month'] ?? null),
            'year' => (string) $payload['year'],
            'status_updated' => trim((string) $payload['status_updated']),
        ]);
    }

    public function updateImplementationStatus(int $statusId, array $payload): void
    {
        $initiative = MstInitiative::query()
            ->whereKey((int) $payload['initiative_id'])
            ->where('tipe_initiative', 1)
            ->firstOrFail();

        $implementationStatus = TrsStatusImplementation::query()->findOrFail($statusId);

        $implementationStatus->update([
            'initiative_id' => $initiative->id,
            'review_status' => trim((string) $payload['review_status']),
            'pic' => filled($payload['pic'] ?? null) ? trim((string) $payload['pic']) : null,
            'start' => $this->databaseMonthLabel($payload['start_month']),
            'end' => $this->databaseMonthLabel($payload['end_month'] ?? null),
            'year' => (string) $payload['year'],
            'status_updated' => trim((string) $payload['status_updated']),
        ]);
    }

    public function deleteImplementationStatus(int $statusId): void
    {
        TrsStatusImplementation::query()->findOrFail($statusId)->delete();
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
                'pcStatusImplementations' => static fn ($query) => $query->orderBy('year', 'asc')->orderBy('id', 'asc'),
                'projectStatusHistories.projectCharter:id,project_id,version_label,tgl_dokumen',
            ])
            ->where('tipe_inisiative', 1)
            ->orderBy('id')
            ->get()
            ->values();
    }

    private function mstDigitalInitiatives()
    {
        $initiatives = MstInitiative::query()
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
                'statusHistory',
            ])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get();

        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $statusRank = [
            'approved' => 4,
            'review' => 3,
            'propose' => 2,
            'drafting' => 1,
            'draft' => 1,
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];

        foreach ($initiatives as $initiative) {
            $resolved = $initiative->resolveCanonicalPlanningStatus();
            $canonical = $resolved['canonical'];
            $displayStatus = $resolved['displayStatus'];

            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }

            $initiative->setAttribute('project_status_key', $canonical);
            $initiative->setRelation('latestStatus', $displayStatus);
        }

        return $initiatives->values();
    }

    private function implementationStatusRows(): Collection
    {
        return TrsStatusImplementation::query()
            ->with([
                'initiative:id,business_unit,code,name',
                'initiative.organization:id,name,groub_id',
            ])
            ->whereHas('initiative', static fn ($query) => $query->where('tipe_initiative', 1))
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (TrsStatusImplementation $status): array {
                $initiative = $status->initiative;

                return [
                    'id' => (int) $status->id,
                    'initiative_id' => (int) ($status->initiative_id ?? 0),
                    'organization_id' => $initiative?->organization?->id
                        ? (int) $initiative->organization->id
                        : null,
                    'business_unit' => $this->normalizeNullableString(
                        $initiative?->organization?->name ?? $initiative?->business_unit
                    ),
                    'code' => $this->normalizeNullableString($initiative?->code),
                    'initiative' => $this->normalizeNullableString($initiative?->name),
                    'review_status' => $this->normalizeNullableString($status->review_status),
                    'pic' => $this->normalizeNullableString($status->pic),
                    'period_start_month' => $this->normalizeMonthNumber($status->start),
                    'period_end_month' => $this->normalizeMonthNumber($status->end),
                    'period_year' => $this->normalizeNullableString($status->year),
                    'periode_status' => $this->buildImplementationPeriodLabel($status),
                    'updated_status' => $this->normalizeNullableString($status->status_updated),
                    'created_at' => $status->created_at?->toISOString(),
                    'updated_at' => $status->updated_at?->toISOString(),
                ];
            })
            ->values();
    }

    private function implementationOrganizations(): Collection
    {
        return TrsOrganization::query()
            ->select(['id', 'name'])
            ->whereHas('initiatives', static fn ($query) => $query->where('tipe_initiative', 1))
            ->orderBy('name')
            ->get()
            ->map(static fn (TrsOrganization $organization): array => [
                'id' => (int) $organization->id,
                'name' => trim((string) $organization->name),
            ])
            ->values();
    }

    private function implementationInitiatives(): Collection
    {
        return MstInitiative::query()
            ->select(['id', 'code', 'name', 'business_unit'])
            ->where('tipe_initiative', 1)
            ->with(['organization:id,name'])
            ->orderBy('code')
            ->orderBy('name')
            ->get()
            ->map(static fn (MstInitiative $initiative): array => [
                'id' => (int) $initiative->id,
                'code' => trim((string) ($initiative->code ?? '')),
                'name' => trim((string) ($initiative->name ?? '')),
                'organization_id' => $initiative->organization?->id
                    ? (int) $initiative->organization->id
                    : null,
                'organization_name' => trim((string) ($initiative->organization?->name ?? '')),
            ])
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

    private function buildImplementationPeriodLabel(TrsStatusImplementation $status): ?string
    {
        $start = $this->normalizeMonthLabel($status->start);
        $end = $this->normalizeMonthLabel($status->end);
        $year = $this->normalizeNullableString($status->year);

        if ($start && $end && $year) {
            return $start === $end
                ? sprintf('%s %s', $start, $year)
                : sprintf('%s - %s %s', $start, $end, $year);
        }

        if ($start && $end) {
            return $start === $end ? $start : sprintf('%s - %s', $start, $end);
        }

        if ($start && $year) {
            return sprintf('%s %s', $start, $year);
        }

        if ($end && $year) {
            return sprintf('%s %s', $end, $year);
        }

        return $year ?? $start ?? $end;
    }

    private function normalizeMonthLabel(mixed $value): ?string
    {
        $rawValue = trim((string) ($value ?? ''));
        if ($rawValue === '') {
            return null;
        }

        $monthNumber = $this->normalizeMonthNumber($value);

        if ($monthNumber === null) {
            return ucwords(strtolower($rawValue));
        }

        return $this->monthMap()[$monthNumber] ?? ucwords(strtolower($rawValue));
    }

    private function normalizeMonthNumber(mixed $value): ?int
    {
        $rawValue = trim((string) ($value ?? ''));
        if ($rawValue === '') {
            return null;
        }

        if (ctype_digit($rawValue)) {
            $monthNumber = (int) $rawValue;

            return array_key_exists($monthNumber, $this->monthMap()) ? $monthNumber : null;
        }

        $normalizedValue = strtolower($rawValue);
        $monthMap = [
            'jan' => 1,
            'january' => 1,
            'januari' => 1,
            'feb' => 2,
            'february' => 2,
            'februari' => 2,
            'mar' => 3,
            'march' => 3,
            'maret' => 3,
            'apr' => 4,
            'april' => 4,
            'may' => 5,
            'mei' => 5,
            'jun' => 6,
            'june' => 6,
            'juni' => 6,
            'jul' => 7,
            'july' => 7,
            'juli' => 7,
            'aug' => 8,
            'august' => 8,
            'agustus' => 8,
            'sep' => 9,
            'sept' => 9,
            'september' => 9,
            'oct' => 10,
            'october' => 10,
            'okt' => 10,
            'oktober' => 10,
            'nov' => 11,
            'november' => 11,
            'dec' => 12,
            'december' => 12,
            'des' => 12,
            'desember' => 12,
        ];

        return $monthMap[$normalizedValue] ?? null;
    }

    private function databaseMonthLabel(mixed $value): ?string
    {
        $monthNumber = $this->normalizeMonthNumber($value);

        return $monthNumber !== null
            ? ($this->databaseMonthMap()[$monthNumber] ?? null)
            : null;
    }

    private function databaseMonthMap(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function monthMap(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $rawValue = trim((string) ($value ?? ''));

        return $rawValue !== '' ? $rawValue : null;
    }
}
