<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Summary;

use App\Http\Controllers\Controller;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\DataSource;
use App\Models\Theme;
use App\Models\TrsMasterMilestone;
use App\Models\TrsOrganization;
use App\Models\TrsProject;
use App\Models\TrsScInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\TrsStatusImplementation;
use App\Models\TrsReviewSc;

class IndexController extends Controller
{
    public function __invoke(MstInitiative $initiative): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        // 0. Status Implementation
        $statusImplementations = TrsStatusImplementation::query()
            ->where('initiative_id', $initiative->id)
            ->latest()
            ->get();

        // 0.1 Summary Review Notes
        $summaryReviewNotes = TrsReviewSc::query()
            ->where('initiative_id', $initiative->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        // 1. Project Charter (DigitalInitiativeDocument)
        $projectCharter = TrsProject::query()
            ->where(function ($query) use ($initiative) {
                $query->whereHas('mappedInitiatives', function ($q) use ($initiative) {
                    $q->where('initiative_id', $initiative->id);
                })
                ->orWhere('code', sprintf('AUTO-MI-%d', $initiative->id))
                ->orWhere(function ($q) use ($initiative) {
                    $q->where('name', $initiative->name)
                      ->where('tipe_inisiative', (string) $initiative->tipe_initiative);
                });
            })
            ->with([
                'charter',
                'charters' => fn ($q) => $q->latest()->with('milestones'),
                'owner',
                'statusRef:id,name',
                'pcStatusImplementations',
                'mappedInitiatives.coe:id,name',
                'mappedInitiatives.organization:id,name,groub_id',
                'mappedInitiatives.organization.groub:id,name',
                'mappedInitiatives.sourceData:id,name,month,year,created_at',
                'mappedInitiatives.taggings.theme:id,idGoal,theme_number,name',
                'mappedInitiatives.taggings.theme.goal:id,code,title',
            ])
            ->first();

        // 2. Compendium and Appendix Data
        $compendiumInitiative = TrsScInitiative::query()
            ->where('source_id', 1)
            ->whereHas('mstInitiatives', function ($q) use ($initiative) {
                $q->where('initiative_id', $initiative->id);
            })
            ->with([
                'mstInitiatives:id,code,name',
                'scDetails' => fn ($q) => $q->latest('id'),
                'appendixes' => fn ($query) => $query->with([
                    'mstInitiatives:id,code,name',
                    'compendiums:id,usecase',
                    'scDetails' => fn ($q) => $q->latest('id')->limit(1),
                ])->limit(1),
            ])
            ->first();

        $compendiumData = null;
        $appendixData = null;

        if ($compendiumInitiative) {
            $compDetail = $compendiumInitiative->scDetails->first();
            
            $compendiumData = [
                'id' => (int) $compendiumInitiative->id,
                'initiative_ids' => $compendiumInitiative->mstInitiatives->pluck('id')->toArray(),
                'owner' => $compendiumInitiative->owner,
                'coe' => $compendiumInitiative->coe,
                'usecase' => $compendiumInitiative->usecase,
                'description' => $compendiumInitiative->description,
                'source_id' => $compendiumInitiative->source_id,
                'value' => ($compendiumInitiative->value === null || (int) $compendiumInitiative->value === 4) ? null : (int) $compendiumInitiative->value,
                'urgency' => ($compendiumInitiative->urgency === null || (int) $compendiumInitiative->urgency === 4) ? null : (int) $compendiumInitiative->value,
                'status' => $compendiumInitiative->status,
                'rjpp_tagging_ids' => $this->rjppTaggingIds((int) $compendiumInitiative->id),
                'detail_useCase_description' => $compDetail?->useCase_description ?? '',
                'current_situation' => $compDetail?->current_situation ?? '',
                'key_functionalities' => $compDetail?->key_functionalities ?? '',
                'value_detail' => $compDetail?->value_detail ?? '',
                'urgency_detail' => $compDetail?->urgency_detail ?? '',
                'ease_implementation' => $compDetail ? (int) $compDetail->ease_implementation : 4,
                'ease_detail' => $compDetail?->ease_detail ?? '',
                'resource_requirement' => $compDetail ? (int) $compDetail->resource_requirement : 4,
                'resource_detail' => $compDetail?->resource_detail ?? '',
                'interpendencies' => $compDetail?->interpendencies ?? '',
                'sign_by' => $compDetail?->sign_by ?? '',
            ];

            $appendixInitiative = $compendiumInitiative->appendixes->first();
            if ($appendixInitiative) {
                $appDetail = $appendixInitiative->scDetails->first();
                $appendixRjppIds = $this->rjppTaggingIds((int) $appendixInitiative->id);
                
                $signBy = $appDetail?->sign_by;
                if (is_string($signBy)) {
                    $signBy = json_decode($signBy, true) ?? [];
                }

                $appendixData = [
                    'id' => (int) $appendixInitiative->id,
                    'initiative_ids' => $appendixInitiative->mstInitiatives->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'compendium_ids' => $appendixInitiative->compendiums->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'usecase' => $appendixInitiative->usecase,
                    'owner' => $appendixInitiative->owner,
                    'coe' => $appendixInitiative->coe,
                    'value' => $appendixInitiative->value,
                    'urgency' => $appendixInitiative->urgency,
                    'rjpp_tagging_ids' => $appendixRjppIds,
                    'organization' => $appDetail?->organization,
                    'situation' => $appDetail?->situation,
                    'key_functionalities' => $appDetail?->key_functionalities,
                    'value_rationale' => $appDetail?->value_rationale,
                    'value_matrics' => $appDetail?->value_matrics,
                    'urgency_rationale' => $appDetail?->urgency_rationale,
                    'urgency_expected' => $appDetail?->urgency_expected,
                    'expected_q' => $appDetail?->expected_q,
                    'year_q' => $appDetail?->year_q,
                    'ease' => $appDetail?->ease,
                    'ease_rationale' => $appDetail?->ease_rationale,
                    'ease_detail' => $appDetail?->ease_detail,
                    'resource' => $appDetail?->resource,
                    'resource_rationale' => $appDetail?->resource_rationale,
                    'resource_retionale' => $appDetail?->resource_retionale,
                    'resource_detail' => $appDetail?->resource_detail,
                    'predecessor' => $appDetail?->predecessor,
                    'successor' => $appDetail?->successor,
                    'otherBU' => $appDetail?->otherBU,
                    'update_doc' => $appDetail?->update_doc,
                    'sign_by' => $signBy ?? [],
                    'description' => $appendixInitiative->description,
                ];
            }
        }

        // 3. Roadmap Data
        $milestones = TrsMasterMilestone::query()
            ->where('initiative_id', $initiative->id)
            ->with([
                'initiative:id,code,name,business_unit',
                'initiative.organization:id,name',
            ])
            ->orderBy('id')
            ->get()
            ->map(fn (TrsMasterMilestone $milestone): array => [
                'id' => (int) $milestone->id,
                'initiative_id' => (int) $milestone->initiative_id,
                'initiative_code' => trim((string) ($milestone->initiative?->code ?? '')),
                'initiative_name' => trim((string) ($milestone->initiative?->name ?? '')) !== '' ? trim((string) $milestone->initiative->name) : sprintf('Initiative #%d', $milestone->initiative_id),
                'organization_name' => trim((string) ($milestone->initiative?->organization?->name ?? '')) !== '' ? trim((string) $milestone->initiative?->organization?->name) : '-',
                'activity' => $milestone->activity,
                'startYear' => (int) $milestone->startYear,
                'startQ' => $this->normalizeQuarterLabel($milestone->startQ),
                'endYear' => (int) $milestone->endYear,
                'endQ' => $this->normalizeQuarterLabel($milestone->endQ),
                'version' => trim((string) ($milestone->version ?? '')),
            ])
            ->values();

        $startYear = 2024;
        $endYear = 2029;
        if ($milestones->isNotEmpty()) {
            $sY = (int) ($milestones->min('startYear') ?? 2024);
            $eY = (int) ($milestones->max('endYear') ?? 2029);
            $startYear = min($sY > 0 ? $sY : 2024, 2024);
            $endYear = max($eY >= $startYear ? $eY : $startYear, 2029);
        }

        // 4. Options
        $coeOptions = MstCoe::orderBy('name')->get(['id', 'name'])->values();
        $sourceOptions = DataSource::orderBy('name')->get(['id', 'name', 'month', 'year'])->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'month' => $this->getMonthName($s->month),
            'year' => $s->year,
        ])->values();
        
        $themeOptions = Theme::with('goal:id,code,title')
            ->orderBy('id')
            ->get()
            ->map(function (Theme $theme): array {
                $goalCode = $theme->goal?->code ?? '-';
                $goalTitle = $theme->goal?->title ?? 'No Pillar';
                $themeNum = $theme->theme_number ?? 'N/A';
                return [
                    'id' => (int) $theme->id,
                    'code' => $goalCode,
                    'strategic_pillar' => $goalTitle,
                    'theme_code' => $themeNum,
                    'name' => $theme->name,
                    'label' => "[$goalCode - $goalTitle] #$themeNum - $theme->name",
                ];
            })->values();

        $organizationOptions = TrsOrganization::orderBy('name')->get(['id', 'name'])->values();
        
        // Options used in MasterInitiative Mapping resolving.
        $initiativeOptionsQuery = MstInitiative::where('tipe_initiative', 1)
            ->with([
                'taggings' => fn ($q) => $q->whereNotNull('themes_id')->orWhereNotNull('goal'),
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'sourceData:id,name,month,year,created_at',
            ])
            ->orderBy('code')
            ->orderBy('name');
            
        // Limit options to only those found in form (for display optimization) if needed,
        // Since it's only read-only, we provide full options or just the required ones.
        $initiativeOptions = $initiativeOptionsQuery->get()->map(function (MstInitiative $init): array {
            return [
                'id' => (int) $init->id,
                'code' => $init->code,
                'name' => $init->name,
                'description' => $init->description,
                'group' => $init->organization?->groub?->name ?? '-',
                'project_owner' => $init->organization?->name ?? '-',
                'coe' => $init->coe?->name ?? '-',
                'taggings' => $init->taggings,
            ];
        })->values();


        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Summary/Index', [
            'initiativeMaster' => $initiative->load([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'sourceData:id,name,month,year,created_at',
                'taggings' => fn ($q) => $q->whereNotNull('themes_id')->orWhereNotNull('goal'),
                'taggings.theme:id,idGoal,theme_number,name',
                'taggings.theme.goal:id,code,title',
                'statusHistory' => fn ($q) => $q->latest('tanggal'),
            ]),
            'projectCharter' => $projectCharter,
            'compendiumData' => $compendiumData,
            'appendixData' => $appendixData,
            'roadmapItems' => $milestones,
            'roadmapStartYear' => $startYear,
            'roadmapEndYear' => $endYear,
            
            'coeOptions' => $coeOptions,
            'sourceOptions' => $sourceOptions,
            'themeOptions' => $themeOptions,
            'organizationOptions' => $organizationOptions,
            'initiativeOptions' => $initiativeOptions,
            'statusImplementations' => $statusImplementations,
            'summaryReviewNotes' => $summaryReviewNotes,
        ]);
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

    private function normalizeQuarterLabel(mixed $value): string
    {
        if (preg_match('/Q?([1-4])/', strtoupper(trim((string) $value)), $matches)) {
            return sprintf('Q%d', (int) $matches[1]);
        }
        return 'Q1';
    }

    private function getMonthName($month): string
    {
        if (empty($month)) return '';
        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            return date('F', mktime(0, 0, 0, (int) $month, 10));
        }
        return (string) $month;
    }
}
