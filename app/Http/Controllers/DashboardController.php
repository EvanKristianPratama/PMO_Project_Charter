<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions = $this->statusOptions();
        $baselineStatusId = $this->baselineStatusId($statusOptions);

        $projectStatusCounts = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $digitalStatusCounts = DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $openDigitalInitiatives = DigitalInitiative::query()
            ->select(['id', 'no', 'type', 'projectOwner', 'useCase', 'status', 'updated_at'])
            ->with(['statusRef:id,name'])
            ->where(static function ($query) use ($baselineStatusId): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', $baselineStatusId);
            })
            ->latest()
            ->get();

        $openItInitiatives = Project::query()
            ->select(['id', 'code', 'name', 'status', 'updated_at'])
            ->with([
                'charter' => static function ($query): void {
                    $query->select([
                        'trs_project_charters.id',
                        'trs_project_charters.project_id',
                        'trs_project_charters.category',
                    ]);
                },
                'statusRef:id,name',
            ])
            ->where(static function ($query) use ($baselineStatusId): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', $baselineStatusId);
            })
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'overview' => [
                'total_projects' => Project::query()->count(),
                'total_digital_initiatives' => DigitalInitiative::query()->count(),
                'status_options' => $statusOptions,
                'status_counts' => $this->mapCountsByStatus($statusOptions, $projectStatusCounts),
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $digitalStatusCounts),
            ],
            'completedStatusId' => $baselineStatusId,
            'openDigitalInitiatives' => $openDigitalInitiatives,
            'openItInitiatives' => $openItInitiatives,
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

    private function baselineStatusId(array $statusOptions): int
    {
        $baselineStatus = collect($statusOptions)->firstWhere('name', 'baseline');

        return (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());
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
