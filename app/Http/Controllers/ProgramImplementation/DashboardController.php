<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use App\Models\Project;
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

        $itStatusCounts          = $this->mapCountsByStatus($statusOptions, $this->statusCountsRaw());
        $digitalStatusCounts     = $this->mapCountsByStatus($statusOptions, $this->digitalStatusCountsRaw());

        return Inertia::render('ProgramImplementation/Dashboard', [
            'overview' => [
                'total_projects'            => Project::query()->count(),
                'total_digital_initiatives' => DigitalInitiative::query()->count(),
                'status_options'            => $statusOptions,
                'status_counts'             => $itStatusCounts,
                'digital_status_counts'     => $digitalStatusCounts,
            ],
            'completedStatusId'      => $baselineStatusId,
            'openDigitalInitiatives' => collect(),
            'openItInitiatives'      => $this->openItInitiatives($baselineStatusId),
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────

    private function statusCountsRaw(): Collection
    {
        return Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function digitalStatusCountsRaw(): Collection
    {
        return DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function openItInitiatives(int $baselineStatusId): Collection
    {
        return Project::query()
            ->with(['statusRef:id,name'])
            ->where(static function ($query) use ($baselineStatusId): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', $baselineStatusId);
            })
            ->latest()
            ->get();
    }
}
