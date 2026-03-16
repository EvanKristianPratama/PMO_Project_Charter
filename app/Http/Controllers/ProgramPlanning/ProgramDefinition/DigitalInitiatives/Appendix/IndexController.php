<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\Theme;
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
            'compendiumOptions' => $this->compendiumOptions(),
            'initiativeOptions' => $this->initiativeOptions(),
            'coeOptions' => MstCoe::orderBy('name')->get(['id', 'name'])->values(),
            'themeOptions' => $this->themeOptions(),
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

    private function compendiumOptions()
    {
        return TrsScInitiative::query()
            ->where('source_id', 1)
            ->with(['mstInitiatives:id,code,name'])
            ->orderBy('id')
            ->get(['id', 'owner', 'usecase'])
            ->map(function (TrsScInitiative $item): array {
                $firstInitiative = $item->mstInitiatives->first();
                $label = trim((string) ($item->usecase ?: ($firstInitiative?->name ?? '-')));

                return [
                    'id' => (int) $item->id,
                    'label' => $label !== '' ? $label : '-',
                ];
            })
            ->values();
    }

    private function initiativeOptions()
    {
        return MstInitiative::where('tipe_initiative', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name'])
            ->map(fn (MstInitiative $initiative) => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
            ])
            ->values();
    }

    private function themeOptions()
    {
        return Theme::with('goal:id,code,title')
            ->orderBy('id')
            ->get()
            ->map(function (Theme $theme): array {
                $goal = $theme->goal;
                $goalTitle = $goal?->title ?? 'No Pillar';
                $goalCode = $goal?->code ?? '-';
                $themeNum = $theme->theme_number ?? 'N/A';

                return [
                    'id' => (int) $theme->id,
                    'code' => $goalCode,
                    'goal' => $goalTitle,
                    'strategic_pillar' => $goalTitle,
                    'theme_number' => $themeNum,
                    'theme_code' => $themeNum,
                    'themes' => $theme->name,
                    'name' => $theme->name,
                    'label' => "[$goalCode - $goalTitle] #$themeNum - $theme->name",
                ];
            })
            ->values();
    }
}
