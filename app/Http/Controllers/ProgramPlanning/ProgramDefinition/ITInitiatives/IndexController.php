<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\ITInitiatives;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
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
            ->map(fn ($status) => [
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

        $masterItInitiatives = MstInitiative::query()
            ->select($masterSelectColumns)
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
            ])
            ->where('tipe_initiative', 2)
            ->orderBy('code')
            ->get()
            ->values();

        // Build statusCounts from the loaded collection so every initiative
        // is accounted for — even those without a trs_status_mstinitiative row.
        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = collect();
        foreach ($masterItInitiatives as $initiative) {
            $raw = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            // If the effective status isn't one of the timeline nodes, default to drafting
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }

        return Inertia::render('ProgramPlanning/ProgramDefinition/ITInitiatives/Index', [
            'totalItInitiatives' => $masterItInitiatives->count(),
            'masterItInitiatives' => $masterItInitiatives,
            'statusOptions' => $statusOptions,
            'statusCounts' => $statusCounts,
            'coeOptions' => MstCoe::orderBy('name')->get(['id', 'name'])->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'organizationOptions' => TrsOrganization::with('groub:id,name')->orderBy('name')->get(['id', 'name', 'groub_id'])->map(fn ($o) => ['id' => $o->id, 'name' => $o->name, 'groub' => $o->groub?->name])->values(),
        ]);
    }
}
