<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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

        $typeColumn = $this->resolveInitiativeTypeColumn();
        $typeOneScopeStatusCounts = $this->statusCountsRawByType($typeColumn, '1');
        $emptyItScopeStatusCounts = collect();

        $openDigitalInitiatives = DigitalInitiative::query()
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
                'status_counts' => $this->mapCountsByStatus($statusOptions, $emptyItScopeStatusCounts),
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $typeOneScopeStatusCounts),
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

    private function resolveInitiativeTypeColumn(): ?string
    {
        foreach (['tipe_inisiative', 'tipe_initiative', 'tipe_inisiatif'] as $column) {
            if (Schema::hasColumn('mst_digitalInitiative', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function statusCountsRawByType(?string $typeColumn, string $typeValue): Collection
    {
        if ($typeColumn === null) {
            return collect();
        }

        return DigitalInitiative::query()
            ->whereNotNull($typeColumn)
            ->where($typeColumn, '!=', '')
            ->where($typeColumn, $typeValue)
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
