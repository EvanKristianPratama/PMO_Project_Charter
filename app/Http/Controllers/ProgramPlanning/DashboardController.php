<?php

namespace App\Http\Controllers\ProgramPlanning;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
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

        // Status counts from mst_initiative + latestStatus relation
        $digitalStatusCounts = $this->mstStatusCountsFromCollection(1);
        $itStatusCounts      = $this->mstStatusCountsFromCollection(2);

        $totalDigital = MstInitiative::where('tipe_initiative', 1)->count();
        $totalIt      = MstInitiative::where('tipe_initiative', 2)->count();

        // All mst_initiative with relationships for the unified table
        $mstInitiatives = MstInitiative::with(['coe', 'organization.groub', 'latestStatus', 'sourceData:id,name'])
            ->orderBy('tipe_initiative')
            ->orderBy('id')
            ->get();

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

    private function categoryOptions(): array
    {
        return [
            ['id' => 1, 'label' => 'Planning'],
            ['id' => 2, 'label' => 'Implementation'],
        ];
    }
}
