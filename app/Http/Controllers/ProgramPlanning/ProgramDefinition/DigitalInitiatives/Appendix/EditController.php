<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\Theme;
use App\Models\TrsOrganization;
use App\Models\TrsScInitiative;
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
            'compendiums:id,usecase',
            'scDetails' => fn ($query) => $query->latest('id'),
        ]);

        $detail = $scInitiative->scDetails->first();

        $rjppScKey = Schema::hasColumn('trs_rjpp', 'sc_id') ? 'sc_id' : 'digital_id';

        $rjppTaggingIds = DB::table('trs_rjpp')
            ->where($rjppScKey, $scInitiative->id)
            ->pluck('theme_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $signBy = $detail?->sign_by;
        if (is_string($signBy)) {
            $signBy = json_decode($signBy, true) ?? [];
        }

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Appendix/Show', [
            'appendix' => [
                'id' => (int) $scInitiative->id,
                'initiative_ids' => $scInitiative->mstInitiatives->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                'compendium_ids' => $scInitiative->compendiums->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                'owner' => $scInitiative->owner,
                'coe' => $scInitiative->coe,
                'usecase' => $scInitiative->usecase,
                'description' => $scInitiative->description,
                'value' => ($scInitiative->value === null || (int) $scInitiative->value === 4) ? null : (int) $scInitiative->value,
                'urgency' => ($scInitiative->urgency === null || (int) $scInitiative->urgency === 4) ? null : (int) $scInitiative->urgency,
                'status' => $scInitiative->status,
                'rjpp_tagging_ids' => $rjppTaggingIds,
                'organization' => $detail?->organization,
                'update_doc' => $detail?->update_doc,
                'situation' => $detail?->situation,
                'key_functionalities' => $detail?->key_functionalities,
                'value_rationale' => $detail?->value_rationale,
                'value_matrics' => $detail?->value_matrics,
                'urgency_rationale' => $detail?->urgency_rationale,
                'urgency_expected' => $detail?->urgency_expected,
                'ease' => $detail?->ease,
                'ease_rationale' => $detail?->ease_rationale,
                'ease_detail' => $detail?->ease_detail,
                'resource' => $detail?->resource,
                'resource_rationale' => $detail?->resource_rationale,
                'resource_detail' => $detail?->resource_detail,
                'resource_retionale' => $detail?->resource_retionale ?? null,
                'predecessor' => $detail?->predecessor,
                'successor' => $detail?->successor,
                'otherBU' => $detail?->otherBU,
                'sign_by' => $signBy ?? [],
            ],
            'appendixOptions' => $this->appendixOptions(),
            'initiativeOptions' => $this->initiativeOptions(),
            'compendiumOptions' => $this->compendiumOptions(),
            'coeOptions' => MstCoe::orderBy('name')->get(['id', 'name'])->values(),
            'themeOptions' => $this->themeOptions(),
            'organizationOptions' => TrsOrganization::orderBy('name')->get(['id', 'name'])->values(),
        ]);
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

    private function appendixOptions()
    {
        return TrsScInitiative::query()
            ->where('source_id', 2)
            ->with(['mstInitiatives:id,code,name'])
            ->orderBy('id')
            ->get(['id', 'owner', 'usecase', 'description'])
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
}
