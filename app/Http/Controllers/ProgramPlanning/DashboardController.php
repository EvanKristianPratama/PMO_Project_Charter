<?php

namespace App\Http\Controllers\ProgramPlanning;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use ResolvesInitiativeStatus;

    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions    = $this->statusOptions();
        $baselineStatusId = $this->baselineStatusId($statusOptions);

        $emptyItStatusCounts = $this->mapCountsByStatus($statusOptions, collect());
        $digitalStatusCounts = $this->mapCountsByStatus($statusOptions, $this->statusCountsRaw());

        return Inertia::render('ProgramPlanning/Dashboard', [
            'summary' => [
                'total_it_initiatives'      => 0,
                'total_digital_initiatives' => DigitalInitiative::query()->count(),
                'total_all_initiatives'     => DigitalInitiative::query()->count(),
                'status_options'            => $statusOptions,
                'it_status_counts'          => $emptyItStatusCounts,
                'digital_status_counts'     => $digitalStatusCounts,
                'combined_status_counts'    => $digitalStatusCounts,
                'status_rows'              => $this->statusRows($statusOptions, $emptyItStatusCounts, $digitalStatusCounts),
            ],
            'completedStatusId'      => $baselineStatusId,
            'openDigitalInitiatives' => $this->openDigitalInitiatives($baselineStatusId),
            'openItInitiatives'      => collect(),
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────

    private function statusCountsRaw(): Collection
    {
        return DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function openDigitalInitiatives(int $baselineStatusId): Collection
    {
        return DigitalInitiative::query()
            ->with(['statusRef:id,name'])
            ->where(static function ($query) use ($baselineStatusId): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', $baselineStatusId);
            })
            ->latest()
            ->get();
    }

    private function statusRows(array $statusOptions, array $itCounts, array $digitalCounts): array
    {
        return collect($statusOptions)
            ->map(function (array $status) use ($itCounts, $digitalCounts): array {
                $statusId     = (string) $status['id'];
                $itCount      = (int) ($itCounts[$statusId] ?? 0);
                $digitalCount = (int) ($digitalCounts[$statusId] ?? 0);

                return [
                    'id'      => (int) $status['id'],
                    'name'    => $status['name'],
                    'label'   => $status['label'],
                    'it'      => $itCount,
                    'digital' => $digitalCount,
                    'total'   => $itCount + $digitalCount,
                ];
            })
            ->values()
            ->all();
    }
}
