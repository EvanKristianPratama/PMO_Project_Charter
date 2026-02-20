<?php

namespace App\Http\Controllers\Concerns;

use App\Models\InitiativeStatus;
use Illuminate\Support\Collection;

trait ResolvesInitiativeStatus
{
    protected function statusOptions(): array
    {
        return InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id'    => (int) $status->id,
                'name'  => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values()
            ->all();
    }

    protected function baselineStatusId(array $statusOptions): int
    {
        $baselineStatus = collect($statusOptions)->firstWhere('name', 'baseline');

        return (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());
    }

    protected function mapCountsByStatus(array $statusOptions, Collection $rawCounts): array
    {
        return collect($statusOptions)
            ->mapWithKeys(fn (array $status) => [
                (string) $status['id'] => (int) ($rawCounts[$status['id']] ?? $rawCounts[(string) $status['id']] ?? 0),
            ])
            ->all();
    }
}
