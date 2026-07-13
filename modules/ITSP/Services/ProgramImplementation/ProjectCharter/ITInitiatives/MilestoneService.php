<?php

namespace Modules\ITSP\Services\ProgramImplementation\ProjectCharter\ITInitiatives;

use Modules\ITSP\Models\Milestone;
use Modules\ITSP\Models\TrsProjectCharter;

class MilestoneService
{
    public function storeMilestone(TrsProjectCharter $charter, array $payload): void
    {
        $nextOrder = (int) (Milestone::query()
            ->where('pc_id', $charter->id)
            ->max('order') ?? 0) + 1;

        Milestone::query()->create([
            'pc_id' => (int) $charter->id,
            'version' => $this->normalizeVersionLabel($charter->version_label),
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
            'order' => $nextOrder,
        ]);
    }

    public function updateMilestone(TrsProjectCharter $charter, Milestone $milestone, array $payload): void
    {
        abort_if((int) ($milestone->pc_id ?? 0) !== (int) $charter->id, 404);

        $milestone->update([
            'pc_id' => (int) $charter->id,
            'version' => $this->normalizeVersionLabel($charter->version_label),
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
        ]);
    }

    public function deleteMilestone(TrsProjectCharter $charter, Milestone $milestone): void
    {
        abort_if((int) ($milestone->pc_id ?? 0) !== (int) $charter->id, 404);

        $milestone->delete();
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $rawValue = strtolower(trim((string) $value));

        if ($rawValue === '' || $rawValue === 'v') {
            return 'v1';
        }

        if (preg_match('/^v(\d+)$/', $rawValue, $matches) === 1) {
            return 'v'.max(1, (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $rawValue) === 1) {
            return 'v'.max(1, (int) $rawValue);
        }

        return $rawValue;
    }
}
