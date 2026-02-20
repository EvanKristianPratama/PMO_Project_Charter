<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ProgramImplementationController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions = $this->statusOptions();
        $digitalStatusCountsRaw = collect();
        $itStatusCountsRaw = $this->statusCountsRawFromProjects();

        return Inertia::render('ProgramImplementation/Index', [
            'projectCharterOverview' => [
                'status_options' => $statusOptions,
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $digitalStatusCountsRaw),
                'it_status_counts' => $this->mapCountsByStatus($statusOptions, $itStatusCountsRaw),
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

    private function statusCountsRawFromProjects(): Collection
    {
        return Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function mapCountsByStatus(array $statusOptions, Collection $rawCounts): array
    {
        return collect($statusOptions)
            ->mapWithKeys(fn (array $status) => [
                (string) $status['id'] => (int) ($rawCounts[$status['id']] ?? $rawCounts[(string) $status['id']] ?? 0),
            ])
            ->all();
    }
}
