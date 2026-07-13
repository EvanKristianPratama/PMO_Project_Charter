<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\MasterDigitalInitiative;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\InitiativeStatus;
use Modules\ITSP\Models\MstCoe;
use Modules\ITSP\Models\MstInitiative;
use Modules\ITSP\Models\StatusMstInitiative;
use Modules\ITSP\Models\TrsMapSc;
use App\Models\TrsOrganization;
use Modules\ITSP\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {

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
                'statusHistory',
            ])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get();

        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $statusRank = [
            'approved' => 4,
            'review' => 3,
            'propose' => 2,
            'drafting' => 1,
            'draft' => 1,
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];

        $statusCounts = collect();
        $postponeFromCounts = collect();

        foreach ($masterDigitalInitiatives as $initiative) {
            $resolved = $initiative->resolveCanonicalPlanningStatus();
            $canonical = $resolved['canonical'];
            $displayStatus = $resolved['displayStatus'];

            if ($canonical === 'postpone') {
                $history = $initiative->statusHistory;
                $highestEntry = $history->filter(fn ($s) => strtolower(trim($s->status)) !== 'postpone')
                    ->sortByDesc(fn ($s) => [
                        $statusRank[$aliasMap[strtolower(trim($s->status))] ?? strtolower(trim($s->status))] ?? 0,
                        $s->id,
                    ])
                    ->first();

                if ($highestEntry) {
                    $hr = strtolower(trim($highestEntry->status));
                    $fromKey = $aliasMap[$hr] ?? $hr;
                    $postponeFromCounts[$fromKey] = ($postponeFromCounts[$fromKey] ?? 0) + 1;
                }
            }

            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }

            $initiative->setAttribute('project_status_key', $canonical);
            $initiative->setRelation('latestStatus', $displayStatus);
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }

        $masterDigitalInitiatives = $masterDigitalInitiatives->values();

        // For initiatives whose latest status is "postpone", find the status
        // just before postpone so the frontend knows which main node to branch from.
        // (Note: we already calculated postponeFromCounts in the loop above for efficiency)
        // We can keep the existing logic below or use the one from the loop.
        // To avoid redundant queries, let's use the one from the loop.
        
        $postponeFromCounts = collect($postponeFromCounts)->mapWithKeys(function ($total, $key) use ($aliasMap) {
            $canonical = $aliasMap[$key] ?? $key;
            return [$canonical => $total];
        });

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
            ->flatMap(function (TrsScInitiative $scInitiative) {
                return $scInitiative->mapSc
                    ->map(function (TrsMapSc $mapSc) use ($scInitiative): array {
                        $initiative = $mapSc->Initiative;

                        return [
                            'id' => (int) $scInitiative->id,
                            'no' => $initiative?->code,
                            'useCase' => $initiative?->name,
                        ];
                    })
                    ->filter(fn (array $item) => filled($item['no']) || filled($item['useCase']));
            })
            ->values();

        return Inertia::render('modules/ITSP/ProgramPlanning/ProgramDefinition/DigitalInitiatives/MasterDigitalInitiative/Index', [
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
