<?php

namespace App\Services\StrategicHouse;

use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\TrsStatusImplementation;
use App\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use App\Services\StrategicHouse\InitiativeRelation\InitiativeRelationService;
use App\Services\StrategicHouse\InitiativeSupport\InitiativeSupportService;
use App\Services\StrategicHouse\RoadMap\ItInitiativeRoadmapService;
use App\Services\StrategicHouse\StrategicPillars\StrategicPillarPageService;
use App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        [
            'name' => 'User Interface and Experience',
            'label' => 'User Interface',
            'tone' => 'sky',
            'description_lines' => [
                'Memastikan',
                'standarisasi UI/UX',
                'pada seluruh aplikasi',
            ],
        ],
        [
            'name' => 'Integration and Automation',
            'label' => 'Integration and Automation',
            'tone' => 'blue',
            'description_lines' => [
                'Meningkatkan',
                'interaksi sistem di',
                'seluruh holding,',
                'subholding, dan APFS',
            ],
        ],
        [
            'name' => 'Business Application System',
            'label' => 'Business Application System',
            'tone' => 'indigo',
            'description_lines' => [
                'Merasionalisasi dan',
                'memodernisasi',
                'aplikasi legacy,',
                'termasuk membangun',
                'groupwide ERP',
            ],
        ],
        [
            'name' => 'Infrastructure',
            'label' => 'Infrastructure',
            'tone' => 'cyan',
            'description_lines' => [
                'Membangun',
                'infrastruktur best-in-',
                'class untuk',
                'mendukung',
                'peningkatan',
                'kompleksitas use case',
                'digital',
            ],
        ],
        [
            'name' => 'Data and Analytics',
            'label' => 'Data and Analytics',
            'tone' => 'emerald',
            'description_lines' => [
                'AI platform;',
                'Memastikan',
                'ketersediaan dan',
                'keandalan data untuk',
                'mendukung use case',
                'digital',
            ],
        ],
        [
            'name' => 'Cybersecurity',
            'label' => 'Cybersecurity',
            'tone' => 'slate',
            'description_lines' => [
                'Memperkuat kesiapan',
                'menghadapi cyber',
                'threats yang terus',
                'meningkat',
            ],
        ],
    ];

    private const FOUNDATION_COE_CONFIG = [
        'name' => 'People, Process and Technology',
        'label' => 'People, Process and Technology',
        'tone' => 'foundation',
    ];

    private const ARCHITECTURE_COE_CONFIG = [
        'name' => 'Overall Architecture',
        'label' => 'Overall Architecture',
        'tone' => 'architecture',
    ];

    private const TBC_COE_CONFIG = [
        'name' => 'TBC',
        'label' => 'TBC',
        'tone' => 'support',
    ];

    private const DUAL_GROWTH_GOAL_CODES = ['A', 'B'];

    private const FALLBACK_DUAL_GROWTH_GOALS = [
        [
            'id' => 'goal-a',
            'code' => 'A',
            'title' => 'Maximize Legacy Business',
            'themes' => [
                [
                    'id' => 'theme-a1',
                    'theme_number' => 1,
                    'name' => 'Maximizing Value',
                    'label' => 'Maximizing Value',
                    'initiatives_count' => 0,
                    'initiatives' => [],
                ],
                [
                    'id' => 'theme-a2',
                    'theme_number' => 2,
                    'name' => 'Expand to new markets & adjacencies',
                    'label' => 'Expand to new markets & adjacencies',
                    'initiatives_count' => 0,
                    'initiatives' => [],
                ],
            ],
            'initiatives_count' => 0,
            'direct_initiatives_count' => 0,
            'direct_initiatives' => [],
        ],
        [
            'id' => 'goal-b',
            'code' => 'B',
            'title' => 'Building low carbon business',
            'themes' => [],
            'initiatives_count' => 0,
            'direct_initiatives_count' => 0,
            'direct_initiatives' => [],
        ],
    ];

    public function __construct(
        protected ItBuildingBlockService $itBuildingBlockService,
        protected BusinessStrategyService $businessStrategyService,
        protected InitiativeSupportService $initiativeSupportService,
        protected StrategicPillarPageService $strategicPillarPageService,
        protected InitiativeRelationService $initiativeRelationService,
        protected ItInitiativeRoadmapService $itInitiativeRoadmapService,
        protected MasterMilestonePageService $digitalRoadmapPageService
    ) {}

    public function getPageProps(array $filters = []): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $initiativeType = $normalizedFilters['initiative_type'];
        $showEmpty = $normalizedFilters['show_empty'];
        $dualGrowthGoals = $this->getDualGrowthGoals();
        $roofSection = $this->getRoofSection();

        $coeCatalog = $this->getCoeCatalog($initiativeType);

        // Grand IT Strategy section always shows IT Transformation initiatives (type 2)
        $itCoeCatalog = ($initiativeType === 2) ? $coeCatalog : $this->getCoeCatalog(2);

        $technologyCards = $this->buildSectionCards($coeCatalog, self::TECHNOLOGY_COE_CONFIG, $showEmpty);
        $strategyCards = $this->buildSectionCards($itCoeCatalog, self::STRATEGY_COE_CONFIG, $showEmpty);
        $foundationCard = $this->buildSingleCard($itCoeCatalog, self::FOUNDATION_COE_CONFIG);
        $architectureCard = $this->buildSingleCard($itCoeCatalog, self::ARCHITECTURE_COE_CONFIG);
        $tbcCard = $this->buildSingleCard($itCoeCatalog, self::TBC_COE_CONFIG);
        $unassignedInitiatives = $this->getUnassignedInitiatives($initiativeType);

        // Strategic Pillar data
        $pilarId = (string) ($filters['pilar'] ?? '1');
        $pillarData = $this->strategicPillarPageService->getPageProps(
            $filters['goal_id'] ?? null,
            $filters['org_id'] ?? null,
            $pilarId,
            $initiativeType
        );

        $relationData = $this->initiativeRelationService->getIndexProps();
        $roadmapData  = $this->itInitiativeRoadmapService->getPageProps();
        $digitalRoadmapData = $this->digitalRoadmapPageService->getIndexPageProps();
        $digitalRoadmapGroups = $this->buildDigitalRoadmapGroups($digitalRoadmapData['roadmapItems'] ?? []);
        $initiativeSupportData = $this->initiativeSupportService->getPageProps();
        $businessStrategyData = $this->businessStrategyService->getPageProps();

        return [
            'filters' => $normalizedFilters,
            'page' => [
                'title' => 'Strategic House',
                'headline' => 'Pertamina Group Dual Growth Strategy',
                'visionTitle' => 'Visi Pertamina IT',
                'visionText' => 'Meningkatkan peranan IT dari business enabler menjadi strategic value creator, mendorong transformasi digital untuk mendukung ambisi dual growth Pertamina Group.',
                'initiativeLabel' => $initiativeType === 2
                    ? 'IT transformation initiatives'
                    : 'Digital transformation initiatives',
                'grandStrategyTitle' => 'Grand IT Strategy',
                'grandStrategyText' => 'Single source of truth for groupwide IT reference architecture',
            ],
            'summary' => $this->buildSummary(
                $technologyCards,
                $strategyCards,
                $foundationCard,
                $architectureCard,
                $tbcCard,
                $unassignedInitiatives
            ),
            'roofSection' => $roofSection,
            'focusBands' => $this->getFocusBands($dualGrowthGoals),
            'dualGrowthGoals' => $dualGrowthGoals,
            'technologyCards' => $technologyCards,
            'strategyCards' => $strategyCards,
            'foundationCard' => $foundationCard,
            'architectureCard' => $architectureCard,
            'tbcCard' => $tbcCard,
            'unassignedInitiatives' => $unassignedInitiatives,
            'businessStrategyPage' => $businessStrategyData['page'],
            'businessStrategySummary' => $businessStrategyData['summary'],
            'businessStrategyGroups' => $businessStrategyData['groups'],
            'businessStrategyColumns' => $businessStrategyData['strategyColumns'],
            'businessStrategyOrganizationOptions' => $businessStrategyData['organizationOptions'],
            'coeOptions' => $this->itBuildingBlockService->getCoeOptions(),
            'statusPeriods' => $this->itBuildingBlockService->getStatusPeriods(),
            'digitalInitiativeOptions' => $this->itBuildingBlockService->getDigitalInitiativeOptions(),
            'itBuildingBlockMatrix' => $this->itBuildingBlockService->getGroupedMappings(),
            'itInitiativeOptions' => $this->itBuildingBlockService->getItInitiativeOptions(),
            'initiativeSupportGroups' => $initiativeSupportData['groups'],
            'initiativeSupportDigitalOptions' => $initiativeSupportData['digitalInitiativeOptions'],
            'initiativeSupportItOptions' => $initiativeSupportData['itInitiativeOptions'],

            // Merge Strategic Pillar props
            'strategicPillars' => $pillarData['strategicPillars'] instanceof \Closure ? $pillarData['strategicPillars']() : $pillarData['strategicPillars'],
            'taggings' => $pillarData['taggings'] instanceof \Closure ? $pillarData['taggings']() : $pillarData['taggings'],
            'allGoals' => $pillarData['allGoals'] instanceof \Closure ? $pillarData['allGoals']() : $pillarData['allGoals'],
            'allOrganizations' => $pillarData['allOrganizations'] instanceof \Closure ? $pillarData['allOrganizations']() : $pillarData['allOrganizations'],
            'allInitiatives' => $pillarData['allInitiatives'] instanceof \Closure ? $pillarData['allInitiatives']() : $pillarData['allInitiatives'],
            'allThemes' => $pillarData['allThemes'] instanceof \Closure ? $pillarData['allThemes']() : $pillarData['allThemes'],
            'matrixInitiatives' => $pillarData['matrixInitiatives'] instanceof \Closure ? $pillarData['matrixInitiatives']() : $pillarData['matrixInitiatives'],
            'pilarOptions' => $pillarData['pilarOptions'],
            'pillarFilters' => $pillarData['filters'],

            // Initiative Relation props
            'mstInitiatives' => $relationData['mstInitiatives'],
            'initiativeRelations' => $relationData['initiativeRelations'],
            'modelRelationOptions' => $relationData['modelRelationOptions'],
            'typeRelationOptions' => $relationData['typeRelationOptions'],

            // IT Initiative Roadmap props (pre-loaded once, no extra HTTP request)
            'itRoadmapGroups'               => $roadmapData['groups'],
            'itRoadmapStartYear'            => $roadmapData['startYear'],
            'itRoadmapEndYear'              => $roadmapData['endYear'],
            'itRoadmapTotalCount'           => $roadmapData['totalCount'],
            'itRoadmapMilestoneTypeOptions' => $roadmapData['milestoneTypeOptions'],

            'digitalRoadmapGroups'         => $digitalRoadmapGroups,
            'digitalRoadmapStartYear'      => $digitalRoadmapData['startYearRange'],
            'digitalRoadmapEndYear'        => $digitalRoadmapData['endYearRange'],
        ];
    }

    private function buildDigitalRoadmapGroups(array|Collection $roadmapItems): array
    {
        $globalNumber = 1;
        $coeOrder = [
            'AI / Adv. Analytics',
            'Advance Cloud',
            'IoT',
            'RPA',
            'CoE Not Identified',
        ];

        $normalizedItems = collect($roadmapItems)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(function (array $item): array {
                $item['coe_name'] = $this->normalizeDigitalRoadmapCoeName(
                    (string) ($item['coe_name'] ?? ''),
                );

                return $item;
            });

        $initiativeIds = $normalizedItems
            ->pluck('initiative_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $latestStatusByInitiative = TrsStatusImplementation::query()
            ->whereIn('initiative_id', $initiativeIds)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('initiative_id')
            ->map(function (Collection $rows): ?string {
                $latest = $rows->first();

                return $this->normalizeImplementationStatus(
                    (string) ($latest?->review_status ?? ''),
                );
            });

        $byCoe = $normalizedItems->groupBy(
            fn (array $item): string => (string) ($item['coe_name'] ?? 'CoE Not Identified'),
        );

        return collect($coeOrder)
            ->values()
            ->map(function (string $coeName, int $index) use (&$globalNumber, $byCoe, $latestStatusByInitiative): array {
                $items = collect($byCoe->get($coeName, collect()));
                $initiatives = $items
                    ->groupBy(fn (array $item): int => (int) ($item['initiative_id'] ?? 0))
                    ->sortKeys()
                    ->values()
                    ->map(function (Collection $initiativeItems) use (&$globalNumber, $latestStatusByInitiative): array {
                        $firstItem = $initiativeItems->first() ?? [];
                        $initiativeId = (int) ($firstItem['initiative_id'] ?? 0);

                        $minStart = $initiativeItems
                            ->map(fn (array $item): int => $this->quarterIndex((int) ($item['startYear'] ?? 0), (string) ($item['startQ'] ?? '')))
                            ->filter(fn (int $value): bool => $value > 0)
                            ->min();

                        $maxEnd = $initiativeItems
                            ->map(fn (array $item): int => $this->quarterIndex((int) ($item['endYear'] ?? 0), (string) ($item['endQ'] ?? '')))
                            ->filter(fn (int $value): bool => $value > 0)
                            ->max();

                        $startYear = $minStart ? intdiv($minStart - 1, 4) : 0;
                        $startQuarter = $minStart ? (($minStart - 1) % 4) + 1 : 1;
                        $endYear = $maxEnd ? intdiv($maxEnd - 1, 4) : 0;
                        $endQuarter = $maxEnd ? (($maxEnd - 1) % 4) + 1 : 1;

                        $startDate = $this->resolveQuarterDate(
                            $startYear,
                            sprintf('Q%d', $startQuarter),
                            false,
                        );
                        $endDate = $this->resolveQuarterDate(
                            $endYear,
                            sprintf('Q%d', $endQuarter),
                            true,
                        );

                        $initiativeCode = trim((string) ($firstItem['initiative_code'] ?? ''));
                        $badgeLabel = $this->resolveDigitalInitiativeBadgeLabel($initiativeCode, $globalNumber);
                        $activityLabels = $initiativeItems
                            ->pluck('activity')
                            ->map(fn (mixed $value): string => trim((string) $value))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        return [
                            'no' => $badgeLabel,
                            'id' => $initiativeId,
                            'name' => trim((string) ($firstItem['initiative_name'] ?? '')) ?: sprintf('Initiative #%d', $initiativeId),
                            'projects' => [[
                                'id' => (int) ($firstItem['id'] ?? 0),
                                'project_id' => null,
                                'name' => implode('; ', $activityLabels),
                                'status' => 'baseline',
                                'status_ref' => ['name' => 'Baseline'],
                                'milestones' => [[
                                    'id' => (int) ($firstItem['id'] ?? 0),
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                ]],
                            ]],
                            'implementation_status' => $latestStatusByInitiative->get($initiativeId),
                            'review_statuses' => [],
                        ];
                    })
                    ->all();

                if ($initiatives === []) {
                    $initiatives[] = [
                        'no' => '-',
                        'id' => -1000 - $index,
                        'name' => '-',
                        'projects' => [],
                        'implementation_status' => null,
                        'review_statuses' => [],
                    ];
                }

                return [
                    'coe_name' => $coeName,
                    'initiatives' => $initiatives,
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeDigitalRoadmapCoeName(string $rawName): string
    {
        $name = strtolower(trim($rawName));

        if ($name === '') {
            return 'CoE Not Identified';
        }

        if (str_contains($name, 'ai') || str_contains($name, 'analytics')) {
            return 'AI / Adv. Analytics';
        }

        if (str_contains($name, 'cloud')) {
            return 'Advance Cloud';
        }

        if (str_contains($name, 'iot')) {
            return 'IoT';
        }

        if (str_contains($name, 'rpa')) {
            return 'RPA';
        }

        return 'CoE Not Identified';
    }

    private function normalizeImplementationStatus(string $rawStatus): ?string
    {
        $status = strtolower(trim($rawStatus));

        if ($status === '') {
            return null;
        }

        if (str_contains($status, 'done') || str_contains($status, 'complete')) {
            return 'Done';
        }

        if (str_contains($status, 'review')) {
            return 'On Review';
        }

        if (str_contains($status, 'progress')) {
            return 'On Progress';
        }

        return ucwords($status);
    }

    private function resolveDigitalInitiativeBadgeLabel(string $initiativeCode, int &$fallbackNumber): string
    {
        if ($initiativeCode !== '') {
            if (preg_match('/(\d+)/', $initiativeCode, $matches) === 1) {
                return (string) ((int) $matches[1]);
            }

            return $initiativeCode;
        }

        return (string) $fallbackNumber++;
    }

    private function quarterIndex(int $year, string $quarter): int
    {
        if ($year <= 0) {
            return 0;
        }

        if (preg_match('/Q?([1-4])/', strtoupper(trim($quarter)), $matches) !== 1) {
            return 0;
        }

        return ($year * 4) + (int) $matches[1];
    }

    private function resolveQuarterDate(int $year, string $quarter, bool $isEndDate): ?string
    {
        if ($year <= 0) {
            return null;
        }

        $rawQuarter = strtoupper(trim($quarter));
        if (preg_match('/Q?([1-4])/', $rawQuarter, $matches) !== 1) {
            return null;
        }

        $quarterNumber = (int) $matches[1];
        $startMonth = (($quarterNumber - 1) * 3) + 1;
        $month = $isEndDate ? $startMonth + 2 : $startMonth;
        $day = $isEndDate
            ? cal_days_in_month(CAL_GREGORIAN, $month, $year)
            : 1;

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function storeItBuildingBlockMapping(array $data): int
    {
        return $this->itBuildingBlockService->storeMapping($data);
    }

    public function deleteItBuildingBlockPrimary(int $primaryId): void
    {
        $this->itBuildingBlockService->deletePrimary($primaryId);
    }

    public function deleteItBuildingBlockSecondary(int $primaryId, int $secondaryId): void
    {
        $this->itBuildingBlockService->deleteSecondary($primaryId, $secondaryId);
    }

    public function deleteItBuildingBlockInitiative(int $primaryId, int $secondaryId, int $initiativeId): void
    {
        $this->itBuildingBlockService->deleteInitiative($primaryId, $secondaryId, $initiativeId);
    }

    public function deleteItBuildingBlockMultipleMappings(array $removals): void
    {
        $this->itBuildingBlockService->deleteMultipleMappings($removals);
    }


    private function normalizeFilters(array $filters): array

    {
        $initiativeType = (int) ($filters['initiative_type'] ?? self::DEFAULT_INITIATIVE_TYPE);

        return [
            'initiative_type' => in_array($initiativeType, [1, 2], true)
                ? $initiativeType
                : self::DEFAULT_INITIATIVE_TYPE,
            'show_empty' => (bool) ($filters['show_empty'] ?? true),
            'pilar' => $filters['pilar'] ?? null,
        ];
    }

    private function getCoeCatalog(int $initiativeType): Collection
    {
        return MstCoe::query()
            ->select(['id', 'name'])
            ->with([
                'initiatives' => fn ($query) => $query
                    ->select(['id', 'coe_id', 'code', 'name', 'status', 'source', 'description'])
                    ->where('tipe_initiative', $initiativeType)
                    ->with([
                        'latestStatus',
                        'mappedProjects:id',
                        'latestStatusImplementation',
                        'statusImplementations:id,initiative_id,start,end,year,review_status',
                        'sourceData:id,name',
                    ])
                    ->orderBy('code'),
            ])
            ->withCount([
                'initiatives as initiatives_count' => fn ($query) => $query
                    ->where('tipe_initiative', $initiativeType),
            ])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (MstCoe $coe): array => [
                Str::lower($coe->name) => $this->mapCoeCard($coe),
            ]);
    }

    private function buildSectionCards(Collection $coeCatalog, array $configs, bool $showEmpty): array
    {
        return collect($configs)
            ->map(fn (array $config): ?array => $this->buildSingleCard($coeCatalog, $config))
            ->filter(fn (?array $card): bool => $card !== null && ($showEmpty || ! $card['is_empty']))
            ->values()
            ->all();
    }

    private function buildSingleCard(Collection $coeCatalog, array $config): ?array
    {
        $baseCard = $coeCatalog->get(Str::lower($config['name']));

        if ($baseCard === null) {
            return null;
        }

        return [
            ...$baseCard,
            'display_name' => $config['label'] ?? $baseCard['display_name'],
            'tone' => $config['tone'] ?? $baseCard['tone'],
            'description_lines' => $config['description_lines'] ?? [],
        ];
    }

    private function mapCoeCard(MstCoe $coe): array
    {
        $initiatives = ($coe->initiatives ?? collect())
            ->map(function (MstInitiative $initiative): array {
                $status = $this->normalizeStatus($initiative->latestPlanningStatusValue());
                $mappedProjectId = (int) ($initiative->mappedProjects?->first()?->id ?? 0);

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
                    'mapped_project_id' => $mappedProjectId > 0 ? $mappedProjectId : null,
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                    'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                    'statuses' => collect($initiative->statusImplementations ?? [])->map(fn($s) => [
                        'start' => $s->start,
                        'end' => $s->end,
                        'year' => (int) $s->year,
                        'status' => $s->review_status,
                    ])->values()->all(),
                    'source' => !is_null($initiative->source) ? (int) $initiative->source : null,
                    'source_name' => $initiative->sourceData?->name,
                ];
            })
            ->values();

        $previewInitiatives = $initiatives->take(3)->values();

        return [
            'id' => (int) $coe->id,
            'name' => $coe->name,
            'display_name' => $coe->name,
            'tone' => 'default',
            'initiatives_count' => $initiatives->count(),
            'is_empty' => $initiatives->isEmpty(),
            'initiatives' => $initiatives->all(),
            'initiatives_preview' => $previewInitiatives->all(),
            'remaining_initiatives_count' => max(0, $initiatives->count() - $previewInitiatives->count()),
            'status_breakdown' => $this->buildStatusBreakdown($initiatives),
        ];
    }

    private function buildSummary(
        array $technologyCards,
        array $strategyCards,
        ?array $foundationCard,
        ?array $architectureCard,
        ?array $tbcCard,
        array $unassignedInitiatives
    ): array {
        $allCards = collect([
            ...$technologyCards,
            ...$strategyCards,
            ...array_filter([$foundationCard, $architectureCard, $tbcCard]),
        ]);

        $totalMappedInitiatives = (int) $allCards->sum('initiatives_count');
        $totalInitiatives = $totalMappedInitiatives + count($unassignedInitiatives);
        $activeCoeCount = (int) $allCards->filter(fn (array $card): bool => ($card['initiatives_count'] ?? 0) > 0)->count();
        $topCard = $allCards->sortByDesc('initiatives_count')->first();
        $technologyTotal = (int) collect($technologyCards)->sum('initiatives_count');

        return [
            'total_initiatives' => $totalInitiatives,
            'mapped_initiatives' => $totalMappedInitiatives,
            'technology_initiatives' => $technologyTotal,
            'active_coe_count' => $activeCoeCount,
            'unassigned_count' => count($unassignedInitiatives),
            'top_coe_name' => $topCard['display_name'] ?? '-',
            'top_coe_count' => (int) ($topCard['initiatives_count'] ?? 0),
        ];
    }

    private function getDualGrowthGoals(): array
    {
        $directInitiativesByGoal = $this->getDirectDualGrowthInitiatives();

        $goals = Goal::query()
            ->select(['id', 'code', 'title', 'pilar'])
            ->with([
                'themes' => fn ($query) => $query
                    ->select(['id', 'idGoal', 'theme_number', 'name'])
                    ->with([
                        'initiativeTaggings' => fn ($taggingQuery) => $taggingQuery
                            ->select(['id', 'themes_id', 'initiative_id', 'pilar'])
                            ->where('pilar', 2)
                            ->with([
                                'initiative' => fn ($initiativeQuery) => $initiativeQuery
                                    ->select(['id', 'code', 'name', 'coe_id', 'business_unit', 'source', 'description'])
                                    ->with([
                                        'coe:id,name',
                                        'organization:id,name,groub_id',
                                        'latestStatusImplementation',
                                        'statusImplementations',
                                    ]),
                            ]),
                    ])
                    ->orderBy('theme_number'),
            ])
            ->where('pilar', 2)
            ->orderByRaw("case code when 'A' then 1 when 'B' then 2 else 99 end")
            ->orderBy('code')
            ->get();

        return $goals->map(fn (Goal $goal): array => $this->mapDualGrowthGoal(
            $goal,
            $directInitiativesByGoal->get(strtoupper((string) $goal->code), [])
        ))
            ->values()
            ->all();
    }

    private function getDirectDualGrowthInitiatives(): Collection
    {
        return InitiativeTagging::query()
            ->select(['id', 'goal', 'initiative_id', 'pilar', 'themes_id'])
            ->with([
                'initiative' => fn ($query) => $query
                    ->select(['id', 'code', 'name', 'coe_id', 'business_unit', 'source', 'description'])
                    ->with([
                        'coe:id,name',
                        'organization:id,name,groub_id',
                        'latestStatusImplementation',
                        'statusImplementations',
                    ]),
            ])
            ->where('pilar', 2)
            ->whereNull('themes_id')
            ->get()
            ->groupBy(fn ($tagging): string => strtoupper((string) $tagging->goal))
            ->map(function (Collection $taggings): array {
                return $taggings
                    ->map(fn ($tagging) => $tagging->initiative)
                    ->filter()
                    ->unique('id')
                    ->sortBy(fn ($initiative) => sprintf('%08s-%s', (string) $initiative->code, (string) $initiative->name))
                    ->values()
                    ->map(fn ($initiative): array => $this->mapDualGrowthInitiative($initiative))
                    ->all();
            });
    }

    private function mapDualGrowthGoal(Goal $goal, array $directInitiatives = []): array
    {
        $themes = collect($goal->themes ?? [])
            ->map(fn ($theme): array => $this->mapDualGrowthTheme($theme))
            ->values()
            ->all();

        return [
            'id' => (int) $goal->id,
            'code' => (string) $goal->code,
            'title' => (string) $goal->title,
            'themes' => $themes,
            'direct_initiatives_count' => count($directInitiatives),
            'direct_initiatives' => $directInitiatives,
            'initiatives_count' => (int) collect($themes)->sum('initiatives_count') + count($directInitiatives),
        ];
    }

    private function mapDualGrowthTheme($theme): array
    {
        $initiatives = collect($theme->initiativeTaggings ?? [])
            ->map(fn ($tagging) => $tagging->initiative)
            ->filter()
            ->unique('id')
            ->sortBy(fn ($initiative) => sprintf('%08s-%s', (string) $initiative->code, (string) $initiative->name))
            ->values()
            ->map(fn ($initiative): array => $this->mapDualGrowthInitiative($initiative))
            ->all();

        return [
            'id' => (int) $theme->id,
            'theme_number' => (int) $theme->theme_number,
            'name' => (string) $theme->name,
            'label' => (string) $theme->name,
            'initiatives_count' => count($initiatives),
            'initiatives' => $initiatives,
        ];
    }

    private function mapDualGrowthInitiative($initiative): array
    {
        return [
            'id' => (int) $initiative->id,
            'code' => $initiative->code,
            'name' => $initiative->name,
            'description' => $initiative->description,
            'coe_id' => $initiative->coe_id ? (int) $initiative->coe_id : null,
            'coe_name' => $initiative->coe?->name,
            'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
            'business_unit' => $initiative->organization?->name,
            'groub_id' => $initiative->organization?->groub_id,
            'implementation_status' => $initiative->latestStatusImplementation?->review_status,
            'statuses' => collect($initiative->statusImplementations ?? [])->map(fn($s) => [
                'start' => $s->start,
                'end' => $s->end,
                'year' => (int) $s->year,
                'status' => $s->review_status,
            ])->values()->all(),
            'source' => !is_null($initiative->source) ? (int) $initiative->source : null,
        ];
    }

    private function getFocusBands(array $dualGrowthGoals): array
    {
        return collect($dualGrowthGoals)
            ->map(fn (array $goal): array => [
                'id' => is_numeric($goal['id']) ? 'goal-'.$goal['id'] : (string) $goal['id'],
                'code' => $goal['code'],
                'title' => $goal['title'],
                'label' => $goal['title'],
            ])
            ->values()
            ->all();
    }

    private function getRoofSection(): array
    {
        $goals = Goal::query()
            ->with(['themes' => fn ($query) => $query->orderBy('theme_number')])
            ->where('pilar', '2')
            ->whereIn('code', ['A', 'B'])
            ->get()
            ->keyBy('code');

        /** @var Goal|null $mainGoal */
        $mainGoal = $goals->get('A');
        /** @var Goal|null $sideGoal */
        $sideGoal = $goals->get('B');

        return [
            'main_goal' => $mainGoal
                ? [
                    'id' => (int) $mainGoal->id,
                    'code' => $mainGoal->code,
                    'title' => $mainGoal->title,
                ]
                : null,
            'main_goal_themes' => collect($mainGoal?->themes ?? [])
                ->take(2)
                ->map(fn ($theme): array => [
                    'id' => (int) $theme->id,
                    'theme_number' => (int) $theme->theme_number,
                    'name' => $theme->name,
                    'label' => $theme->name,
                ])
                ->values()
                ->all(),
            'side_goal' => $sideGoal
                ? [
                    'id' => (int) $sideGoal->id,
                    'code' => $sideGoal->code,
                    'title' => $sideGoal->title,
                ]
                : null,
        ];
    }

    private function getUnassignedInitiatives(int $initiativeType): array
    {
        return MstInitiative::query()
            ->select(['id', 'code', 'name', 'status', 'source', 'description'])
            ->with([
                'latestStatus',
                'latestStatusImplementation',
                'statusImplementations:id,initiative_id,start,end,year,review_status',
                'sourceData:id,name',
            ])
            ->where('tipe_initiative', $initiativeType)
            ->whereNull('coe_id')
            ->orderBy('code')
            ->get()
            ->map(function (MstInitiative $initiative): array {
                $status = $this->normalizeStatus($initiative->latestPlanningStatusValue());

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                    'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                    'statuses' => collect($initiative->statusImplementations ?? [])->map(fn($s) => [
                        'start' => $s->start,
                        'end' => $s->end,
                        'year' => (int) $s->year,
                        'status' => $s->review_status,
                    ])->values()->all(),
                    'source' => !is_null($initiative->source) ? (int) $initiative->source : null,
                    'source_name' => $initiative->sourceData?->name,
                ];
            })
            ->values()
            ->all();
    }

    private function buildStatusBreakdown(Collection $initiatives): array
    {
        return collect(['drafting', 'propose', 'review', 'approved', 'other'])
            ->map(function (string $status) use ($initiatives): array {
                return [
                    'key' => $status,
                    'label' => $this->statusLabel($status),
                    'count' => (int) $initiatives->where('status', $status)->count(),
                ];
            })
            ->filter(fn (array $item): bool => $item['count'] > 0)
            ->values()
            ->all();
    }

    private function normalizeStatus(?string $status): string
    {
        $value = Str::lower(trim((string) $status));

        if ($value === '' || in_array($value, ['0', '1', 'draft', 'drafting', 'not start'], true)) {
            return 'drafting';
        }

        if ($value === '2' || Str::contains($value, 'propose')) {
            return 'propose';
        }

        if ($value === '3' || Str::contains($value, 'review')) {
            return 'review';
        }

        if (in_array($value, ['4', 'approve', 'approved', 'aproved'], true)) {
            return 'approved';
        }

        return 'other';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'drafting' => 'Drafting',
            'propose' => 'Propose',
            'review' => 'Review',
            'approved' => 'Approved',
            default => 'Other',
        };
    }
}
