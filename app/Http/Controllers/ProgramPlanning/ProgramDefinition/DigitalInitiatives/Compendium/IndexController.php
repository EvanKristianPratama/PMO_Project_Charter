<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\MstScSource;
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
            'mstInitiatives.coe:id,name',
            'mstInitiatives.organization:id,name,groub_id',
            'mstInitiatives.organization.groub:id,name',
            'mstInitiatives.sourceData:id,name,month,year,created_at',
            'sourceData:id,name,created_at',
        ])
            ->whereHas('mstInitiatives')
            ->orderBy('id')
            ->get([
                'id',
                'owner',
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
                ->selectRaw("rj.$rjppScKey as sc_id, theme.name as theme_name")
                ->orderBy('theme.name')
                ->get()
                ->groupBy('sc_id')
                ->map(fn ($rows) => $rows
                    ->pluck('theme_name')
                    ->filter(fn ($name) => is_string($name) && trim($name) !== '')
                    ->values()
                    ->implode(', '));
        }

        $compendiumItems = $records
            ->map(function (TrsScInitiative $item) use ($rjppMap): array {
                $firstMst = $item->mstInitiatives->first();
                $source = $item->sourceData ?? $firstMst?->sourceData;

                $sourceCreatedAt = '-';
                if ($source) {
                    if (!empty($source->month) && !empty($source->year)) {
                        $sourceCreatedAt = $source->month . ' ' . $source->year;
                    } elseif (!empty($source->created_at)) {
                        $sourceCreatedAt = $source->created_at->format('M Y');
                    }
                }

                $rjpp = (string) ($rjppMap->get($item->id, '-') ?? '-');

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $firstMst?->id,
                    'group' => $firstMst?->organization?->groub?->name ?? '-',
                    'no' => $firstMst?->code ?? '-',
                    'project_owner' => $item->owner ?: ($firstMst?->organization?->name ?? '-'),
                    'use_case' => $item->usecase ?: ($firstMst?->name ?? '-'),
                    'desc' => $item->description ?: ($firstMst?->description ?? '-'),
                    'value' => $this->scoreLabel($item->value),
                    'urgency' => $this->scoreLabel($item->urgency),
                    'rjpp' => trim($rjpp) !== '' ? $rjpp : '-',
                    'coe' => $firstMst?->coe?->name ?? '-',
                    'data_source' => $source?->name ?? '-',
                    'data_source_created' => $sourceCreatedAt,
                ];
            })
            ->values();

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->values();

        $sourceOptions = MstScSource::orderBy('name')
            ->get(['id', 'name'])
            ->values();

        $themeOptions = Theme::with('goal:id,title')
            ->orderBy('id')
            ->get()
            ->map(function ($theme) {
                $goalTitle = $theme->goal?->title ?? 'No Pillar';
                $themeNum = $theme->theme_number ?? 'N/A';

                return [
                    'id' => $theme->id,
                    'name' => "[$goalTitle] #$themeNum - $theme->name",
                ];
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Index', [
            'compendiumItems' => $compendiumItems,
            'totalCompendiumItems' => $compendiumItems->count(),
            'initiativeOptions' => $initiativeOptions,
            'sourceOptions' => $sourceOptions,
            'themeOptions' => $themeOptions,
        ]);
    }

    private function scoreLabel(?int $score): string
    {
        return match ((int) $score) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => '-',
        };
    }
}
