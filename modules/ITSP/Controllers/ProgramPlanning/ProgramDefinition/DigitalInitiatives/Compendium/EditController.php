<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Modules\ITSP\Models\MstScSource;
use Modules\ITSP\Models\Theme;
use Modules\ITSP\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class EditController extends Controller
{
    public function __invoke(TrsScInitiative $scInitiative): Response|RedirectResponse
    {

        $scInitiative->load([
            'mstInitiatives:id,code,name',
            'scDetails' => fn ($query) => $query->latest('id'),
            // Load appendix initiatives via trs_sc_dependency
            'appendixes' => fn ($query) => $query->with([
                'mstInitiatives:id,code,name',
                'compendiums:id,usecase',
                'scDetails' => fn ($q) => $q->latest('id')->limit(1),
            ])->limit(1),
        ]);

        $detail = $scInitiative->scDetails->first();

        // Get the first linked appendix initiative
        $appendixInitiative = $scInitiative->appendixes->first();
        $appendixDetail = $appendixInitiative?->scDetails->first();

        // Build appendix RJPP theme IDs
        $appendixRjppIds = $appendixInitiative
            ? DB::table('trs_rjpp')
                ->where('sc_id', $appendixInitiative->id)
                ->pluck('theme_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : [];

        // Decode sign_by JSON stored as string
        $signBy = $appendixDetail?->sign_by;
        if (is_string($signBy)) {
            $signBy = json_decode($signBy, true) ?? [];
        }

        return Inertia::render('modules/ITSP/ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Show', [
            'compendium' => [
                'id' => (int) $scInitiative->id,
                'initiative_ids' => $scInitiative->mstInitiatives->pluck('id')->toArray(),
                'owner' => $scInitiative->owner,
                'coe' => $scInitiative->coe,
                'usecase' => $scInitiative->usecase,
                'description' => $scInitiative->description,
                'source_id' => $scInitiative->source_id,
                'value' => ($scInitiative->value === null || (int) $scInitiative->value === 4) ? null : (int) $scInitiative->value,
                'urgency' => ($scInitiative->urgency === null || (int) $scInitiative->urgency === 4) ? null : (int) $scInitiative->urgency,
                'status' => $scInitiative->status,
                'rjpp_tagging_ids' => $this->rjppTaggingIds((int) $scInitiative->id),
                'detail_useCase_description' => $detail?->useCase_description ?? '',
                'current_situation' => $detail?->current_situation ?? '',
                'key_functionalities' => $detail?->key_functionalities ?? '',
                'value_detail' => $detail?->value_detail ?? '',
                'urgency_detail' => $detail?->urgency_detail ?? '',
                'ease_implementation' => $detail ? (int) $detail->ease_implementation : 4,
                'ease_detail' => $detail?->ease_detail ?? '',
                'resource_requirement' => $detail ? (int) $detail->resource_requirement : 4,
                'resource_detail' => $detail?->resource_detail ?? '',
                'interpendencies' => $detail?->interpendencies ?? '',
                'sign_by' => $detail?->sign_by ?? '',
            ],
            // Full appendix data from the linked initiative
            'appendix' => $appendixInitiative ? [
                'id' => (int) $appendixInitiative->id,
                'initiative_ids' => $appendixInitiative->mstInitiatives->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                'compendium_ids' => $appendixInitiative->compendiums->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                'usecase' => $appendixInitiative->usecase,
                'owner' => $appendixInitiative->owner,
                'coe' => $appendixInitiative->coe,
                'value' => $appendixInitiative->value,
                'urgency' => $appendixInitiative->urgency,
                'rjpp_tagging_ids' => $appendixRjppIds,
                'organization' => $appendixDetail?->organization,
                'situation' => $appendixDetail?->situation,
                'key_functionalities' => $appendixDetail?->key_functionalities,
                'value_rationale' => $appendixDetail?->value_rationale,
                'value_matrics' => $appendixDetail?->value_matrics,
                'urgency_rationale' => $appendixDetail?->urgency_rationale,
                'urgency_expected' => $appendixDetail?->urgency_expected,
                'ease' => $appendixDetail?->ease,
                'ease_rationale' => $appendixDetail?->ease_rationale,
                'ease_detail' => $appendixDetail?->ease_detail,
                'resource' => $appendixDetail?->resource,
                'resource_rationale' => $appendixDetail?->resource_rationale,
                'resource_retionale' => $appendixDetail?->resource_retionale,
                'resource_detail' => $appendixDetail?->resource_detail,
                'predecessor' => $appendixDetail?->predecessor,
                'successor' => $appendixDetail?->successor,
                'otherBU' => $appendixDetail?->otherBU,
                'update_doc' => $appendixDetail?->update_doc,
                'sign_by' => $signBy ?? [],
                'description' => $appendixInitiative->description,
            ] : null,
            'initiativeOptions' => $this->initiativeOptions(),
            'coeOptions' => \App\Models\MstCoe::orderBy('name')->get(['id', 'name'])->values(),
            'sourceOptions' => MstScSource::orderBy('name')->get(['id', 'name', 'month', 'year'])->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'month' => $this->getMonthName($s->month),
                'year' => $s->year,
            ])->values(),
            'themeOptions' => $this->themeOptions(),
            'compendiumOptions' => $this->compendiumOptions(),
            'organizationOptions' => \App\Models\TrsOrganization::orderBy('name')->get(['id', 'name'])->values(),
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
                    'initiative_ids' => $item->mstInitiatives->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                ];
            })
            ->values();
    }

    private function initiativeOptions()
    {
        return MstInitiative::where('tipe_initiative', 1)
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

    private function rjppTaggingIds(int $scInitiativeId): array
    {
        $scColumn = Schema::hasColumn('trs_rjpp', 'sc_id') ? 'sc_id' : 'digital_id';

        return DB::table('trs_rjpp')
            ->where($scColumn, $scInitiativeId)
            ->pluck('theme_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
