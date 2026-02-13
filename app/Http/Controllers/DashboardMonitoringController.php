<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardMonitoringController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions = $this->statusOptions();

        $itStatusCountsRaw = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $digitalStatusCountsRaw = DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $itStatusCounts = $this->mapCountsByStatus($statusOptions, $itStatusCountsRaw);
        $digitalStatusCounts = $this->mapCountsByStatus($statusOptions, $digitalStatusCountsRaw);

        return Inertia::render('DashboardMonitoring', [
            'summary' => [
                'total_it_initiatives' => Project::query()->count(),
                'total_digital_initiatives' => DigitalInitiative::query()->count(),
                'total_all_initiatives' => Project::query()->count() + DigitalInitiative::query()->count(),
                'status_options' => $statusOptions,
                'it_status_counts' => $itStatusCounts,
                'digital_status_counts' => $digitalStatusCounts,
                'combined_status_counts' => $this->combineCounts($itStatusCounts, $digitalStatusCounts),
                'status_rows' => $this->statusRows($statusOptions, $itStatusCounts, $digitalStatusCounts),
            ],
        ]);
    }

    private function statusOptions(): array
    {
        return InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values()
            ->all();
    }

    private function mapCountsByStatus(array $statusOptions, Collection $rawCounts): array
    {
        return collect($statusOptions)
            ->mapWithKeys(fn (array $status) => [
                (string) $status['id'] => (int) ($rawCounts[$status['id']] ?? $rawCounts[(string) $status['id']] ?? 0),
            ])
            ->all();
    }

    private function combineCounts(array $itStatusCounts, array $digitalStatusCounts): array
    {
        return collect($itStatusCounts)
            ->mapWithKeys(fn (int $count, string $statusId) => [
                $statusId => $count + (int) ($digitalStatusCounts[$statusId] ?? 0),
            ])
            ->all();
    }

    private function statusRows(array $statusOptions, array $itStatusCounts, array $digitalStatusCounts): array
    {
        return collect($statusOptions)
            ->map(function (array $status) use ($itStatusCounts, $digitalStatusCounts): array {
                $statusId = (string) $status['id'];
                $itCount = (int) ($itStatusCounts[$statusId] ?? 0);
                $digitalCount = (int) ($digitalStatusCounts[$statusId] ?? 0);

                return [
                    'id' => (int) $status['id'],
                    'name' => $status['name'],
                    'label' => $status['label'],
                    'it' => $itCount,
                    'digital' => $digitalCount,
                    'total' => $itCount + $digitalCount,
                ];
            })
            ->values()
            ->all();
    }
}
