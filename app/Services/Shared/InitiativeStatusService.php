<?php

namespace App\Services\Shared;

use App\Models\InitiativeStatus;
use Illuminate\Support\Collection;

class InitiativeStatusService
{
    public function statusOptions(): array
    {
        return InitiativeStatus::ordered()
            ->map(fn ($status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values()
            ->all();
    }

    public function baselineStatusId(array $statusOptions): int
    {
        $baselineStatus = collect($statusOptions)->firstWhere('name', 'baseline');

        return (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());
    }

    public function mapCountsByStatus(array $statusOptions, Collection $rawCounts): array
    {
        return collect($statusOptions)
            ->mapWithKeys(fn (array $status) => [
                (string) $status['id'] => (int) ($rawCounts[$status['id']] ?? $rawCounts[(string) $status['id']] ?? 0),
            ])
            ->all();
    }
}
