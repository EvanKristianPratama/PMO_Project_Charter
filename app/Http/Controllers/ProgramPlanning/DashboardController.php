<?php

namespace App\Http\Controllers\ProgramPlanning;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsProject;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use ResolvesInitiativeStatus;

    public function __invoke(Request $request): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $filters = [
            'search'          => $request->string('search')->toString(),
            'category_fase'   => $request->input('category_fase'),
            'source_id'       => $request->input('source_id'),
            'groub_id'        => $request->input('groub_id'),
            'phase_id'        => $request->input('phase_id'),
            'organization_id'  => $request->input('organization_id'),
            'coe_id'          => $request->input('coe_id'),
        ];

        $options = $this->dashboardOptions();
        $statusOptions = $this->statusOptions();
        $baselineStatusId = $this->baselineStatusId($statusOptions);

        $projects = TrsProject::query()
            ->select(['id', 'code', 'name', 'status', 'metadata', 'tipe_inisiative'])
            ->with('projectStatusHistories')
            ->whereIn('tipe_inisiative', [1, 2])
            ->get();

        $totalDigital = MstInitiative::where('tipe_initiative', 1)->count();
        $totalIt      = MstInitiative::where('tipe_initiative', 2)->count();

        // All mst_initiative with relationships for the unified table
        $mstInitiatives = MstInitiative::with(['coe', 'organization.groub', 'latestStatus', 'sourceData:id,name'])
            ->orderBy('tipe_initiative')
            ->orderBy('id')
            ->get();
        $this->decorateInitiativesWithProjectStatus($mstInitiatives, $projects);
        $digitalStatusCounts = $this->initiativeProjectStatusCounts(
            $mstInitiatives->where('tipe_initiative', 1)->values()
        );
        $itStatusCounts = $this->initiativeProjectStatusCounts(
            $mstInitiatives->where('tipe_initiative', 2)->values()
        );

        return Inertia::render('ProgramPlanning/Dashboard', [
            'summary' => [
                'total_it_initiatives'       => $totalIt,
                'total_digital_initiatives'  => $totalDigital,
                'total_all_initiatives'      => $totalDigital + $totalIt,
                'status_options'             => $statusOptions,
                'it_status_counts'           => $itStatusCounts,
                'digital_status_counts'      => $digitalStatusCounts,
            ],
            'mstInitiatives'         => $mstInitiatives,
            'completedStatusId'      => $baselineStatusId,
            'filters'               => $filters,
            'options'               => $options,
            'categoryOptions'       => $this->categoryOptions(),
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────

    private function dashboardOptions(): array
    {
        return [
            'sources'        => collect(),
            'groubs'         => collect(),
            'phases'         => collect(),
            'organizations'  => collect(),
            'coes'           => collect(),
            'rjpps'          => collect(),
        ];
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
            $key = $this->projectStatusKeyFromId($this->resolvedProjectStatusId($project));
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    private function initiativeProjectStatusCounts(Collection $initiatives): array
    {
        $counts = [
            'not_start' => 0,
            'drafting' => 0,
            'propose' => 0,
            'review' => 0,
            'baseline' => 0,
            'approved' => 0,
        ];

        foreach ($initiatives as $initiative) {
            $key = (string) ($initiative->getAttribute('project_status_key') ?? 'not_start');
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    private function decorateInitiativesWithProjectStatus(Collection $initiatives, Collection $projects): void
    {
        $projectsByMetadataId = $projects
            ->filter(static fn (TrsProject $project): bool => (int) ($project->metadata['mst_initiative_id'] ?? 0) > 0)
            ->keyBy(static fn (TrsProject $project): int => (int) ($project->metadata['mst_initiative_id'] ?? 0));

        $projectsByNameAndType = $projects->groupBy(static function (TrsProject $project): string {
            return sprintf(
                '%s|%s',
                strtolower(trim((string) $project->name)),
                (string) $project->tipe_inisiative
            );
        });

        foreach ($initiatives as $initiative) {
            $project = $projectsByMetadataId->get((int) $initiative->id);

            if (! $project) {
                $key = sprintf(
                    '%s|%s',
                    strtolower(trim((string) $initiative->name)),
                    (string) $initiative->tipe_initiative
                );
                $project = $projectsByNameAndType->get($key)?->first();
            }

            $statusId = $project ? $this->resolvedProjectStatusId($project) : 0;
            $statusKey = $this->projectStatusKeyFromId($statusId);
            $latestHistory = $project ? $this->latestProjectStatusHistoryEntry($project) : null;

            $initiative->setAttribute('project_status_id', $statusId);
            $initiative->setAttribute('project_status_key', $statusKey);
            $initiative->setAttribute('project_status_label', $this->projectStatusLabel($statusKey));
            $initiative->setAttribute('project_status_date', $latestHistory?->tanggal?->toDateString() ?? $latestHistory?->tanggal);
        }
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

    private function categoryOptions(): array
    {
        return [
            ['id' => 1, 'label' => 'Planning'],
            ['id' => 2, 'label' => 'Implementation'],
        ];
    }
}
