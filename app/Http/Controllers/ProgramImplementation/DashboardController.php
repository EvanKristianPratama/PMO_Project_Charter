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
        $flowStatusOptions = $this->flowStatusOptions();

        $digitalProjects = $this->projectsByType(1);
        $itProjects = $this->projectsByType(2);
        $digitalStatusCounts = $this->projectStatusCounts($digitalProjects);
        $itStatusCounts = $this->projectStatusCounts($itProjects);
        $digitalApprovedFromMst = $this->mstApprovedCountByType(1);
        $itApprovedFromMst = $this->mstApprovedCountByType(2);

        return Inertia::render('ProgramImplementation/Dashboard', [
            'overview' => [
                'total_projects'              => TrsProject::query()->count(),
                'total_digital_initiatives'   => $digitalProjects->count(),
                'total_it_initiatives'        => $itProjects->count(),
                'status_options'              => $flowStatusOptions,
                'it_status_counts'            => $itStatusCounts,
                'digital_status_counts'       => $digitalStatusCounts,
                'total_digital_approved'      => $digitalApprovedFromMst,
                'total_it_approved'           => $itApprovedFromMst,
            ],
            'completedStatusId'      => $baselineStatusId,
            'openDigitalInitiatives' => $digitalProjects,
            'openItInitiatives'      => $itProjects,
        ]);
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
        return TrsProject::query()
            ->with([
                'charter',
                'statusRef:id,name',
                'latestPcStatusImplementation',
                'pcStatusImplementations',
                'mappedInitiatives:id,code,name',
            ])
            ->where('tipe_inisiative', $tipeInitiative)
            ->latest()
            ->get();
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
            $statusId = is_numeric($project->status) ? (int) $project->status : null;

            if ($statusId === null) {
                $counts['not_start']++;
                continue;
            }

            if ($statusId === 1) {
                $counts['drafting']++;
                continue;
            }

            if ($statusId === 2) {
                $counts['propose']++;
                continue;
            }

            if ($statusId === 3) {
                $counts['review']++;
                continue;
            }

            if ($statusId === 5) {
                $counts['baseline']++;
                continue;
            }

            if ($statusId === 4) {
                $counts['approved']++;
            }
        }

        return $counts;
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
