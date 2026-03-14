<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
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

        $records = TrsScInitiative::with([
            'mstInitiatives:id,code,name,description,coe_id,business_unit,source',
            'mstInitiatives.organization:id,name,groub_id',
            'compendiums:id,usecase',
        ])
            ->where('source_id', 2)
            ->orderBy('id')
            ->get([
                'id',
                'owner',
                'coe',
                'usecase',
                'description',
                'source_id',
                'value',
                'urgency',
            ]);

        $rjppMap = collect();

        if ($records->isNotEmpty()) {
            $rjppScKey = Schema::hasColumn('trs_rjpp', 'sc_id') ? 'sc_id' : 'digital_id';

            $rjppMap = DB::table('trs_rjpp as rj')
                ->join('trs_themes as theme', 'theme.id', '=', 'rj.theme_id')
                ->whereIn("rj.$rjppScKey", $records->pluck('id')->all())
                ->selectRaw("rj.$rjppScKey as sc_id, theme.theme_number")
                ->orderBy('theme.theme_number')
                ->get()
                ->groupBy('sc_id')
                ->map(fn ($rows) => $rows
                    ->pluck('theme_number')
                    ->filter(fn ($num) => ! empty($num))
                    ->map(fn ($num) => "#$num")
                    ->values()
                    ->implode(', '));
        }

        $appendixItems = $records
            ->map(function (TrsScInitiative $item) use ($rjppMap): array {
                $firstMst = $item->mstInitiatives->first();

                $rjpp = (string) ($rjppMap->get($item->id, '-') ?? '-');

                $compendiums = $item->compendiums->map(function ($compendium) {
                    $label = trim((string) ($compendium->usecase ?? ''));
                    return $label !== '' ? $label : "Compendium #{$compendium->id}";
                })->filter()->implode(', ');

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $firstMst?->id,
                    'compendium' => $compendiums ?: '-',
                    'project_owner' => $item->owner ?: ($firstMst?->organization?->name ?? '-'),
                    'use_case' => $item->usecase ?: ($firstMst?->name ?? '-'),
                    'desc' => $item->description ?: ($firstMst?->description ?? '-'),
                    'value' => $this->scoreLabel($item->value),
                    'urgency' => $this->scoreLabel($item->urgency),
                    'rjpp' => trim($rjpp) !== '' ? $rjpp : '-',
                    'coe' => $item->coe ?: '-',
                ];
            })
            ->values();

        $uniqueCompendiums = $appendixItems->pluck('compendium')
            ->flatMap(fn ($compendium) => explode(', ', $compendium))
            ->filter(fn ($compendium) => $compendium !== '-')
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Index', [
            'appendixItems' => $appendixItems,
            'totalAppendixItems' => $appendixItems->count(),
            'uniqueCompendiums' => $uniqueCompendiums,
        ]);
    }

    private function scoreLabel(?int $score): string
    {
        return match ((int) $score) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => 'TBC',
        };
    }
}
