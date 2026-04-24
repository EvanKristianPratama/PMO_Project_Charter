<?php

namespace App\Services\StrategicHouse;

use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use App\Services\StrategicHouse\InitiativeRelation\InitiativeRelationService;
use App\Services\StrategicHouse\InitiativeSupport\InitiativeSupportService;
use App\Services\StrategicHouse\RoadMap\ItInitiativeRoadmapService;
use App\Services\StrategicHouse\StrategicPillars\StrategicPillarPageService;
use App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use App\Services\StrategicHouse\MapTechnology\MapTechnologyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class StrategicHousePageService
{
    private const DEFAULT_INITIATIVE_TYPE = 1;

    private const TECHNOLOGY_COE_CONFIG = [
        ['name' => 'IoT', 'label' => 'IoT', 'tone' => 'sky'],
        ['name' => 'Cloud & Advanced Computing', 'label' => 'Advance Cloud', 'tone' => 'indigo'],
        ['name' => 'RPA', 'label' => 'RPA', 'tone' => 'cyan'],
        ['name' => 'Robotics', 'label' => 'Robotics', 'tone' => 'slate'],
        ['name' => 'AI/Analytics', 'label' => 'AI / Adv. Analytics', 'tone' => 'amber'],
    ];

    private const STRATEGY_COE_CONFIG = [
        ['name' => 'User Interface and Experience', 'label' => 'User Interface', 'tone' => 'sky', 'description_lines' => ['Memastikan', 'standarisasi UI/UX', 'pada seluruh aplikasi']],
        ['name' => 'Integration and Automation', 'label' => 'Integration and Automation', 'tone' => 'blue', 'description_lines' => ['Meningkatkan', 'interaksi sistem di', 'seluruh holding,', 'subholding, dan APFS']],
        ['name' => 'Business Application System', 'label' => 'Business Application System', 'tone' => 'indigo', 'description_lines' => ['Merasionalisasi dan', 'memodernisasi', 'aplikasi legacy,', 'termasuk membangun', 'groupwide ERP']],
        ['name' => 'Infrastructure', 'label' => 'Infrastructure', 'tone' => 'cyan', 'description_lines' => ['Membangun', 'infrastruktur best-in-', 'class untuk', 'mendukung', 'peningkatan', 'kompleksitas use case', 'digital']],
        ['name' => 'Data and Analytics', 'label' => 'Data and Analytics', 'tone' => 'emerald', 'description_lines' => ['AI platform;', 'Memastikan', 'ketersediaan dan', 'keandalan data untuk', 'mendukung use case', 'digital']],
        ['name' => 'Cybersecurity', 'label' => 'Cybersecurity', 'tone' => 'slate', 'description_lines' => ['Memperkuat kesiapan', 'menghadapi cyber', 'threats yang terus', 'meningkat']],
    ];

    private const FOUNDATION_COE_CONFIG = ['name' => 'People, Process and Technology', 'label' => 'People, Process and Technology', 'tone' => 'foundation'];
    private const ARCHITECTURE_COE_CONFIG = ['name' => 'Overall Architecture', 'label' => 'Overall Architecture', 'tone' => 'architecture'];
    private const TBC_COE_CONFIG = ['name' => 'TBC', 'label' => 'TBC', 'tone' => 'support'];

    public function __construct(
        protected ItBuildingBlockService $itBuildingBlockService,
        protected BusinessStrategyService $businessStrategyService,
        protected InitiativeSupportService $initiativeSupportService,
        protected StrategicPillarPageService $strategicPillarPageService,
        protected InitiativeRelationService $initiativeRelationService,
        protected ItInitiativeRoadmapService $itInitiativeRoadmapService,
        protected MasterMilestonePageService $digitalRoadmapPageService,
        protected MapTechnologyService $mapTechnologyService
    ) {}

    public function getPageProps(array $filters = [], array $selectedProps = []): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $initiativeType = $normalizedFilters['initiative_type'];
        $pilarId = (string) ($filters['pilar'] ?? '1');

        // 1. Always available lightweight data
        $coes = Cache::remember('sh_coes_fixed', 3600, fn() => MstCoe::query()->select('id', 'name')->orderBy('id')->get());
        $dualGrowthGoals = Cache::remember('sh_dual_goals_fixed', 3600, fn() => $this->getDualGrowthGoals());
        $roofSection = Cache::remember('sh_roof_fixed', 3600, fn() => $this->getRoofSection());

        $mappingData = null;
        $businessStrategyProps = null;
        $initiativeSupportProps = null;
        $mapTechnologyProps = null;
        $strategicPillarProps = null;
        $initiativeRelationProps = null;
        $itRoadmapProps = null;
        $digitalRoadmapProps = null;

        $loadMappingData = function () use (&$mappingData, $coes, $initiativeType, $normalizedFilters): array {
            return $mappingData ??= $this->getMappingData($coes, $initiativeType, $normalizedFilters['show_empty']);
        };

        $loadBusinessStrategyProps = function () use (&$businessStrategyProps): array {
            return $businessStrategyProps ??= $this->businessStrategyService->getPageProps($this->getConsolidatedInitiatives());
        };

        $loadInitiativeSupportProps = function () use (&$initiativeSupportProps): array {
            return $initiativeSupportProps ??= $this->initiativeSupportService->getPageProps();
        };

        $loadMapTechnologyProps = function () use (&$mapTechnologyProps): array {
            return $mapTechnologyProps ??= $this->mapTechnologyService->getPageProps();
        };

        $loadStrategicPillarProps = function () use (&$strategicPillarProps, $filters, $pilarId, $initiativeType): array {
            return $strategicPillarProps ??= $this->strategicPillarPageService->getPageProps($filters['goal_id'] ?? null, $filters['org_id'] ?? null, $pilarId, $initiativeType, $this->getConsolidatedInitiatives());
        };

        $loadInitiativeRelationProps = function () use (&$initiativeRelationProps): array {
            return $initiativeRelationProps ??= $this->initiativeRelationService->getIndexProps();
        };

        $loadItRoadmapProps = function () use (&$itRoadmapProps): array {
            return $itRoadmapProps ??= $this->itInitiativeRoadmapService->getPageProps();
        };

        $loadDigitalRoadmapPageProps = function () use (&$digitalRoadmapProps): array {
            return $digitalRoadmapProps ??= $this->digitalRoadmapPageService->getIndexPageProps();
        };

        $baseProps = [
            'filters' => $normalizedFilters,
            'page' => [
                'title' => 'Strategic House',
                'headline' => 'Pertamina Group Dual Growth Strategy',
                'visionTitle' => 'Visi Pertamina IT',
                'visionText' => 'Meningkatkan peranan IT dari business enabler menjadi strategic value creator, mendorong transformasi digital untuk mendukung ambisi dual growth Pertamina Group.',
                'initiativeLabel' => $initiativeType === 2 ? 'IT transformation initiatives' : 'Digital transformation initiatives',
                'grandStrategyTitle' => 'Grand IT Strategy',
                'grandStrategyText' => 'Single source of truth for groupwide IT reference architecture',
            ],
            'roofSection' => $roofSection,
            'focusBands' => $this->getFocusBands($dualGrowthGoals),
            'coeOptions' => $coes->map(fn($coe) => ['id' => (int)$coe->id, 'name' => $coe->name])->sortBy('name')->values(),
            'statusPeriods' => Cache::remember('sh_periods', 3600, fn() => $this->itBuildingBlockService->getStatusPeriods()),
        ];

        $heavyProps = [
            'summary' => fn() => $loadMappingData()['summary'],
            'technologyCards' => fn() => $loadMappingData()['technologyCards'],
            'strategyCards' => fn() => $loadMappingData()['strategyCards'],
            'foundationCard' => fn() => $loadMappingData()['foundationCard'],
            'architectureCard' => fn() => $loadMappingData()['architectureCard'],
            'tbcCard' => fn() => $loadMappingData()['tbcCard'],
            'unassignedInitiatives' => fn() => $loadMappingData()['unassignedInitiatives'],

            'businessStrategyPage' => fn() => $loadBusinessStrategyProps()['page'],
            'businessStrategySummary' => fn() => $loadBusinessStrategyProps()['summary'],
            'businessStrategyHeaderGoals' => fn() => $loadBusinessStrategyProps()['headerGoals'],
            'businessStrategyEnablerGoals' => fn() => $loadBusinessStrategyProps()['enablerGoals'],
            'businessStrategyGroups' => fn() => $loadBusinessStrategyProps()['groups'],
            'businessStrategyColumns' => fn() => $loadBusinessStrategyProps()['strategyColumns'],
            'businessStrategyOrganizationOptions' => fn() => $loadBusinessStrategyProps()['organizationOptions'],

            'dualGrowthGoals' => fn() => $dualGrowthGoals,
            'digitalInitiativeOptions' => fn() => $this->itBuildingBlockService->getDigitalInitiativeOptions($this->getConsolidatedInitiatives()),
            'itBuildingBlockMatrix' => fn() => $this->itBuildingBlockService->getGroupedMappings(),
            'itInitiativeOptions' => fn() => $this->itBuildingBlockService->getItInitiativeOptions($this->getConsolidatedInitiatives()),

            'initiativeSupportGroups' => fn() => $loadInitiativeSupportProps()['groups'],
            'initiativeSupportDigitalOptions' => fn() => $loadInitiativeSupportProps()['digitalInitiativeOptions'],
            'initiativeSupportItOptions' => fn() => $loadInitiativeSupportProps()['itInitiativeOptions'],

            'mapTechnologies' => fn() => $loadMapTechnologyProps()['mapTechnologies'],
            'mapTechnologyCoeOptions' => fn() => $loadMapTechnologyProps()['coeOptions'],
            'mapTechnologyInitiativeOptions' => fn() => $loadMapTechnologyProps()['initiativeOptions'],

            'strategicPillars' => fn() => $loadStrategicPillarProps()['strategicPillars'],
            'taggings' => fn() => $loadStrategicPillarProps()['taggings'],
            'allGoals' => fn() => $loadStrategicPillarProps()['allGoals'],
            'allOrganizations' => fn() => $loadStrategicPillarProps()['allOrganizations'],
            'allInitiatives' => fn() => $loadStrategicPillarProps()['allInitiatives'],
            'allThemes' => fn() => $loadStrategicPillarProps()['allThemes'],
            'matrixInitiatives' => fn() => $loadStrategicPillarProps()['matrixInitiatives'],
            'pilarOptions' => fn() => $loadStrategicPillarProps()['pilarOptions'],
            'pillarFilters' => fn() => $loadStrategicPillarProps()['filters'],

            'mstInitiatives' => fn() => $loadInitiativeRelationProps()['mstInitiatives'],
            'initiativeRelations' => fn() => $loadInitiativeRelationProps()['initiativeRelations'],
            'modelRelationOptions' => fn() => $loadInitiativeRelationProps()['modelRelationOptions'],
            'typeRelationOptions' => fn() => $loadInitiativeRelationProps()['typeRelationOptions'],

            'itRoadmapGroups' => fn() => $loadItRoadmapProps()['groups'],
            'itRoadmapStartYear' => fn() => $loadItRoadmapProps()['startYear'],
            'itRoadmapEndYear' => fn() => $loadItRoadmapProps()['endYear'],
            'itRoadmapTotalCount' => fn() => $loadItRoadmapProps()['totalCount'],
            'itRoadmapMilestoneTypeOptions' => fn() => $loadItRoadmapProps()['milestoneTypeOptions'],

            'digitalRoadmapGroups' => fn() => $this->buildDigitalRoadmapGroups($loadDigitalRoadmapPageProps()['roadmapItems'] ?? []),
            'digitalRoadmapStartYear' => fn() => $loadDigitalRoadmapPageProps()['startYearRange'],
            'digitalRoadmapEndYear' => fn() => $loadDigitalRoadmapPageProps()['endYearRange'],
        ];

        if (empty($selectedProps)) {
            return array_merge($baseProps, $heavyProps);
        }

        $selectedHeavyProps = array_intersect_key($heavyProps, array_flip($selectedProps));

        return array_merge($baseProps, $selectedHeavyProps);
    }

    private function getMappingData(Collection $coes, int $initiativeType, bool $showEmpty): array
    {
        $allInitiatives = $this->getConsolidatedInitiatives();
        $clonedCoes = $coes->map(fn($c) => clone $c);
        foreach ($clonedCoes as $coe) {
            $coe->setRelation('initiatives', $allInitiatives->where('coe_id', $coe->id)->values());
        }
        $coeCatalog = $this->filterCoeCatalogByType($clonedCoes, $initiativeType);
        $itCoeCatalog = ($initiativeType === 2) ? $coeCatalog : $this->filterCoeCatalogByType($clonedCoes, 2);

        return [
            'technologyCards' => $this->buildSectionCards($coeCatalog, self::TECHNOLOGY_COE_CONFIG, $showEmpty),
            'strategyCards' => $this->buildSectionCards($itCoeCatalog, self::STRATEGY_COE_CONFIG, $showEmpty),
            'foundationCard' => $this->buildSingleCard($itCoeCatalog, self::FOUNDATION_COE_CONFIG),
            'architectureCard' => $this->buildSingleCard($itCoeCatalog, self::ARCHITECTURE_COE_CONFIG),
            'tbcCard' => $this->buildSingleCard($itCoeCatalog, self::TBC_COE_CONFIG),
            'unassignedInitiatives' => $allInitiatives->where('tipe_initiative', $initiativeType)->whereNull('coe_id')->map(fn($i) => $this->mapInitiativeForSummary($i))->values()->all(),
            'summary' => $this->buildSummary(
                $this->buildSectionCards($coeCatalog, self::TECHNOLOGY_COE_CONFIG, true),
                $this->buildSectionCards($itCoeCatalog, self::STRATEGY_COE_CONFIG, true),
                $this->buildSingleCard($itCoeCatalog, self::FOUNDATION_COE_CONFIG),
                $this->buildSingleCard($itCoeCatalog, self::ARCHITECTURE_COE_CONFIG),
                $this->buildSingleCard($itCoeCatalog, self::TBC_COE_CONFIG),
                []
            ),
        ];
    }

    private function getConsolidatedInitiatives(): Collection
    {
        return Cache::remember('sh_consolidated_initiatives_v6', 3600, function() {
            return MstInitiative::query()->with(['latestStatus', 'organization', 'latestStatusImplementation', 'statusImplementations', 'sourceData', 'taggings', 'mappedProjects'])->orderBy('code')->get();
        });
    }

    private function normalizeFilters(array $filters): array
    {
        $initiativeType = (int) ($filters['initiative_type'] ?? self::DEFAULT_INITIATIVE_TYPE);
        return [
            'initiative_type' => in_array($initiativeType, [1, 2], true) ? $initiativeType : self::DEFAULT_INITIATIVE_TYPE,
            'show_empty' => (bool) ($filters['show_empty'] ?? true),
            'pilar' => $filters['pilar'] ?? null,
            'view' => $filters['view'] ?? 'mapping',
        ];
    }

    private function mapInitiativeForSummary(MstInitiative $initiative): array
    {
        $status = $this->normalizeStatus($initiative->latestPlanningStatusValue());
        return [
            'id' => (int) $initiative->id,
            'code' => $initiative->code,
            'name' => $initiative->name,
            'description' => $initiative->description,
            'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
            'business_unit_id' => ! is_null($initiative->business_unit) ? (int) $initiative->business_unit : null,
            'business_unit_name' => trim((string) ($initiative->organization?->name ?? '')),
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'implementation_status' => $initiative->latestStatusImplementation?->review_status,
            'statuses' => collect($initiative->statusImplementations ?? [])->map(fn($s) => ['start' => $s->start, 'end' => $s->end, 'year' => (int) $s->year, 'status' => $s->review_status])->values()->all(),
            'source' => !is_null($initiative->source) ? (int) $initiative->source : null,
            'source_name' => $initiative->sourceData?->name,
        ];
    }

    private function buildDigitalRoadmapGroups(array|Collection $roadmapItems): array
    {
        $globalNumber = 1; $coeOrder = ['AI / Adv. Analytics', 'Advance Cloud', 'IoT', 'RPA', 'CoE Not Identified'];
        $normalizedItems = collect($roadmapItems)->filter(fn (mixed $item): bool => is_array($item))->map(function (array $item): array { $item['coe_name'] = $this->normalizeDigitalRoadmapCoeName((string) ($item['coe_name'] ?? '')); return $item; });
        $byCoe = $normalizedItems->groupBy(fn (array $item): string => (string) ($item['coe_name'] ?? 'CoE Not Identified'));
        return collect($coeOrder)->values()->map(function (string $coeName, int $index) use (&$globalNumber, $byCoe): array {
            $items = collect($byCoe->get($coeName, collect()));
            $initiatives = $items->groupBy(fn (array $item): int => (int) ($item['initiative_id'] ?? 0))->sortKeys()->values()->map(function (Collection $initiativeItems) use (&$globalNumber): array {
                $firstItem = $initiativeItems->first() ?? []; $initiativeId = (int) ($firstItem['initiative_id'] ?? 0);
                $minStart = $initiativeItems->map(fn (array $item): int => $this->quarterIndex((int) ($item['startYear'] ?? 0), (string) ($item['startQ'] ?? '')))->filter(fn (int $value): bool => $value > 0)->min();
                $maxEnd = $initiativeItems->map(fn (array $item): int => $this->quarterIndex((int) ($item['endYear'] ?? 0), (string) ($item['endQ'] ?? '')))->filter(fn (int $value): bool => $value > 0)->max();
                $startYear = $minStart ? intdiv($minStart - 1, 4) : 0; $startQuarter = $minStart ? (($minStart - 1) % 4) + 1 : 1;
                $endYear = $maxEnd ? intdiv($maxEnd - 1, 4) : 0; $endQuarter = $maxEnd ? (($maxEnd - 1) % 4) + 1 : 1;
                $startDate = $this->resolveQuarterDate($startYear, sprintf('Q%d', $startQuarter), false); $endDate = $this->resolveQuarterDate($endYear, sprintf('Q%d', $endQuarter), true);
                $activityLabels = $initiativeItems->pluck('activity')->map(fn (mixed $value): string => trim((string) $value))->filter()->unique()->values()->all();
                return ['no' => $this->resolveDigitalInitiativeBadgeLabel(trim((string) ($firstItem['initiative_code'] ?? '')), $globalNumber), 'id' => $initiativeId, 'name' => trim((string) ($firstItem['initiative_name'] ?? '')) ?: sprintf('Initiative #%d', $initiativeId), 'organization_name' => trim((string) ($firstItem['organization_name'] ?? '')), 'projects' => [['id' => (int) ($firstItem['id'] ?? 0), 'project_id' => null, 'name' => implode('; ', $activityLabels), 'status' => 'baseline', 'status_ref' => ['name' => 'Baseline'], 'milestones' => [['id' => (int) ($firstItem['id'] ?? 0), 'start_date' => $startDate, 'end_date' => $endDate]]]], 'implementation_status' => $this->normalizeImplementationStatus((string) ($firstItem['implementation_status'] ?? '')), 'review_statuses' => []];
            })->all();
            return ['coe_name' => $coeName, 'initiatives' => $initiatives ?: [['no' => '-', 'id' => -1000 - $index, 'name' => '-', 'organization_name' => '', 'projects' => [], 'implementation_status' => null, 'review_statuses' => []]]];
        })->values()->all();
    }

    private function normalizeDigitalRoadmapCoeName(string $rawName): string { $name = strtolower(trim($rawName)); if ($name === '') return 'CoE Not Identified'; if (str_contains($name, 'ai') || str_contains($name, 'analytics')) return 'AI / Adv. Analytics'; if (str_contains($name, 'cloud')) return 'Advance Cloud'; if (str_contains($name, 'iot')) return 'IoT'; if (str_contains($name, 'rpa')) return 'RPA'; return 'CoE Not Identified'; }
    private function normalizeImplementationStatus(string $rawStatus): ?string { $status = strtolower(trim($rawStatus)); if ($status === '') return null; if (str_contains($status, 'done') || str_contains($status, 'complete')) return 'Done'; if (str_contains($status, 'review')) return 'On Review'; if (str_contains($status, 'progress')) return 'On Progress'; return ucwords($status); }
    private function resolveDigitalInitiativeBadgeLabel(string $initiativeCode, int &$fallbackNumber): string { if ($initiativeCode !== '') { if (preg_match('/(\d+)/', $initiativeCode, $matches) === 1) return (string) ((int) $matches[1]); return $initiativeCode; } return (string) $fallbackNumber++; }
    private function quarterIndex(int $year, string $quarter): int { if ($year <= 0 || preg_match('/Q?([1-4])/', strtoupper(trim($quarter)), $matches) !== 1) return 0; return ($year * 4) + (int) $matches[1]; }
    private function resolveQuarterDate(int $year, string $quarter, bool $isEndDate): ?string { if ($year <= 0 || preg_match('/Q?([1-4])/', strtoupper(trim($quarter)), $matches) !== 1) return null; $quarterNumber = (int) $matches[1]; $startMonth = (($quarterNumber - 1) * 3) + 1; $month = $isEndDate ? $startMonth + 2 : $startMonth; $day = $isEndDate ? cal_days_in_month(CAL_GREGORIAN, $month, $year) : 1; return sprintf('%04d-%02d-%02d', $year, $month, $day); }

    public function storeItBuildingBlockMapping(array $data): int { return $this->itBuildingBlockService->storeMapping($data); }
    public function deleteItBuildingBlockPrimary(int $primaryId): void { $this->itBuildingBlockService->deletePrimary($primaryId); }
    public function deleteItBuildingBlockSecondary(int $primaryId, int $secondaryId): void { $this->itBuildingBlockService->deleteSecondary($primaryId, $secondaryId); }
    public function deleteItBuildingBlockInitiative(int $primaryId, int $secondaryId, int $initiativeId): void { $this->itBuildingBlockService->deleteInitiative($primaryId, $secondaryId, $initiativeId); }
    public function deleteItBuildingBlockMultipleMappings(array $removals): void { $this->itBuildingBlockService->deleteMultipleMappings($removals); }

    private function filterCoeCatalogByType(Collection $catalog, int $initiativeType): Collection { return $catalog->mapWithKeys(function (MstCoe $coe) use ($initiativeType): array { $filteredCoe = clone $coe; $filteredCoe->setRelation('initiatives', $coe->initiatives->where('tipe_initiative', $initiativeType)->values()); return [Str::lower($coe->name) => $this->mapCoeCard($filteredCoe)]; }); }
    private function buildSectionCards(Collection $coeCatalog, array $configs, bool $showEmpty): array { return collect($configs)->map(fn (array $config): ?array => $this->buildSingleCard($coeCatalog, $config))->filter(fn (?array $card): bool => $card !== null && ($showEmpty || ! $card['is_empty']))->values()->all(); }
    private function buildSingleCard(Collection $coeCatalog, array $config): ?array { $baseCard = $coeCatalog->get(Str::lower($config['name'])); if ($baseCard === null) return null; return [...$baseCard, 'display_name' => $config['label'] ?? $baseCard['display_name'], 'tone' => $config['tone'] ?? $baseCard['tone'], 'description_lines' => $config['description_lines'] ?? []]; }
    private function mapCoeCard(MstCoe $coe): array { $initiatives = ($coe->initiatives ?? collect())->map(fn($i) => $this->mapInitiativeForSummary($i))->values(); $previewInitiatives = $initiatives->take(3)->values(); return ['id' => (int) $coe->id, 'name' => $coe->name, 'display_name' => $coe->name, 'tone' => 'default', 'initiatives_count' => $initiatives->count(), 'is_empty' => $initiatives->isEmpty(), 'initiatives' => $initiatives->all(), 'initiatives_preview' => $previewInitiatives->all(), 'remaining_initiatives_count' => max(0, $initiatives->count() - $previewInitiatives->count()), 'status_breakdown' => $this->buildStatusBreakdown($initiatives)]; }
    private function buildSummary(array $t, array $s, ?array $f, ?array $a, ?array $tb, array $u): array { $all = collect([...$t, ...$s, ...array_filter([$f, $a, $tb])]); return ['total_initiatives' => (int) $all->sum('initiatives_count') + count($u), 'mapped_initiatives' => (int) $all->sum('initiatives_count'), 'technology_initiatives' => (int) collect($t)->sum('initiatives_count'), 'active_coe_count' => (int) $all->filter(fn ($c) => ($c['initiatives_count'] ?? 0) > 0)->count(), 'unassigned_count' => count($u), 'top_coe_name' => $all->sortByDesc('initiatives_count')->first()['display_name'] ?? '-', 'top_coe_count' => (int) ($all->sortByDesc('initiatives_count')->first()['initiatives_count'] ?? 0)]; }
    private function getDualGrowthGoals(): array { $direct = $this->getDirectDualGrowthInitiatives(); $goals = Goal::query()->with(['themes.initiativeTaggings.initiative.coe', 'themes.initiativeTaggings.initiative.organization', 'themes.initiativeTaggings.initiative.latestStatusImplementation', 'themes.initiativeTaggings.initiative.statusImplementations'])->where('pilar', 2)->orderByRaw("case code when 'A' then 1 when 'B' then 2 else 99 end")->orderBy('code')->get(); return $goals->map(fn ($g) => $this->mapDualGrowthGoal($g, $direct->get(strtoupper((string) $g->code), [])))->values()->all(); }
    private function getDirectDualGrowthInitiatives(): Collection { return InitiativeTagging::query()->with(['initiative.coe', 'initiative.organization', 'initiative.latestStatusImplementation', 'initiative.statusImplementations'])->where('pilar', 2)->whereNull('themes_id')->get()->groupBy(fn ($t) => strtoupper((string) $t->goal))->map(fn ($ts) => $tappings = $ts->map(fn ($t) => $t->initiative)->filter()->unique('id')->sortBy(fn ($i) => sprintf('%08s-%s', (string) $i->code, (string) $i->name))->values()->map(fn ($i) => $this->mapDualGrowthInitiative($i))->all()); }
    private function mapDualGrowthGoal(Goal $g, array $d = []): array { $ts = collect($g->themes ?? [])->map(fn ($t) => $this->mapDualGrowthTheme($t))->values(); return ['id' => (int) $g->id, 'code' => (string) $g->code, 'title' => (string) $g->title, 'themes' => $ts->all(), 'direct_initiatives_count' => count($d), 'direct_initiatives' => $d, 'initiatives_count' => (int) $ts->sum('initiatives_count') + count($d)]; }
    private function mapDualGrowthTheme($t): array { $is = collect($t->initiativeTaggings ?? [])->map(fn ($tg) => $tg->initiative)->filter()->unique('id')->sortBy(fn ($i) => sprintf('%08s-%s', (string) $i->code, (string) $i->name))->values()->map(fn ($i) => $this->mapDualGrowthInitiative($i))->all(); return ['id' => (int) $t->id, 'theme_number' => (int) $t->theme_number, 'name' => (string) $t->name, 'label' => (string) $t->name, 'initiatives_count' => count($is), 'initiatives' => $is]; }
    private function mapDualGrowthInitiative($i): array { return ['id' => (int) $i->id, 'code' => $i->code, 'name' => $i->name, 'description' => $i->description, 'coe_id' => $i->coe_id ? (int) $i->coe_id : null, 'coe_name' => $i->coe?->name, 'label' => trim(collect([$i->code, $i->name])->filter()->implode(' - ')), 'business_unit' => $i->organization?->name, 'groub_id' => $i->organization?->groub_id, 'implementation_status' => $i->latestStatusImplementation?->review_status, 'statuses' => collect($i->statusImplementations ?? [])->map(fn($s) => ['start' => $s->start, 'end' => $s->end, 'year' => (int) $s->year, 'status' => $s->review_status])->values()->all(), 'source' => !is_null($i->source) ? (int) $i->source : null]; }
    private function getFocusBands(array $gs): array { return collect($gs)->map(fn ($g) => ['id' => is_numeric($g['id']) ? 'goal-'.$g['id'] : (string) $g['id'], 'code' => $g['code'], 'title' => $g['title'], 'label' => $g['title']])->values()->all(); }
    private function getRoofSection(): array { $gs = Goal::query()->with(['themes' => fn ($q) => $q->orderBy('theme_number')])->where('pilar', '2')->whereIn('code', ['A', 'B'])->get()->keyBy('code'); $m = $gs->get('A'); $s = $gs->get('B'); return ['main_goal' => $m ? ['id' => (int) $m->id, 'code' => $m->code, 'title' => $m->title] : null, 'main_goal_themes' => collect($m?->themes ?? [])->take(2)->map(fn ($t) => ['id' => (int) $t->id, 'theme_number' => (int) $t->theme_number, 'name' => $t->name, 'label' => $t->name])->values()->all(), 'side_goal' => $s ? ['id' => (int) $s->id, 'code' => $s->code, 'title' => $s->title] : null]; }
    private function buildStatusBreakdown(Collection $is): array { return collect(['drafting', 'propose', 'review', 'approved', 'other'])->map(fn ($s) => ['key' => $s, 'label' => $this->statusLabel($s), 'count' => (int) $is->where('status', $s)->count()])->filter(fn ($i) => $i['count'] > 0)->values()->all(); }
    private function normalizeStatus(?string $s): string { $v = Str::lower(trim((string) $s)); if ($v === '' || in_array($v, ['0', '1', 'draft', 'drafting', 'not start'], true)) return 'drafting'; if ($v === '2' || Str::contains($v, 'propose')) return 'propose'; if ($v === '3' || Str::contains($v, 'review')) return 'review'; if (in_array($v, ['4', 'approve', 'approved', 'aproved'], true)) return 'approved'; return 'other'; }
    private function statusLabel(string $s): string { return match ($s) { 'drafting' => 'Drafting', 'propose' => 'Propose', 'review' => 'Review', 'approved' => 'Approved', default => 'Other' }; }
}
