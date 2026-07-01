<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Modules\ITSP\Models\MstScSource;
use Modules\ITSP\Models\Theme;
use Modules\ITSP\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->with([
                'taggings',
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'sourceData:id,name,month,year,created_at',
            ])
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'description', 'business_unit', 'coe_id', 'source'])
            ->map(function (MstInitiative $initiative): array {
                $source = $initiative->sourceData;

                $sourceCreated = '-';
                if ($source) {
                    if (! empty($source->month) && ! empty($source->year)) {
                        $sourceCreated = $this->getMonthName($source->month).' '.$source->year;
                    } elseif (! empty($source->created_at)) {
                        $sourceCreated = $source->created_at->format('M Y');
                    }
                }

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'group' => $initiative->organization?->groub?->name ?? '-',
                    'project_owner' => $initiative->organization?->name ?? '-',
                    'coe' => $initiative->coe?->name ?? '-',
                    'data_source' => $source?->name ?? '-',
                    'data_source_created' => $sourceCreated,
                    'taggings' => $initiative->taggings,
                ];
            })
            ->values();

        return Inertia::render('modules/ITSP/ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Show', [
            'initiativeOptions' => $initiativeOptions,
            'coeOptions' => \App\Models\MstCoe::orderBy('name')->get(['id', 'name'])->values(),
            'sourceOptions' => MstScSource::orderBy('name')->get(['id', 'name', 'month', 'year'])->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'month' => $this->getMonthName($s->month),
                'year' => $s->year,
            ])->values(),
            'themeOptions' => $this->themeOptions(),
            'compendiumOptions' => $this->compendiumOptions(),
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

    private function compendiumOptions()
    {
        return TrsScInitiative::query()
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
                    'strategic_pillar' => $goalTitle,
                    'theme_code' => $themeNum,
                    'name' => $theme->name,
                    'label' => "[$goalCode - $goalTitle] #$themeNum - $theme->name",
                ];
            })
            ->values();
    }
}
