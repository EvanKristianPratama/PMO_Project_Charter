<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\StatusMstInitiative;
use App\Models\TrsMapSc;
use App\Models\TrsOrganization;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions = InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values();

        $masterSelectColumns = [
            'id',
            'coe_id',
            'tipe_initiative',
            'business_unit',
            'code',
            'name',
            'description',
            'status',
        ];
        if (Schema::hasColumn('mst_initiative', 'project_id')) {
            $masterSelectColumns[] = 'project_id';
        }

        $masterDigitalInitiatives = MstInitiative::query()
            ->select($masterSelectColumns)
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
            ])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get()
            ->values();

        // Build statusCounts from the loaded collection so every initiative
        // is accounted for, even those without a trs_status_mstinitiative row.
        $aliasMap = [
            'draft'    => 'drafting',
            'approve'  => 'approved',
            'aproved'  => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = collect();
        foreach ($masterDigitalInitiatives as $initiative) {
            $raw = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }

        // For initiatives whose latest status is "postpone", find the status
        // just before postpone so the frontend knows which main node to branch from.
        // Result: { "drafting": 1, "propose": 2, ... }
        $postponeFromCounts = [];
        $latestIds = StatusMstInitiative::query()
            ->select(DB::raw('MAX(id) as id'))
            ->whereHas('initiative', fn ($q) => $q->where('tipe_initiative', 1))
            ->groupBy('initiative_id');

        $postponedInitiativeIds = StatusMstInitiative::query()
            ->joinSub($latestIds, 'latest', fn ($join) => $join->on('trs_status_mstinitiative.id', '=', 'latest.id'))
            ->whereRaw('LOWER(status) = ?', ['postpone'])
            ->pluck('initiative_id');

        if ($postponedInitiativeIds->isNotEmpty()) {
            // For each postponed initiative, get the second-to-last status entry
            $prevStatuses = StatusMstInitiative::query()
                ->whereIn('initiative_id', $postponedInitiativeIds)
                ->whereRaw('LOWER(status) != ?', ['postpone'])
                ->select('initiative_id', DB::raw('MAX(id) as prev_id'))
                ->groupBy('initiative_id');

            $postponeFromCounts = StatusMstInitiative::query()
                ->joinSub($prevStatuses, 'prev', fn ($join) => $join->on('trs_status_mstinitiative.id', '=', 'prev.prev_id'))
                ->selectRaw('LOWER(trs_status_mstinitiative.status) as from_key, COUNT(*) as total')
                ->groupBy('from_key')
                ->pluck('total', 'from_key');

            // Normalize aliases for postponeFromCounts too
            $normalizedPfc = collect();
            foreach ($postponeFromCounts as $key => $total) {
                $canonical = $aliasMap[$key] ?? $key;
                $normalizedPfc[$canonical] = ($normalizedPfc[$canonical] ?? 0) + $total;
            }
            $postponeFromCounts = $normalizedPfc;
        }

        $statusForeignKey = Schema::hasColumn('trs_sc_status_implementation', 'sc_id')
            ? 'sc_id'
            : (Schema::hasColumn('trs_sc_status_implementation', 'sc_initiative_id')
                ? 'sc_initiative_id'
                : 'digital_initiative_id');

        $statusValueColumn = Schema::hasColumn('trs_sc_status_implementation', 'status')
            ? 'status'
            : (Schema::hasColumn('trs_sc_status_implementation', 'periode_status')
                ? 'periode_status'
                : null);

        $statusDateColumn = Schema::hasColumn('trs_sc_status_implementation', 'date')
            ? 'date'
            : (Schema::hasColumn('trs_sc_status_implementation', 'created_at')
                ? 'created_at'
                : null);

        $statusTimeColumn = Schema::hasColumn('trs_sc_status_implementation', 'time_start')
            ? 'time_start'
            : null;

        $latestStatusSelectColumns = [
            'latest.sc_id',
            's.id',
            's.review_status',
        ];

        if ($statusValueColumn) {
            $latestStatusSelectColumns[] = "s.{$statusValueColumn} as status_value";
        }

        if ($statusDateColumn) {
            $latestStatusSelectColumns[] = "s.{$statusDateColumn} as status_date";
        }

        if ($statusTimeColumn) {
            $latestStatusSelectColumns[] = "s.{$statusTimeColumn} as status_time";
        }

        $latestStatusByScId = DB::table('trs_sc_status_implementation as s')
            ->joinSub(
                DB::table('trs_sc_status_implementation')
                    ->selectRaw("MAX(id) as id, {$statusForeignKey} as sc_id")
                    ->whereNotNull($statusForeignKey)
                    ->groupBy($statusForeignKey),
                'latest',
                fn ($join) => $join->on('s.id', '=', 'latest.id')
            )
            ->select($latestStatusSelectColumns)
            ->get()
            ->keyBy(fn ($statusRow) => (int) $statusRow->sc_id);

        $initiativeItems = TrsScInitiative::query()
            ->select(['id'])
            ->with([
                'mapSc' => fn ($query) => $query
                    ->select(['id', 'sc_id', 'initiative_id'])
                    ->with([
                        'Initiative:id,code,name',
                    ]),
            ])
            ->whereHas('mapSc')
            ->get()
            ->flatMap(function (TrsScInitiative $scInitiative) use ($latestStatusByScId) {
                $latestStatus = $latestStatusByScId->get((int) $scInitiative->id);

                return $scInitiative->mapSc
                    ->map(function (TrsMapSc $mapSc) use ($scInitiative, $latestStatus): array {
                        $initiative = $mapSc->Initiative;

                        return [
                            'id' => (int) $scInitiative->id,
                            'no' => $initiative?->code,
                            'useCase' => $initiative?->name,
                            'latest_sc_status_implementation' => $latestStatus ? [
                                'id' => (int) $latestStatus->id,
                                'sc_initiative_id' => (int) $scInitiative->id,
                                'status' => $latestStatus->status_value ?? $latestStatus->review_status,
                                'review_status' => $latestStatus->review_status,
                                'date' => $latestStatus->status_date ?? null,
                                'time_start' => $latestStatus->status_time ?? null,
                            ] : null,
                        ];
                    })
                    ->filter(fn (array $item) => filled($item['no']) || filled($item['useCase']));
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Index', [
            'totalDigitalInitiatives' => $masterDigitalInitiatives->count(),
            'masterDigitalInitiatives' => $masterDigitalInitiatives,
            'initiativeItems' => $initiativeItems,
            'statusOptions' => $statusOptions,
            'statusCounts' => $statusCounts,
            'postponeFromCounts' => $postponeFromCounts,
            'coeOptions' => MstCoe::orderBy('name')->get(['id', 'name'])->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'organizationOptions' => TrsOrganization::with('groub:id,name')->orderBy('name')->get(['id', 'name', 'groub_id'])->map(fn ($o) => ['id' => $o->id, 'name' => $o->name, 'groub' => $o->groub?->name])->values(),
        ]);
    }
}

