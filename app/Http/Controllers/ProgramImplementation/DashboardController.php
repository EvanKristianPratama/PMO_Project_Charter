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

        return redirect()->route('strategic-house.index');
    }

    // ── Private helpers ──────────────────────────────────────────────────

    private function flowStatusOptions(): array
    {
        return [
            ['id' => 0, 'name' => 'not_start', 'label' => 'Not Start'],
            ['id' => 1, 'name' => 'drafting', 'label' => 'Drafting'],
            ['id' => 2, 'name' => 'propose', 'label' => 'Propose'],
            ['id' => 3, 'name' => 'review', 'label' => 'Review'],
            ['id' => 5, 'name' => 'baseline', 'label' => 'Baseline'],
            ['id' => 4, 'name' => 'approved', 'label' => 'Approved'],
        ];
    }

    private function projectsByType(int $tipeInitiative): Collection
    {
        $projects = TrsProject::query()
            ->with([
                'charter',
                'statusRef:id,name',
                'latestPcStatusImplementation',
                'pcStatusImplementations',
                'mappedInitiatives:id,code,name',
                'projectStatusHistories',
            ])
            ->where('tipe_inisiative', $tipeInitiative)
            ->latest()
            ->get();

        $projects->each(function (TrsProject $project): void {
            $statusId = $this->resolvedProjectStatusId($project);
            $statusKey = $this->projectStatusKeyFromId($statusId);
            $latestHistory = $this->latestProjectStatusHistoryEntry($project);

            $project->setAttribute('project_status_id', $statusId);
            $project->setAttribute('project_status_key', $statusKey);
            $project->setAttribute('project_status_label', $this->projectStatusLabel($statusKey));
            $project->setAttribute('project_status_date', $latestHistory?->tanggal?->toDateString() ?? $latestHistory?->tanggal);
        });

        return $projects;
    }

    private function projectStatusCounts(Collection $projects): array
    {
        $counts = [
            'not_start' => 0,
            'drafting' => 0,
            'propose' => 0,
            'review' => 0,
            'baseline' => 0,
            'approved' => 0,
        ];

        foreach ($projects as $project) {
            $statusKey = $this->projectStatusKeyFromId($this->resolvedProjectStatusId($project));
            $counts[$statusKey] = ($counts[$statusKey] ?? 0) + 1;
        }

        return $counts;
    }

    private function latestProjectStatusHistoryEntry(TrsProject $project): ?\App\Models\ProjectStatusHistory
    {
        if ($project->relationLoaded('projectStatusHistories')) {
            return $project->projectStatusHistories->first();
        }

        return $project->projectStatusHistories()->first();
    }

    private function resolvedProjectStatusId(TrsProject $project): int
    {
        $historyStatus = $this->latestProjectStatusHistoryEntry($project)?->status;

        return is_numeric($historyStatus) ? (int) $historyStatus : 0;
    }

    private function projectStatusKeyFromId(?int $statusId): string
    {
        return match ((int) $statusId) {
            1 => 'drafting',
            2 => 'propose',
            3 => 'review',
            5 => 'baseline',
            4 => 'approved',
            default => 'not_start',
        };
    }

    private function projectStatusLabel(string $statusKey): string
    {
        return match ($statusKey) {
            'drafting' => 'Drafting',
            'propose' => 'Propose',
            'review' => 'Review',
            'baseline' => 'Baseline',
            'approved' => 'Approved',
            default => 'Not Start',
        };
    }

    private function mstApprovedCountByType(int $tipeInitiative): int
    {
        $approvedAliases = ['approved', 'approve', 'aproved'];

        return MstInitiative::query()
            ->select(['id', 'status'])
            ->with('latestStatus')
            ->where('tipe_initiative', $tipeInitiative)
            ->get()
            ->filter(static function (MstInitiative $initiative) use ($approvedAliases): bool {
                $rawStatus = strtolower(trim((string) ($initiative->latestStatus?->status ?? $initiative->status ?? '')));

                return in_array($rawStatus, $approvedAliases, true);
            })
            ->count();
    }
}
