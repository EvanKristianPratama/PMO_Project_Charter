<?php

namespace App\Services\ProgramImplementation;

use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use App\Services\Shared\InitiativeStatusService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProgramImplementationPageService
{
    public function __construct(
        private readonly InitiativeStatusService $initiativeStatusService,
    ) {}

    public function getDashboardPageProps(): array
    {
        return Cache::remember('pi_dashboard_props_v1', 3600, function () {
            $statusOptions = $this->initiativeStatusService->statusOptions();
            $baselineStatusId = $this->initiativeStatusService->baselineStatusId($statusOptions);
            $flowStatusOptions = $this->flowStatusOptions();

            $digitalProjects = $this->projectsByType(1);
            $itProjects = $this->projectsByType(2);

            return [
                'overview' => [
                    'total_projects' => TrsProject::query()->count(),
                    'total_digital_initiatives' => $digitalProjects->count(),
                    'total_it_initiatives' => $itProjects->count(),
                    'status_options' => $flowStatusOptions,
                    'it_status_counts' => $this->projectStatusCounts($itProjects),
                    'digital_status_counts' => $this->projectStatusCounts($digitalProjects),
                    'total_digital_approved' => $this->approvedInitiativeCountByType(1),
                    'total_it_approved' => $this->approvedInitiativeCountByType(2),
                ],
                'completedStatusId' => $baselineStatusId,
                'openDigitalInitiatives' => $digitalProjects,
                'openItInitiatives' => $itProjects,
            ];
        });
    }

    public function getOverviewPageProps(): array
    {
        return Cache::remember('pi_overview_props_v1', 3600, function () {
            $statusOptions = $this->initiativeStatusService->statusOptions();
            $digitalProjects = $this->projectsByType(1);
            $itProjects = $this->projectsByType(2);

            return [
                'projectCharterOverview' => [
                    'status_options' => $statusOptions,
                    'digital_status_counts' => $this->countsByStatusOptions($digitalProjects, $statusOptions),
                    'it_status_counts' => $this->countsByStatusOptions($itProjects, $statusOptions),
                ],
            ];
        });
    }

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

    private function projectsByType(int $initiativeType): Collection
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
            ->where('tipe_inisiative', $initiativeType)
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

    private function countsByStatusOptions(Collection $projects, array $statusOptions): array
    {
        $rawCounts = $projects->countBy(fn (TrsProject $project) => (string) $this->resolvedProjectStatusId($project));

        return $this->initiativeStatusService->mapCountsByStatus($statusOptions, $rawCounts);
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

    private function latestProjectStatusHistoryEntry(TrsProject $project): ?ProjectStatusHistory
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

    private function approvedInitiativeCountByType(int $initiativeType): int
    {
        $approvedAliases = ['approved', 'approve', 'aproved'];

        return MstInitiative::query()
            ->select(['id', 'status'])
            ->with('latestStatus')
            ->where('tipe_initiative', $initiativeType)
            ->get()
            ->filter(static function (MstInitiative $initiative) use ($approvedAliases): bool {
                $rawStatus = strtolower(trim((string) ($initiative->latestStatus?->status ?? $initiative->status ?? '')));

                return in_array($rawStatus, $approvedAliases, true);
            })
            ->count();
    }
}
