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
            $history = $initiative->statusHistory;
            $absoluteLatest = $history->sortByDesc('id')->first();
            $absStatusRaw = strtolower(trim($absoluteLatest?->status ?? ''));
            $absStatus = $aliasMap[$absStatusRaw] ?? $absStatusRaw;

            // Find highest rank (excluding postpone)
            $highestEntry = $history->filter(fn ($s) => strtolower(trim($s->status)) !== 'postpone')
                ->sortByDesc(function ($s) use ($statusRank, $aliasMap) {
                    $r = strtolower(trim($s->status));

                    return $statusRank[$aliasMap[$r] ?? $r] ?? 0;
                })
                ->sortByDesc('id') // tie-breaker
                ->first();

            if ($absStatus === 'postpone') {
                $canonical = 'postpone';
                $displayStatus = $absoluteLatest;

                if ($highestEntry) {
                    $hr = strtolower(trim($highestEntry->status));
                    $fromKey = $aliasMap[$hr] ?? $hr;
                    $postponeFromCounts[$fromKey] = ($postponeFromCounts[$fromKey] ?? 0) + 1;
                }
            } else {
                if ($highestEntry) {
                    $hr = strtolower(trim($highestEntry->status));
                    $canonical = $aliasMap[$hr] ?? $hr;
                    $displayStatus = $highestEntry;
                } else {
                    $raw = strtolower(trim($initiative->status ?? 'drafting'));
                    $canonical = $aliasMap[$raw] ?? $raw;
                    $displayStatus = (object) ['status' => $canonical, 'notes' => ''];
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
