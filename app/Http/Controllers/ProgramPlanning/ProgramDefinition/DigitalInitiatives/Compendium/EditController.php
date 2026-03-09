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

class EditController extends Controller
{
    public function __invoke(TrsScInitiative $scInitiative): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $scInitiative->load([
            'mstInitiatives:id,code,name',
            'scDetails' => fn ($query) => $query->latest('id'),
        ]);

        $detail = $scInitiative->scDetails->first();

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name'])
            ->map(fn (MstInitiative $initiative) => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
            ])
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Show', [
            'compendium' => [
                'id' => (int) $scInitiative->id,
                'initiative_ids' => $scInitiative->mstInitiatives->pluck('id')->toArray(),
                'owner' => $scInitiative->owner,
                'usecase' => $scInitiative->usecase,
                'description' => $scInitiative->description,
                'source_id' => $scInitiative->source_id,
                'value' => (int) $scInitiative->value,
                'urgency' => (int) $scInitiative->urgency,
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
            'initiativeOptions' => $initiativeOptions,
            'sourceOptions' => MstScSource::orderBy('name')->get(['id', 'name'])->values(),
            'themeOptions' => $this->themeOptions(),
            'compendiumOptions' => $this->compendiumOptions(),
        ]);
    }

    private function compendiumOptions()
    {
        return TrsScInitiative::query()
            ->with(['mstInitiatives:id,code,name'])
            ->whereHas('mstInitiatives')
            ->orderBy('id')
            ->get(['id', 'owner', 'usecase'])
            ->map(function (TrsScInitiative $item): array {
                $firstInitiative = $item->mstInitiatives->first();
                $code = trim((string) ($firstInitiative?->code ?? ''));
                $name = trim((string) ($item->usecase ?: ($firstInitiative?->name ?? '-')));
                $owner = trim((string) ($item->owner ?? ''));

                $label = ($code !== '' ? "[{$code}] " : '') . ($name !== '' ? $name : '-');
                if ($owner !== '') {
                    $label .= " - {$owner}";
                }

                return [
                    'id' => (int) $item->id,
                    'label' => $label,
                ];
            })
            ->values();
    }

    private function themeOptions()
    {
        return Theme::with('goal:id,title')
            ->orderBy('id')
            ->get()
            ->map(function (Theme $theme): array {
                $goalTitle = $theme->goal?->title ?? 'No Pillar';
                $themeNum = $theme->theme_number ?? 'N/A';

                return [
                    'id' => (int) $theme->id,
                    'name' => "[$goalTitle] #$themeNum - $theme->name",
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
