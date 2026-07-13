<?php

namespace Modules\ITSP\Services\ProgramImplementation\ProjectCharter;

use Modules\ITSP\Models\InitiativeStatus;
use Modules\ITSP\Models\ProjectStatusHistory;
use Modules\ITSP\Models\TrsProject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ProjectCharterStatusService
{
    public function getStatusOptions(): Collection
    {
        return InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status): array => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values();
    }

    public function getDefaultStatusId(): int
    {
        return InitiativeStatus::DRAFTING;
    }

    public function getCompletedStatusId(): int
    {
        $baselineStatus = $this->getStatusOptions()->firstWhere('name', 'baseline');

        return (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());
    }

    public function buildStatusCounts(iterable $initiatives): array
    {
        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = [];

        foreach ($initiatives as $initiative) {
            $rawStatus = strtolower(trim((string) ($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting')));
            $canonicalStatus = $aliasMap[$rawStatus] ?? $rawStatus;

            if (! in_array($canonicalStatus, $validStatuses, true)) {
                $canonicalStatus = 'drafting';
            }

            $statusCounts[$canonicalStatus] = ($statusCounts[$canonicalStatus] ?? 0) + 1;
        }

        return $statusCounts;
    }

    public function resolveProjectStatusId(TrsProject $project): int
    {
        $historyStatus = $this->latestProjectStatusHistoryEntry($project)?->status;

        return is_numeric($historyStatus) ? (int) $historyStatus : 0;
    }

    public function recordProjectStatusHistory(
        TrsProject $project,
        mixed $fromStatusId,
        mixed $toStatusId,
        ?string $changedAt = null,
        ?string $notes = null,
    ): void {
        if (! $this->supportsProjectStatusHistory()) {
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

        $projectCharterId = $this->resolveProjectStatusHistoryCharterId($project);

        if ($projectCharterId === null) {
            return;
        }

        ProjectStatusHistory::query()->create([
            'project_charter_id' => $projectCharterId,
            'status' => $normalizedToStatusId,
            'version' => (int) $project->projectStatusHistories()->max('version') + 1,
            'tanggal' => \Carbon\Carbon::createFromFormat('Y-m-d', $changedAt)->toDateString(),
            'notes' => $this->buildProjectStatusHistoryNotes($normalizedFromStatusId, $normalizedToStatusId, $notes),
        ]);
    }

    public function updateProjectStatusHistory(TrsProject $project, ProjectStatusHistory $history, array $payload): void
    {
        $this->ensureHistoryBelongsToProject($project, $history);

        $history->update([
            'tanggal' => \Carbon\Carbon::parse($payload['tanggal'])->toDateString(),
            'notes' => filled($payload['notes'] ?? null) ? trim((string) $payload['notes']) : null,
        ]);
    }

    public function deleteProjectStatusHistory(TrsProject $project, ProjectStatusHistory $history): void
    {
        $this->ensureHistoryBelongsToProject($project, $history);

        $history->delete();
        $project->unsetRelation('projectStatusHistories');

        $this->resequenceProjectStatusHistories($project);
        $this->syncProjectStatusFromHistory($project);
    }

    private function supportsProjectStatusHistory(): bool
    {
        return Schema::hasTable('trs_project_status_history')
            && Schema::hasColumn('trs_project_status_history', 'project_charter_id')
            && Schema::hasColumn('trs_project_status_history', 'status')
            && Schema::hasColumn('trs_project_status_history', 'version')
            && Schema::hasColumn('trs_project_status_history', 'tanggal')
            && Schema::hasColumn('trs_project_status_history', 'notes');
    }

    private function ensureHistoryBelongsToProject(TrsProject $project, ProjectStatusHistory $history): void
    {
        $history->loadMissing('projectCharter:id,project_id');

        abort_unless((int) ($history->projectCharter?->project_id ?? 0) === (int) $project->id, 404);
    }

    private function latestProjectStatusHistoryEntry(TrsProject $project): ?ProjectStatusHistory
    {
        if ($project->relationLoaded('projectStatusHistories')) {
            return $project->projectStatusHistories->first();
        }

        return $project->projectStatusHistories()->first();
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
        $resolvedStatusId = $this->resolveProjectStatusId($project);

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
            $statusName = $statusId !== null ? $statusNames->get($statusId) : null;

            if ($statusName === null) {
                return 'Unknown';
            }

            return ucwords(str_replace('_', ' ', (string) $statusName));
        };

        if ($fromStatusId === null) {
            return 'Status project charter menjadi '.$formatStatus($toStatusId).'.';
        }

        return 'Status project charter berubah dari '.$formatStatus($fromStatusId).' menjadi '.$formatStatus($toStatusId).'.';
    }
}
