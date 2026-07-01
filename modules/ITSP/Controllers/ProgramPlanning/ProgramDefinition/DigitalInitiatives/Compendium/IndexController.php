<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use Modules\ITSP\Models\MstScSource;
use Modules\ITSP\Models\Theme;
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

        $records = TrsScInitiative::with([
            'mstInitiatives:id,code,name,description,coe_id,business_unit,source',
            'mstInitiatives.coe:id,name',
            'mstInitiatives.organization:id,name,groub_id',
            'mstInitiatives.organization.groub:id,name',
            'mstInitiatives.sourceData:id,name,month,year,created_at',
            'sourceData:id,name,created_at',
        ])
            ->where('source_id', 1)
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

        $compendiumItems = $records
            ->map(function (TrsScInitiative $item) use ($rjppMap): array {
                $firstMst = $item->mstInitiatives->first();
                $source = $item->sourceData ?? $firstMst?->sourceData;

                $sourceCreatedAt = '-';
                if ($source) {
                    if (! empty($source->month) && ! empty($source->year)) {
                        $sourceCreatedAt = $this->getMonthName($source->month).' '.$source->year;
                    } elseif (! empty($source->created_at)) {
                        $sourceCreatedAt = $source->created_at->format('M Y');
                    }
                }

                $rjpp = (string) ($rjppMap->get($item->id, '-') ?? '-');

                $masterInitiatives = $item->mstInitiatives->map(function ($mi) {
                    $code = str_replace('#', '', $mi->code ?? '');
                    $name = $mi->name ?? '';
                    if ($code && $name) {
                        return "{$code} - {$name}";
                    }

                    return $code ?: ($name ?: null);
                })->filter()->implode(', ');

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $firstMst?->id,
                    'group' => $firstMst?->organization?->groub?->name ?? '-',
                    'master_initiatives' => $masterInitiatives ?: '-',
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

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->with([
                'taggings',
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'sourceData:id,name,month,year,created_at'
            ])
            ->orderBy('code')
            ->get()
            ->map(function ($mi) {
                $source = $mi->sourceData;
                $sourceCreatedAt = '-';
                if ($source) {
                    if (!empty($source->month) && !empty($source->year)) {
                        $sourceCreatedAt = $this->getMonthName($source->month) . ' ' . $source->year;
                    } elseif (!empty($source->created_at)) {
                        $sourceCreatedAt = $source->created_at->format('M Y');
                    }
                }

                return [
                    'id' => $mi->id,
                    'code' => $mi->code,
                    'name' => $mi->name,
                    'description' => $mi->description,
                    'coe' => $mi->coe?->name,
                    'project_owner' => $mi->organization?->name,
                    'group' => $mi->organization?->groub?->name,
                    'data_source' => $mi->sourceData?->name,
                    'data_source_created' => $sourceCreatedAt,
                    'taggings' => $mi->taggings,
                ];
            })
            ->values();

        $coeOptions = MstCoe::orderBy('name')->get(['id', 'name'])->values();

        $sourceOptions = MstScSource::orderBy('name')
            ->get(['id', 'name', 'month', 'year'])
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'month' => $this->getMonthName($s->month),
                'year' => $s->year,
            ])
            ->values();

        $themeOptions = Theme::with('goal:id,code,title')
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
                    'strategic_pillar' => $goalTitle,
                    'theme_code' => $themeNum,
                    'name' => $theme->name,
                    'label' => "[$goalCode - $goalTitle] #$themeNum - $theme->name",
                ];
            })
            ->values();

        $uniqueMasterInitiatives = $compendiumItems->pluck('master_initiatives')
            ->flatMap(fn ($mi) => explode(', ', $mi))
            ->filter(fn ($mi) => $mi !== '-')
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('modules/ITSP/ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Index', [
            'compendiumItems' => $compendiumItems,
            'totalCompendiumItems' => $compendiumItems->count(),
            'initiativeOptions' => $initiativeOptions,
            'coeOptions' => $coeOptions,
            'sourceOptions' => $sourceOptions,
            'themeOptions' => $themeOptions,
            'uniqueMasterInitiatives' => $uniqueMasterInitiatives,
        ]);
    }

    private function getMonthName($month): string
    {
        if (empty($month)) {
            return '';
        }

        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            return date('F', mktime(0, 0, 0, (int) $month, 10));
        }

        return (string) $month;
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
