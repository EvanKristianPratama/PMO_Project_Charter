<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsProject;
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

        // Build status counts from mst_initiative + latestStatus relation
        $digitalStatusCounts = $this->mstStatusCountsFromCollection(1);
        $itStatusCounts      = $this->mstStatusCountsFromCollection(2);

        return Inertia::render('ProgramImplementation/Dashboard', [
            'overview' => [
                'total_projects'              => TrsProject::query()->count(),
                'total_digital_initiatives'   => MstInitiative::where('tipe_initiative', 1)->count(),
                'total_it_initiatives'        => MstInitiative::where('tipe_initiative', 2)->count(),
                'status_options'              => $statusOptions,
                'it_status_counts'            => $itStatusCounts,
                'digital_status_counts'       => $digitalStatusCounts,
                'total_digital_approved'      => (int) ($digitalStatusCounts['approved'] ?? 0),
                'total_it_approved'           => (int) ($itStatusCounts['approved'] ?? 0),
            ],
            'completedStatusId'      => $baselineStatusId,
            'openDigitalInitiatives' => collect(),
            'openItInitiatives'      => $this->openItInitiatives($baselineStatusId),
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────

    /**
     * Count statuses from mst_initiative collection, using latestStatus
     * relation when available, falling back to 'drafting'.
     */
    private function mstStatusCountsFromCollection(int $tipeInitiative): array
    {
        $initiatives = MstInitiative::query()
            ->select(['id', 'tipe_initiative', 'status'])
            ->with('latestStatus')
            ->where('tipe_initiative', $tipeInitiative)
            ->get();

        $aliasMap = [
            'draft'   => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];

        $counts = [];
        foreach ($initiatives as $initiative) {
            $raw       = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $counts[$canonical] = ($counts[$canonical] ?? 0) + 1;
        }

        return $counts;
    }

    private function openItInitiatives(int $baselineStatusId): Collection
    {
        return TrsProject::query()
            ->with([
                'charter',
                'statusRef:id,name',
                'latestPcStatusImplementation',
                'pcStatusImplementations',
                'mappedInitiatives:id,code,name',
            ])
            ->where(static function ($query) use ($baselineStatusId): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', $baselineStatusId);
            })
            ->latest()
            ->get();
    }
}
