<?php

namespace Modules\ITSP\Controllers\ProgramPlanning;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use ResolvesInitiativeStatus;

    public function __invoke(Request $request): Response|RedirectResponse
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'category_fase' => $request->input('category_fase'),
            'source_id' => $request->input('source_id'),
            'groub_id' => $request->input('groub_id'),
            'phase_id' => $request->input('phase_id'),
            'organization_id' => $request->input('organization_id'),
            'coe_id' => $request->input('coe_id'),
        ];

        $options = $this->dashboardOptions();
        $statusOptions = $this->statusOptions();
        $baselineStatusId = $this->baselineStatusId($statusOptions);

        $mstInitiatives = MstInitiative::with(['coe', 'organization.groub', 'latestStatus', 'statusHistory', 'sourceData:id,name'])
            ->orderBy('tipe_initiative')
            ->orderBy('id')
            ->get();

        $this->decorateInitiativesWithPlanningStatus($mstInitiatives);

        $totalDigital = $mstInitiatives->where('tipe_initiative', 1)->count();
        $totalIt = $mstInitiatives->where('tipe_initiative', 2)->count();

        $digitalStatusCounts = $this->initiativeProjectStatusCounts(
            $mstInitiatives->where('tipe_initiative', 1)->values()
        );
        $itStatusCounts = $this->initiativeProjectStatusCounts(
            $mstInitiatives->where('tipe_initiative', 2)->values()
        );

        return Inertia::render('modules/ITSP/ProgramPlanning/Dashboard', [
            'summary' => [
                'total_it_initiatives' => $totalIt,
                'total_digital_initiatives' => $totalDigital,
                'total_all_initiatives' => $totalDigital + $totalIt,
                'status_options' => $statusOptions,
                'it_status_counts' => $itStatusCounts,
                'digital_status_counts' => $digitalStatusCounts,
            ],
            'mstInitiatives' => $mstInitiatives,
            'completedStatusId' => $baselineStatusId,
            'filters' => $filters,
            'options' => $options,
            'categoryOptions' => $this->categoryOptions(),
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────

    private function dashboardOptions(): array
    {
        return [
            'sources' => collect(),
            'groubs' => collect(),
            'phases' => collect(),
            'organizations' => collect(),
            'coes' => collect(),
            'rjpps' => collect(),
        ];
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

    private function decorateInitiativesWithPlanningStatus(Collection $initiatives): void
    {
        foreach ($initiatives as $initiative) {
            $statusKey = $this->planningStatusKey($initiative);

            $initiative->setAttribute('project_status_id', $this->planningStatusId($statusKey));
            $initiative->setAttribute('project_status_key', $statusKey);
            $initiative->setAttribute('project_status_label', $this->planningStatusLabel($statusKey));
            $initiative->setAttribute('project_status_date', $this->planningStatusDate($initiative));
        }
    }

    private function planningStatusKey(MstInitiative $initiative): string
    {
        return $initiative->resolveCanonicalPlanningStatus()['canonical'];
    }

    private function planningStatusId(string $statusKey): int
    {
        return match ($statusKey) {
            'drafting' => 1,
            'propose' => 2,
            'review' => 3,
            'baseline' => 5,
            'approved' => 4,
            default => 0,
        };
    }

    private function planningStatusLabel(string $statusKey): string
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

    private function planningStatusDate(MstInitiative $initiative): ?string
    {
        $latestStatus = $initiative->latestStatus;

        return $latestStatus?->tanggal?->toDateString()
            ?? $latestStatus?->created_at?->toDateString()
            ?? null;
    }

    private function categoryOptions(): array
    {
        return [
            ['id' => 1, 'label' => 'Planning'],
            ['id' => 2, 'label' => 'Implementation'],
        ];
    }
}
