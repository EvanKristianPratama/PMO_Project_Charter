<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Mapping;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\MstScSource;
use App\Models\Theme;
use App\Models\TrsScInitiative;
use App\Models\TrsOrganization;
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
            'compendiums:id,usecase,description',
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

        $noMasterCompendiumItems = TrsScInitiative::with([
            'mstInitiatives:id,code,name',
        ])
            ->where('source_id', 1)
            ->orderBy('id')
            ->get(['id', 'usecase', 'description'])
            ->filter(fn (TrsScInitiative $item) => $item->mstInitiatives->isEmpty())
            ->map(function (TrsScInitiative $item): array {
                $firstMst = $item->mstInitiatives->first();

                return [
                    'id' => "comp-only-{$item->id}",
                    'master_initiative' => $firstMst ? ($firstMst->code ? "{$firstMst->code} - {$firstMst->name}" : $firstMst->name) : '-',
                    'use_case_compendium' => $item->usecase ?: '-',
                    'use_case_compendium_description' => $item->description ?: '-',
                    'use_case_appendix' => '-',
                    'use_case_appendix_description' => '-',
                ];
            })
            ->values();

        $appendixItems = $records
            ->map(function (TrsScInitiative $item): array {
                $masterInitiatives = $item->mstInitiatives
                    ->map(function ($mi) {
                        $code = $mi->code ?? '';
                        $name = $mi->name ?? '';
                        if ($code && $name) {
                            return "{$code} - {$name}";
                        }

                        return $code ?: ($name ?: null);
                    })
                    ->filter()
                    ->values();
                $masterLabel = $masterInitiatives->implode(', ');

                $compendiums = $item->compendiums->map(function ($compendium) {
                    $label = trim((string) ($compendium->usecase ?? ''));
                    return $label !== '' ? $label : "Compendium #{$compendium->id}";
                })->filter()->implode(', ');

                $compendiumDescriptions = $item->compendiums->map(function ($compendium) {
                    return $compendium->description ?: '-';
                })->filter(fn($d) => $d !== '-')->implode(', ');

                return [
                    'id' => (int) $item->id,
                    'master_initiative' => $masterLabel !== '' ? $masterLabel : '-',
                    'use_case_compendium' => $compendiums ?: '-',
                    'use_case_compendium_description' => $compendiumDescriptions ?: '-',
                    'use_case_appendix' => $item->usecase ?: '-',
                    'use_case_appendix_description' => $item->description ?: '-',
                ];
            })
            ->values();

        $uniqueCompendiums = $appendixItems->pluck('use_case_compendium')
            ->flatMap(fn ($compendium) => explode(', ', $compendium))
            ->filter(fn ($compendium) => $compendium !== '-')
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Mapping/Index', [
            'appendixItems' => $appendixItems,
            'noMasterCompendiumItems' => $noMasterCompendiumItems,
            'totalAppendixItems' => $appendixItems->count(),
            'uniqueCompendiums' => $uniqueCompendiums,
            'compendiumOptions' => $this->compendiumOptions(),
            'initiativeOptions' => $this->initiativeOptions(),
            'coeOptions' => MstCoe::orderBy('name')->get(['id', 'name'])->values(),
            'sourceOptions' => $this->sourceOptions(),
            'themeOptions' => $this->themeOptions(),
            'organizationOptions' => TrsOrganization::orderBy('name')->get(['id', 'name'])->values(),
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

    private function compendiumOptions()
    {
        return TrsScInitiative::query()
            ->where('source_id', 1)
            ->with(['mstInitiatives:id,code,name'])
            ->orderBy('id')
            ->get(['id', 'owner', 'usecase', 'description'])
            ->map(function (TrsScInitiative $item): array {
                $firstInitiative = $item->mstInitiatives->first();
                $label = trim((string) ($item->usecase ?: ($firstInitiative?->name ?? '-')));

                return [
                    'id' => (int) $item->id,
                    'label' => $label !== '' ? $label : '-',
                    'description' => $item->description ?: '-',
                    'master_initiative' => $firstInitiative ? ($firstInitiative->code ? "{$firstInitiative->code} - {$firstInitiative->name}" : $firstInitiative->name) : '-',
                    'initiative_ids' => $item->mstInitiatives->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
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

    private function sourceOptions()
    {
        return MstScSource::orderBy('name')
            ->get(['id', 'name', 'month', 'year'])
            ->map(fn ($source) => [
                'id' => $source->id,
                'name' => $source->name,
                'month' => $this->getMonthName($source->month),
                'year' => $source->year,
            ])
            ->values();
    }
}
