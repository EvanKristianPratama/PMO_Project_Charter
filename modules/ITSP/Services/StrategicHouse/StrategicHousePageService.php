<?php

namespace Modules\ITSP\Services\StrategicHouse;

use Modules\ITSP\Models\Goal;
use Modules\ITSP\Models\InitiativeTagging;
use Modules\ITSP\Models\MstCoe;
use Modules\ITSP\Models\MstInitiative;
use Modules\ITSP\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use Modules\ITSP\Services\StrategicHouse\InitiativeRelation\InitiativeRelationService;
use Modules\ITSP\Services\StrategicHouse\InitiativeSupport\InitiativeSupportService;
use Modules\ITSP\Services\StrategicHouse\RoadMap\ItInitiativeRoadmapService;
use Modules\ITSP\Services\StrategicHouse\StrategicPillars\StrategicPillarPageService;
use Modules\ITSP\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MasterMilestonePageService;
use Modules\ITSP\Services\StrategicHouse\MapTechnology\MapTechnologyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StrategicHousePageService
{
    private const DEFAULT_INITIATIVE_TYPE = 1;

    private const TECHNOLOGY_COE_CONFIG = [
        ["name" => "IoTs", "label" => "IoT", "tone" => "sky"],
        [
            "name" => "Cloud & Advanced Computing",
            "label" => "Advance Cloud",
            "tone" => "indigo",
        ],
        ["name" => "RPA", "label" => "RPA", "tone" => "cyan"],
        ["name" => "Robotics", "label" => "Robotics", "tone" => "slate"],
        [
            "name" => "AI/Analytics",
            "label" => "AI / Adv. Analytics",
            "tone" => "amber",
        ],
    ];

    private const STRATEGY_COE_CONFIG = [
        [
            "name" => "User Interface and Experience",
            "label" => "User Interface and Experience",
            "tone" => "sky",
            "description_lines" => [
                "Memastikan",
                "standarisasi UI/UX",
                "pada seluruh aplikasi",
            ],
        ],
        [
            "name" => "Integration and Automation",
            "label" => "Integration and Automation",
            "tone" => "blue",
            "description_lines" => [
                "Meningkatkan",
                "interaksi sistem di",
                "seluruh holding,",
                "subholding, dan APFS",
            ],
        ],
        [
            "name" => "Business Application System",
            "label" => "Business Application System",
            "tone" => "indigo",
            "description_lines" => [
                "Merasionalisasi dan",
                "memodernisasi",
                "aplikasi legacy,",
                "termasuk membangun",
                "groupwide ERP",
            ],
        ],
        [
            "name" => "Infrastructure",
            "label" => "Infrastructure",
            "tone" => "cyan",
            "description_lines" => [
                "Membangun",
                "infrastruktur best-in-",
                "class untuk",
                "mendukung",
                "peningkatan",
                "kompleksitas use case",
                "digital",
            ],
        ],
        [
            "name" => "Data and Analytics",
            "label" => "Data and Analytics",
            "tone" => "emerald",
            "description_lines" => [
                "AI platform;",
                "Memastikan",
                "ketersediaan dan",
                "keandalan data untuk",
                "mendukung use case",
                "digital",
            ],
        ],
        [
            "name" => "Cybersecurity",
            "label" => "Cybersecurity",
            "tone" => "slate",
            "description_lines" => [
                "Memperkuat kesiapan",
                "menghadapi cyber",
                "threats yang terus",
                "meningkat",
            ],
        ],
    ];

    private const FOUNDATION_COE_CONFIG = [
        "name" => "People, Process and Technology",
        "label" => "People, Process and Technology",
        "tone" => "foundation",
    ];
    private const ARCHITECTURE_COE_CONFIG = [
        "name" => "Overall Architecture",
        "label" => "Overall Architecture",
        "tone" => "architecture",
    ];
    private const TBC_COE_CONFIG = [
        "name" => "TBC",
        "label" => "TBC",
        "tone" => "support",
    ];
    private const INITIATIVE_BASE_SELECT = [
        "id",
        "coe_id",
        "tipe_initiative",
        "business_unit",
        "code",
        "name",
        "description",
        "status",
        "source",
    ];
    private const CONSOLIDATED_PROFILE_MAPPING = "mapping";
    private const CONSOLIDATED_PROFILE_BUSINESS_STRATEGY = "business_strategy";
    private const CONSOLIDATED_PROFILE_DIGITAL_OPTIONS = "digital_options";
    private const CONSOLIDATED_PROFILE_IT_OPTIONS = "it_options";
    private const CONSOLIDATED_PROFILE_STRATEGIC_PILLARS = "strategic_pillars";
    private const CONSOLIDATED_PROFILE_DIGITAL_ROADMAP = "digital_roadmap";

    public function __construct(
        protected ItBuildingBlockService $itBuildingBlockService,
        protected BusinessStrategyService $businessStrategyService,
        protected InitiativeSupportService $initiativeSupportService,
        protected StrategicPillarPageService $strategicPillarPageService,
        protected InitiativeRelationService $initiativeRelationService,
        protected ItInitiativeRoadmapService $itInitiativeRoadmapService,
        protected MasterMilestonePageService $digitalRoadmapPageService,
        protected MapTechnologyService $mapTechnologyService,
    ) {}

    public function getPageProps(
        array $filters = [],
        array $selectedProps = [],
    ): array {
        $normalizedFilters = $this->normalizeFilters($filters);
        $initiativeType = $normalizedFilters["initiative_type"];
        $pilarId = (string) ($filters["pilar"] ?? "1");

        // 1. Always available lightweight data
        try {
            $coes = Cache::remember(
                "sh_coes_fixed_v2",
                3600,
                fn() => MstCoe::query()
                    ->select("id", "name")
                    ->orderBy("id")
                    ->get(),
            );
            $focusBandGoals = Cache::remember(
                "sh_focus_band_goals_v1",
                3600,
                fn() => $this->getFocusBandGoals(),
            );
            $roofSection = Cache::remember(
                "sh_roof_fixed",
                3600,
                fn() => $this->getRoofSection(),
            );
            $statusPeriods = Cache::remember(
                "sh_periods",
                3600,
                fn() => $this->itBuildingBlockService->getStatusPeriods(),
            );
        } catch (\Throwable $e) {
            Log::warning(
                "[StrategicHousePageService] Failed to load base props.",
                [
                    "message" => $e->getMessage(),
                    "default_connection" => config("database.default"),
                ],
            );

            return $this->buildFallbackPageProps(
                $normalizedFilters,
                $initiativeType,
                $selectedProps,
            );
        }

        $dualGrowthGoals = null;
        $mappingData = null;
        $mappingBusinessStrategyProps = null;
        $businessStrategyProps = null;
        $initiativeSupportProps = null;
        $mapTechnologyProps = null;
        $strategicPillarProps = null;
        $initiativeRelationProps = null;
        $itRoadmapProps = null;
        $digitalRoadmapProps = null;
        $digitalRoadmapGroups = null;
        $mappingInitiatives = null;
        $businessStrategyInitiatives = null;
        $digitalInitiativeOptionModels = null;
        $itInitiativeOptionModels = null;
        $strategicPillarInitiatives = null;
        $digitalRoadmapInitiatives = null;

        $loadDualGrowthGoals = function () use (&$dualGrowthGoals): array {
            return $dualGrowthGoals ??= Cache::remember(
                "sh_dual_goals_fixed_v3",
                3600,
                fn() => $this->getDualGrowthGoals(),
            );
        };

        $loadMappingInitiatives = function () use (
            &$mappingInitiatives,
        ): Collection {
            return $mappingInitiatives ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_MAPPING,
            );
        };

        $loadBusinessStrategyInitiatives = function () use (
            &$businessStrategyInitiatives,
        ): Collection {
            return $businessStrategyInitiatives ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_BUSINESS_STRATEGY,
            );
        };

        $loadDigitalInitiativeOptionModels = function () use (
            &$digitalInitiativeOptionModels,
        ): Collection {
            return $digitalInitiativeOptionModels ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_DIGITAL_OPTIONS,
            );
        };

        $loadItInitiativeOptionModels = function () use (
            &$itInitiativeOptionModels,
        ): Collection {
            return $itInitiativeOptionModels ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_IT_OPTIONS,
            );
        };

        $loadStrategicPillarInitiatives = function () use (
            &$strategicPillarInitiatives,
        ): Collection {
            return $strategicPillarInitiatives ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_STRATEGIC_PILLARS,
            );
        };

        $loadDigitalRoadmapInitiatives = function () use (
            &$digitalRoadmapInitiatives,
        ): Collection {
            return $digitalRoadmapInitiatives ??= $this->getConsolidatedInitiatives(
                self::CONSOLIDATED_PROFILE_DIGITAL_ROADMAP,
            );
        };

        $loadMappingData = function () use (
            &$mappingData,
            $coes,
            $initiativeType,
            $normalizedFilters,
            $loadMappingInitiatives,
        ): array {
            return $mappingData ??= $this->getMappingData(
                $coes,
                $loadMappingInitiatives(),
                $initiativeType,
                $normalizedFilters["show_empty"],
            );
        };

        $loadMappingBusinessStrategyProps = function () use (
            &$mappingBusinessStrategyProps,
        ): array {
            return $mappingBusinessStrategyProps ??= $this->businessStrategyService->getMappingProps();
        };

        $loadBusinessStrategyProps = function () use (
            &$businessStrategyProps,
            $loadBusinessStrategyInitiatives,
        ): array {
            return $businessStrategyProps ??= $this->businessStrategyService->getPageProps(
                $loadBusinessStrategyInitiatives(),
            );
        };

        $loadInitiativeSupportProps = function () use (
            &$initiativeSupportProps,
        ): array {
            return $initiativeSupportProps ??= $this->initiativeSupportService->getPageProps();
        };

        $loadMapTechnologyProps = function () use (
            &$mapTechnologyProps,
        ): array {
            return $mapTechnologyProps ??= $this->mapTechnologyService->getPageProps();
        };

        $loadStrategicPillarProps = function () use (
            &$strategicPillarProps,
            $filters,
            $pilarId,
            $initiativeType,
            $loadStrategicPillarInitiatives,
        ): array {
            return $strategicPillarProps ??= $this->strategicPillarPageService->getPageProps(
                $filters["goal_id"] ?? null,
                $filters["org_id"] ?? null,
                $pilarId,
                $initiativeType,
                $loadStrategicPillarInitiatives(),
            );
        };

        $loadInitiativeRelationProps = function () use (
            &$initiativeRelationProps,
        ): array {
            return $initiativeRelationProps ??= $this->initiativeRelationService->getIndexProps();
        };

        $loadItRoadmapProps = function () use (&$itRoadmapProps): array {
            return $itRoadmapProps ??= $this->itInitiativeRoadmapService->getPageProps();
        };

        $loadDigitalRoadmapPageProps = function () use (
            &$digitalRoadmapProps,
        ): array {
            return $digitalRoadmapProps ??= $this->digitalRoadmapPageService->getIndexPageProps();
        };

        $loadDigitalRoadmapGroups = function () use (
            &$digitalRoadmapGroups,
            $loadDigitalRoadmapPageProps,
            $loadDigitalRoadmapInitiatives,
        ): array {
            return $digitalRoadmapGroups ??= $this->buildDigitalRoadmapGroups(
                $loadDigitalRoadmapPageProps()["roadmapItems"] ?? [],
                $loadDigitalRoadmapInitiatives(),
            );
        };

        $baseProps = [
            "filters" => $normalizedFilters,
            "page" => [
                "title" => "Strategic House",
                "headline" => "Pertamina Group Dual Growth Strategy",
                "visionTitle" => "Visi Pertamina IT",
                "visionText" =>
                    "Meningkatkan peranan IT dari business enabler menjadi strategic value creator, mendorong transformasi digital untuk mendukung ambisi dual growth Pertamina Group.",
                "initiativeLabel" =>
                    $initiativeType === 2
                        ? "IT transformation initiatives"
                        : "Digital transformation initiatives",
                "grandStrategyTitle" => "Grand IT Strategy",
                "grandStrategyText" =>
                    "Single source of truth for groupwide IT reference architecture",
            ],
            "roofSection" => $roofSection,
            "focusBands" => $this->getFocusBands($focusBandGoals),
            "coeOptions" => $coes
                ->map(
                    fn($coe) => ["id" => (int) $coe->id, "name" => $coe->name],
                )
                ->sortBy("name")
                ->values(),
            "statusPeriods" => $statusPeriods,
        ];

        $heavyProps = collect([
            "summary" => fn() => $loadMappingData()["summary"],
            "technologyCards" => fn() => $loadMappingData()["technologyCards"],
            "strategyCards" => fn() => $loadMappingData()["strategyCards"],
            "foundationCard" => fn() => $loadMappingData()["foundationCard"],
            "architectureCard" => fn() => $loadMappingData()[
                "architectureCard"
            ],
            "tbcCard" => fn() => $loadMappingData()["tbcCard"],
            "unassignedInitiatives" => fn() => $loadMappingData()[
                "unassignedInitiatives"
            ],

            // Lightweight business strategy data for the mapping tab's show/hide panel
            "mappingBusinessStrategyGroups" => fn() => $loadMappingBusinessStrategyProps()[
                "groups"
            ],
            "mappingBusinessStrategyColumns" => fn() => $loadMappingBusinessStrategyProps()[
                "strategyColumns"
            ],
            "mappingBusinessStrategyOrganizationOptions" => fn() => $loadMappingBusinessStrategyProps()[
                "organizationOptions"
            ],

            "businessStrategyPage" => fn() => $loadBusinessStrategyProps()[
                "page"
            ],
            "businessStrategySummary" => fn() => $loadBusinessStrategyProps()[
                "summary"
            ],
            "businessStrategyHeaderGoals" => fn() => $loadBusinessStrategyProps()[
                "headerGoals"
            ],
            "businessStrategyEnablerGoals" => fn() => $loadBusinessStrategyProps()[
                "enablerGoals"
            ],
            "businessStrategyGroups" => fn() => $loadBusinessStrategyProps()[
                "groups"
            ],
            "businessStrategyColumns" => fn() => $loadBusinessStrategyProps()[
                "strategyColumns"
            ],
            "businessStrategyOrganizationOptions" => fn() => $loadBusinessStrategyProps()[
                "organizationOptions"
            ],

            "dualGrowthGoals" => fn() => $loadDualGrowthGoals(),
            "digitalInitiativeOptions" => fn() => $this->itBuildingBlockService->getDigitalInitiativeOptions(
                $loadDigitalInitiativeOptionModels(),
            ),
            "itBuildingBlockMatrix" => fn() => $this->itBuildingBlockService->getGroupedMappings(),
            "itInitiativeOptions" => fn() => $this->itBuildingBlockService->getItInitiativeOptions(
                $loadItInitiativeOptionModels(),
            ),

            "initiativeSupportGroups" => fn() => $loadInitiativeSupportProps()[
                "groups"
            ],
            "initiativeSupportDigitalOptions" => fn() => $loadInitiativeSupportProps()[
                "digitalInitiativeOptions"
            ],
            "initiativeSupportItOptions" => fn() => $loadInitiativeSupportProps()[
                "itInitiativeOptions"
            ],

            "mapTechnologies" => fn() => $loadMapTechnologyProps()[
                "mapTechnologies"
            ],
            "mapTechnologyCoeOptions" => fn() => $loadMapTechnologyProps()[
                "coeOptions"
            ],
            "mapTechnologyInitiativeOptions" => fn() => $loadMapTechnologyProps()[
                "initiativeOptions"
            ],

            "strategicPillars" => fn() => $loadStrategicPillarProps()[
                "strategicPillars"
            ],
            "taggings" => fn() => $loadStrategicPillarProps()["taggings"],
            "allGoals" => fn() => $loadStrategicPillarProps()["allGoals"],
            "allOrganizations" => fn() => $loadStrategicPillarProps()[
                "allOrganizations"
            ],
            "allInitiatives" => fn() => $loadStrategicPillarProps()[
                "allInitiatives"
            ],
            "allThemes" => fn() => $loadStrategicPillarProps()["allThemes"],
            "matrixInitiatives" => fn() => $loadStrategicPillarProps()[
                "matrixInitiatives"
            ],
            "pilarOptions" => fn() => $loadStrategicPillarProps()[
                "pilarOptions"
            ],
            "pillarFilters" => fn() => $loadStrategicPillarProps()["filters"],

            "mstInitiatives" => fn() => $loadInitiativeRelationProps()[
                "mstInitiatives"
            ],
            "initiativeRelations" => fn() => $loadInitiativeRelationProps()[
                "initiativeRelations"
            ],
            "modelRelationOptions" => fn() => $loadInitiativeRelationProps()[
                "modelRelationOptions"
            ],
            "typeRelationOptions" => fn() => $loadInitiativeRelationProps()[
                "typeRelationOptions"
            ],

            "itRoadmapGroups" => fn() => $loadItRoadmapProps()["groups"],
            "itRoadmapStartYear" => fn() => $loadItRoadmapProps()["startYear"],
            "itRoadmapEndYear" => fn() => $loadItRoadmapProps()["endYear"],
            "itRoadmapTotalCount" => fn() => $loadItRoadmapProps()[
                "totalCount"
            ],
            "itRoadmapMilestoneTypeOptions" => fn() => $loadItRoadmapProps()[
                "milestoneTypeOptions"
            ],

            "digitalRoadmapGroups" => fn() => $loadDigitalRoadmapGroups(),
            "digitalRoadmapTotalCount" => fn() => $loadDigitalRoadmapInitiatives()
                ->where("tipe_initiative", 1)
                ->count(),
            "digitalRoadmapStartYear" => fn() => $loadDigitalRoadmapPageProps()[
                "startYearRange"
            ],
            "digitalRoadmapEndYear" => fn() => $loadDigitalRoadmapPageProps()[
                "endYearRange"
            ],
        ])
            ->mapWithKeys(
                fn(callable $resolver, string $key): array => [
                    $key => fn() => $this->resolveSafeHeavyProp(
                        $key,
                        $resolver,
                    ),
                ],
            )
            ->all();

        if (empty($selectedProps)) {
            return array_merge($baseProps, $heavyProps);
        }

        $selectedHeavyProps = array_intersect_key(
            $heavyProps,
            array_flip($selectedProps),
        );

        return array_merge($baseProps, $selectedHeavyProps);
    }

    private function getMappingData(
        Collection $coes,
        Collection $allInitiatives,
        int $initiativeType,
        bool $showEmpty,
    ): array {
        $clonedCoes = $coes->map(fn($c) => clone $c);
        foreach ($clonedCoes as $coe) {
            $coe->setRelation(
                "initiatives",
                $allInitiatives->where("coe_id", $coe->id)->values(),
            );
        }
        $coeCatalog = $this->filterCoeCatalogByType(
            $clonedCoes,
            $initiativeType,
        );
        $itCoeCatalog =
            $initiativeType === 2
                ? $coeCatalog
                : $this->filterCoeCatalogByType($clonedCoes, 2);

        return [
            "technologyCards" => $this->buildSectionCards(
                $coeCatalog,
                self::TECHNOLOGY_COE_CONFIG,
                $showEmpty,
            ),
            "strategyCards" => $this->buildSectionCards(
                $itCoeCatalog,
                self::STRATEGY_COE_CONFIG,
                $showEmpty,
            ),
            "foundationCard" => $this->buildSingleCard(
                $itCoeCatalog,
                self::FOUNDATION_COE_CONFIG,
            ),
            "architectureCard" => $this->buildSingleCard(
                $itCoeCatalog,
                self::ARCHITECTURE_COE_CONFIG,
            ),
            "tbcCard" => $this->buildSingleCard(
                $itCoeCatalog,
                self::TBC_COE_CONFIG,
            ),
            "unassignedInitiatives" => $allInitiatives
                ->where("tipe_initiative", $initiativeType)
                ->whereNull("coe_id")
                ->map(fn($i) => $this->mapInitiativeForSummary($i))
                ->values()
                ->all(),
            "summary" => $this->buildSummary(
                $this->buildSectionCards(
                    $coeCatalog,
                    self::TECHNOLOGY_COE_CONFIG,
                    true,
                ),
                $this->buildSectionCards(
                    $itCoeCatalog,
                    self::STRATEGY_COE_CONFIG,
                    true,
                ),
                $this->buildSingleCard(
                    $itCoeCatalog,
                    self::FOUNDATION_COE_CONFIG,
                ),
                $this->buildSingleCard(
                    $itCoeCatalog,
                    self::ARCHITECTURE_COE_CONFIG,
                ),
                $this->buildSingleCard($itCoeCatalog, self::TBC_COE_CONFIG),
                [],
            ),
        ];
    }

    private function getConsolidatedInitiatives(
        string $profile = self::CONSOLIDATED_PROFILE_MAPPING,
    ): Collection {
        $cacheKey = sprintf("sh_consolidated_initiatives_%s_v12", $profile);

        return Cache::remember($cacheKey, 3600, function () use ($profile) {
            return MstInitiative::query()
                ->select(self::INITIATIVE_BASE_SELECT)
                ->with($this->getConsolidatedInitiativeRelations($profile))
                ->orderBy("code")
                ->get();
        });
    }

    private function normalizeFilters(array $filters): array
    {
        $initiativeType =
            (int) ($filters["initiative_type"] ??
                self::DEFAULT_INITIATIVE_TYPE);
        return [
            "initiative_type" => in_array($initiativeType, [1, 2], true)
                ? $initiativeType
                : self::DEFAULT_INITIATIVE_TYPE,
            "show_empty" => (bool) ($filters["show_empty"] ?? true),
            "pilar" => $filters["pilar"] ?? null,
            "view" => $filters["view"] ?? "mapping",
            "roadmap" => in_array(
                $filters["roadmap"] ?? "it",
                ["it", "digital", "all"],
                true,
            )
                ? $filters["roadmap"] ?? "it"
                : "it",
        ];
    }

    private function mapInitiativeForSummary(
        MstInitiative $initiative,
        bool $includeFullHistory = false,
    ): array {
        $status = $this->normalizeStatus(
            $initiative->latestPlanningStatusValue(),
        );
        $mappedProject = $initiative->mappedProjects?->first();

        $implData = $this->resolveImplementationData(
            $initiative,
            $includeFullHistory,
        );

        return [
            "id" => (int) $initiative->id,
            "code" => $initiative->code,
            "name" => $initiative->name,
            "description" => $initiative->description,
            "label" => trim(
                collect([$initiative->code, $initiative->name])
                    ->filter()
                    ->implode(" - "),
            ),
            "business_unit_id" => !is_null($initiative->business_unit)
                ? (int) $initiative->business_unit
                : null,
            "business_unit_name" => trim(
                (string) ($initiative->organization?->name ?? ""),
            ),
            "status" => $status,
            "status_label" => $this->statusLabel($status),
            "tipe_initiative" => (int) $initiative->tipe_initiative,
            "implementation_status" => $implData["implementation_status"],
            "statuses" => $implData["statuses"],
            "source" => !is_null($initiative->source)
                ? (int) $initiative->source
                : null,
            "source_name" => $initiative->sourceData?->name,
            "groub_id" => max(
                1,
                (int) ($initiative->organization?->groub_id ?? 1),
            ),
            "mapped_project_id" => $mappedProject?->id,
        ];
    }

    private function resolveImplementationData(
        MstInitiative $initiative,
        bool $includeFullHistory = false,
    ): array {
        if ($initiative->tipe_initiative == 2) {
            $project = $initiative->mappedProjects?->first();
            $latestStatus = null;
            $history = [];

            if ($project) {
                $history = collect(
                    $project->reviewPcStatusImplementations ?? [],
                )
                    ->map(
                        fn($s) => [
                            "start" => $s->start,
                            "end" => $s->end,
                            "year" => (int) $s->year,
                            "status" => $s->review_status,
                        ],
                    )
                    ->values();

                $latestStatus = $history->first()["status"] ?? null;

                if (!$includeFullHistory) {
                    $history = $history->take(6);
                }

                $history = $history->all();
            }

            return [
                "implementation_status" => $latestStatus,
                "statuses" => $history,
            ];
        }

        $latestStatus = $initiative->latestStatusImplementation?->review_status;
        $history = collect($initiative->statusImplementations ?? [])
            ->map(
                fn($s) => [
                    "id" => (int) $s->id,
                    "initiative_id" => (int) $s->initiative_id,
                    "status" => $s->review_status,
                    "review_status" => $s->review_status,
                    "start" => $s->start,
                    "end" => $s->end,
                    "year" => (int) $s->year,
                ],
            )
            ->values();

        if (!$includeFullHistory) {
            $history = $history->take(6);
        }

        return [
            "implementation_status" => $latestStatus,
            "statuses" => $history->all(),
        ];
    }

    private function buildDigitalRoadmapGroups(
        array|Collection $roadmapItems,
        Collection $digitalInitiativeModels,
    ): array {
        $globalNumber = 1;
        $coeOrder = [
            "AI / Adv. Analytics",
            "Advance Cloud",
            "IoT",
            "RPA",
            "CoE Not Identified",
        ];
        $digitalInitiativeModels = $digitalInitiativeModels
            ->where("tipe_initiative", 1)
            ->sortBy(
                fn($initiative) => sprintf(
                    "%08s-%s",
                    (string) $initiative->code,
                    (string) $initiative->name,
                ),
            )
            ->values();
        $initiativeModelById = $digitalInitiativeModels->keyBy(
            fn(MstInitiative $initiative): int => (int) $initiative->id,
        );

        $normalizedItems = collect($roadmapItems)
            ->filter(fn(mixed $item): bool => is_array($item))
            ->map(function (array $item): array {
                $item["coe_name"] = $this->normalizeDigitalRoadmapCoeName(
                    (string) ($item["coe_name"] ?? ""),
                );

                return $item;
            });

        $roadmapInitiatives = $normalizedItems
            ->groupBy(
                fn(array $item): int => (int) ($item["initiative_id"] ?? 0),
            )
            ->map(function (Collection $initiativeItems) use (
                &$globalNumber,
                $initiativeModelById,
            ): array {
                $firstItem = $initiativeItems->first() ?? [];
                $initiativeId = (int) ($firstItem["initiative_id"] ?? 0);
                /** @var MstInitiative|null $initiativeModel */
                $initiativeModel = $initiativeModelById->get($initiativeId);

                $minStart = $initiativeItems
                    ->map(
                        fn(array $item): int => $this->quarterIndex(
                            (int) ($item["startYear"] ?? 0),
                            (string) ($item["startQ"] ?? ""),
                        ),
                    )
                    ->filter(fn(int $value): bool => $value > 0)
                    ->min();
                $maxEnd = $initiativeItems
                    ->map(
                        fn(array $item): int => $this->quarterIndex(
                            (int) ($item["endYear"] ?? 0),
                            (string) ($item["endQ"] ?? ""),
                        ),
                    )
                    ->filter(fn(int $value): bool => $value > 0)
                    ->max();

                $startYear = $minStart ? intdiv($minStart - 1, 4) : 0;
                $startQuarter = $minStart ? (($minStart - 1) % 4) + 1 : 1;
                $endYear = $maxEnd ? intdiv($maxEnd - 1, 4) : 0;
                $endQuarter = $maxEnd ? (($maxEnd - 1) % 4) + 1 : 1;
                $startDate = $this->resolveQuarterDate(
                    $startYear,
                    sprintf("Q%d", $startQuarter),
                    false,
                );
                $endDate = $this->resolveQuarterDate(
                    $endYear,
                    sprintf("Q%d", $endQuarter),
                    true,
                );
                $activityLabels = $initiativeItems
                    ->pluck("activity")
                    ->map(fn(mixed $value): string => trim((string) $value))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    "no" => $this->resolveDigitalInitiativeBadgeLabel(
                        trim((string) ($firstItem["initiative_code"] ?? "")),
                        $globalNumber,
                    ),
                    "id" => $initiativeId,
                    "name" =>
                        trim((string) ($firstItem["initiative_name"] ?? "")) ?:
                        sprintf("Initiative #%d", $initiativeId),
                    "coe_name" =>
                        (string) ($firstItem["coe_name"] ??
                            $this->normalizeDigitalRoadmapCoeName(
                                (string) ($initiativeModel?->coe?->name ??
                                    "CoE Not Identified"),
                            )),
                    "organization_name" => trim(
                        (string) ($firstItem["organization_name"] ??
                            ($initiativeModel?->organization?->name ?? "")),
                    ),
                    "projects" => [
                        [
                            "id" => (int) ($firstItem["id"] ?? 0),
                            "project_id" => null,
                            "name" => implode("; ", $activityLabels),
                            "status" => "baseline",
                            "status_ref" => ["name" => "Baseline"],
                            "milestones" => $initiativeItems
                                ->map(
                                    fn(array $item) => [
                                        "id" => (int) ($item["id"] ?? 0),
                                        "title" => trim(
                                            (string) ($item["activity"] ?? ""),
                                        ),
                                        "start_date" => $this->resolveQuarterDate(
                                            (int) ($item["startYear"] ?? 0),
                                            (string) ($item["startQ"] ?? ""),
                                            false,
                                        ),
                                        "end_date" => $this->resolveQuarterDate(
                                            (int) ($item["endYear"] ?? 0),
                                            (string) ($item["endQ"] ?? ""),
                                            true,
                                        ),
                                    ],
                                )
                                ->values()
                                ->all(),
                        ],
                    ],
                    "implementation_status" => $this->normalizeImplementationStatus(
                        (string) ($firstItem["implementation_status"] ?? ""),
                    ),
                    "review_statuses" => [],
                    "implementation_statuses" => $this->mapDigitalImplementationStatuses(
                        $initiativeModel,
                    ),
                ];
            });

        $allDigitalInitiatives = $digitalInitiativeModels->map(function (
            MstInitiative $initiative,
        ) use (&$globalNumber, $roadmapInitiatives): array {
            $initiativeId = (int) $initiative->id;

            if ($roadmapInitiatives->has($initiativeId)) {
                return $roadmapInitiatives->get($initiativeId);
            }

            return [
                "no" => $this->resolveDigitalInitiativeBadgeLabel(
                    trim((string) ($initiative->code ?? "")),
                    $globalNumber,
                ),
                "id" => $initiativeId,
                "name" =>
                    trim((string) ($initiative->name ?? "")) ?:
                    sprintf("Initiative #%d", $initiativeId),
                "coe_name" => $this->normalizeDigitalRoadmapCoeName(
                    (string) ($initiative->coe?->name ?? ""),
                ),
                "organization_name" => trim(
                    (string) ($initiative->organization?->name ?? ""),
                ),
                "projects" => [],
                "implementation_status" => $this->normalizeImplementationStatus(
                    (string) ($initiative->latestStatusImplementation
                        ?->review_status ?? ""),
                ),
                "review_statuses" => [],
                "implementation_statuses" => $this->mapDigitalImplementationStatuses(
                    $initiative,
                ),
            ];
        });

        $byCoe = $allDigitalInitiatives->groupBy(
            fn(array $initiative): string => (string) ($initiative[
                "coe_name"
            ] ?? "CoE Not Identified"),
        );

        return collect($coeOrder)
            ->values()
            ->map(function (string $coeName, int $index) use ($byCoe): array {
                $initiatives = collect($byCoe->get($coeName, collect()))
                    ->map(
                        fn(array $initiative): array => collect($initiative)
                            ->except("coe_name")
                            ->all(),
                    )
                    ->values()
                    ->all();

                return [
                    "coe_name" => $coeName,
                    "initiatives" => $initiatives ?: [
                        [
                            "no" => "-",
                            "id" => -1000 - $index,
                            "name" => "-",
                            "organization_name" => "",
                            "projects" => [],
                            "implementation_status" => null,
                            "review_statuses" => [],
                        ],
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function mapDigitalImplementationStatuses(
        ?MstInitiative $initiative,
    ): array {
        if (!$initiative) {
            return [];
        }

        return collect($initiative->statusImplementations ?? [])
            ->map(function ($status): ?array {
                $start = trim((string) ($status->start ?? ""));
                $end = trim((string) ($status->end ?? ""));
                $year = trim((string) ($status->year ?? ""));
                $reviewStatus = trim((string) ($status->review_status ?? ""));
                $periodKey = $this->buildDigitalImplementationPeriodKey(
                    $start,
                    $end,
                    $year,
                );
                $periodLabel = $this->buildDigitalImplementationPeriodLabel(
                    $start,
                    $end,
                    $year,
                );

                if (
                    $reviewStatus === "" ||
                    $periodKey === "" ||
                    $periodLabel === ""
                ) {
                    return null;
                }

                return [
                    "id" => (int) ($status->id ?? 0),
                    "initiative_id" => (int) ($status->initiative_id ?? 0),
                    "review_status" => $reviewStatus,
                    "status" => $reviewStatus,
                    "period_key" => $periodKey,
                    "periode_label" => $periodLabel,
                    "start" => $start,
                    "end" => $end,
                    "year" => $year,
                    "created_at" => $status->created_at?->toISOString(),
                    "updated_at" => $status->updated_at?->toISOString(),
                ];
            })
            ->filter()
            ->sort(function (array $left, array $right): int {
                $leftYear = (int) ($left["year"] ?? 0);
                $rightYear = (int) ($right["year"] ?? 0);

                if ($leftYear !== $rightYear) {
                    return $leftYear <=> $rightYear;
                }

                $leftStart = $this->digitalMonthOrderValue(
                    (string) ($left["start"] ?? ""),
                );
                $rightStart = $this->digitalMonthOrderValue(
                    (string) ($right["start"] ?? ""),
                );

                if ($leftStart !== $rightStart) {
                    return $leftStart <=> $rightStart;
                }

                $leftEnd = $this->digitalMonthOrderValue(
                    (string) ($left["end"] ?? ""),
                );
                $rightEnd = $this->digitalMonthOrderValue(
                    (string) ($right["end"] ?? ""),
                );

                if ($leftEnd !== $rightEnd) {
                    return $leftEnd <=> $rightEnd;
                }

                return (int) ($left["id"] ?? 0) <=> (int) ($right["id"] ?? 0);
            })
            ->values()
            ->all();
    }

    private function buildDigitalImplementationPeriodKey(
        string $start,
        string $end,
        string $year,
    ): string {
        if ($start === "" || $year === "") {
            return "";
        }

        return implode("|", [$start, $end, $year]);
    }

    private function buildDigitalImplementationPeriodLabel(
        string $start,
        string $end,
        string $year,
    ): string {
        if ($start !== "" && $end !== "" && $year !== "") {
            return $start === $end
                ? sprintf("%s %s", $start, $year)
                : sprintf("%s - %s %s", $start, $end, $year);
        }

        if ($start !== "" && $year !== "") {
            return sprintf("%s %s", $start, $year);
        }

        if ($end !== "" && $year !== "") {
            return sprintf("%s %s", $end, $year);
        }

        return trim(
            implode(
                " ",
                array_filter(
                    [$start, $end, $year],
                    fn(string $value): bool => $value !== "",
                ),
            ),
        );
    }

    private function digitalMonthOrderValue(string $month): int
    {
        return match (strtolower(trim($month))) {
            "januari" => 1,
            "februari" => 2,
            "maret" => 3,
            "april" => 4,
            "mei" => 5,
            "juni" => 6,
            "juli" => 7,
            "agustus" => 8,
            "september" => 9,
            "oktober" => 10,
            "november" => 11,
            "desember" => 12,
            default => 0,
        };
    }

    private function getConsolidatedInitiativeRelations(string $profile): array
    {
        return match ($profile) {
            self::CONSOLIDATED_PROFILE_BUSINESS_STRATEGY => [
                "sourceData:id,name",
                "latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "statusImplementations:id,initiative_id,start,end,year,review_status",
                "taggings:id,initiative_id,goal,pilar,themes_id",
            ],
            self::CONSOLIDATED_PROFILE_DIGITAL_OPTIONS => [
                "coe:id,name",
                "organization:id,name,groub_id",
                "latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "statusImplementations:id,initiative_id,start,end,year,review_status",
                "sourceData:id,name",
                "mappedProjects:id",
            ],
            self::CONSOLIDATED_PROFILE_IT_OPTIONS => [
                "coe:id,name",
                "organization:id,name,groub_id",
                "sourceData:id,name",
                "mappedProjects:id",
                "mappedProjects.reviewPcStatusImplementations" => fn(
                    $query,
                ) => $this->orderReviewProjectStatusHistoryQuery($query),
            ],
            self::CONSOLIDATED_PROFILE_STRATEGIC_PILLARS => [
                "organization:id,name",
                "latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "taggings:id,initiative_id,pilar,goal,themes_id",
            ],
            self::CONSOLIDATED_PROFILE_DIGITAL_ROADMAP => [
                "coe:id,name",
                "organization:id,name",
                "latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "statusImplementations:id,initiative_id,start,end,year,review_status,created_at,updated_at",
            ],
            default => [
                "latestStatus" => fn(
                    $query,
                ) => $this->selectLatestPlanningStatus($query),
                "organization:id,name,groub_id",
                "latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "statusImplementations:id,initiative_id,start,end,year,review_status",
                "sourceData:id,name",
                "mappedProjects:id",
                "mappedProjects.reviewPcStatusImplementations" => fn(
                    $query,
                ) => $this->orderReviewProjectStatusHistoryQuery($query),
            ],
        };
    }

    private function selectLatestPlanningStatus($query)
    {
        return $query->select([
            "trs_status_mstinitiative.id",
            "trs_status_mstinitiative.initiative_id",
            "trs_status_mstinitiative.status",
        ]);
    }

    private function selectLatestImplementationStatus($query)
    {
        return $query->select([
            "trs_status_implementation.id",
            "trs_status_implementation.initiative_id",
            "trs_status_implementation.review_status",
        ]);
    }

    private function orderProjectStatusHistoryQuery($query)
    {
        return $query
            ->select([
                "trs_pc_status_implementation.id",
                "trs_pc_status_implementation.project_id",
                "trs_pc_status_implementation.month",
                "trs_pc_status_implementation.year",
                "trs_pc_status_implementation.status",
            ])
            ->orderBy("trs_pc_status_implementation.year", "desc")
            ->orderByRaw(
                "CASE
                WHEN trs_pc_status_implementation.month = 'Desember' THEN 12
                WHEN trs_pc_status_implementation.month = 'November' THEN 11
                WHEN trs_pc_status_implementation.month = 'Oktober' THEN 10
                WHEN trs_pc_status_implementation.month = 'September' THEN 9
                WHEN trs_pc_status_implementation.month = 'Agustus' THEN 8
                WHEN trs_pc_status_implementation.month = 'Juli' THEN 7
                WHEN trs_pc_status_implementation.month = 'Juni' THEN 6
                WHEN trs_pc_status_implementation.month = 'Mei' THEN 5
                WHEN trs_pc_status_implementation.month = 'April' THEN 4
                WHEN trs_pc_status_implementation.month = 'Maret' THEN 3
                WHEN trs_pc_status_implementation.month = 'Februari' THEN 2
                WHEN trs_pc_status_implementation.month = 'Januari' THEN 1
                ELSE 0 END DESC",
            )
            ->orderBy("trs_pc_status_implementation.id", "desc");
    }

    private function orderReviewProjectStatusHistoryQuery($query)
    {
        return $query
            ->select([
                "trs_review_pc_status_implementation.id",
                "trs_review_pc_status_implementation.project_id",
                "trs_review_pc_status_implementation.start",
                "trs_review_pc_status_implementation.end",
                "trs_review_pc_status_implementation.year",
                "trs_review_pc_status_implementation.review_status",
            ])
            ->orderBy("trs_review_pc_status_implementation.year", "desc")
            ->orderByRaw(
                "CASE
                WHEN trs_review_pc_status_implementation.start = 'Desember' THEN 12
                WHEN trs_review_pc_status_implementation.start = 'November' THEN 11
                WHEN trs_review_pc_status_implementation.start = 'Oktober' THEN 10
                WHEN trs_review_pc_status_implementation.start = 'September' THEN 9
                WHEN trs_review_pc_status_implementation.start = 'Agustus' THEN 8
                WHEN trs_review_pc_status_implementation.start = 'Juli' THEN 7
                WHEN trs_review_pc_status_implementation.start = 'Juni' THEN 6
                WHEN trs_review_pc_status_implementation.start = 'Mei' THEN 5
                WHEN trs_review_pc_status_implementation.start = 'April' THEN 4
                WHEN trs_review_pc_status_implementation.start = 'Maret' THEN 3
                WHEN trs_review_pc_status_implementation.start = 'Februari' THEN 2
                WHEN trs_review_pc_status_implementation.start = 'Januari' THEN 1
                ELSE 0 END DESC",
            )
            ->orderBy("trs_review_pc_status_implementation.id", "desc");
    }

    private function normalizeDigitalRoadmapCoeName(string $rawName): string
    {
        $name = strtolower(trim($rawName));
        if ($name === "") {
            return "CoE Not Identified";
        }
        if (str_contains($name, "ai") || str_contains($name, "analytics")) {
            return "AI / Adv. Analytics";
        }
        if (str_contains($name, "cloud")) {
            return "Advance Cloud";
        }
        if (str_contains($name, "iot")) {
            return "IoT";
        }
        if (str_contains($name, "rpa")) {
            return "RPA";
        }
        return "CoE Not Identified";
    }
    private function normalizeImplementationStatus(string $rawStatus): ?string
    {
        $status = strtolower(trim($rawStatus));
        if ($status === "") {
            return null;
        }
        if (
            str_contains($status, "done") ||
            str_contains($status, "complete")
        ) {
            return "Done";
        }
        if (str_contains($status, "review")) {
            return "On Review";
        }
        if (str_contains($status, "progress")) {
            return "On Progress";
        }
        return ucwords($status);
    }
    private function resolveDigitalInitiativeBadgeLabel(
        string $initiativeCode,
        int &$fallbackNumber,
    ): string {
        if ($initiativeCode !== "") {
            if (preg_match("/(\d+)/", $initiativeCode, $matches) === 1) {
                return (string) ((int) $matches[1]);
            }
            return $initiativeCode;
        }
        return (string) $fallbackNumber++;
    }
    private function quarterIndex(int $year, string $quarter): int
    {
        if (
            $year <= 0 ||
            preg_match("/Q?([1-4])/", strtoupper(trim($quarter)), $matches) !==
                1
        ) {
            return 0;
        }
        return $year * 4 + (int) $matches[1];
    }
    private function resolveQuarterDate(
        int $year,
        string $quarter,
        bool $isEndDate,
    ): ?string {
        if (
            $year <= 0 ||
            preg_match("/Q?([1-4])/", strtoupper(trim($quarter)), $matches) !==
                1
        ) {
            return null;
        }
        $quarterNumber = (int) $matches[1];
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $month = $isEndDate ? $startMonth + 2 : $startMonth;
        $day = $isEndDate ? (int) date("t", strtotime("$year-$month-01")) : 1;
        return sprintf("%04d-%02d-%02d", $year, $month, $day);
    }

    public function storeItBuildingBlockMapping(array $data): int
    {
        return $this->itBuildingBlockService->storeMapping($data);
    }
    public function deleteItBuildingBlockPrimary(int $primaryId): void
    {
        $this->itBuildingBlockService->deletePrimary($primaryId);
    }
    public function deleteItBuildingBlockSecondary(
        int $primaryId,
        int $secondaryId,
    ): void {
        $this->itBuildingBlockService->deleteSecondary(
            $primaryId,
            $secondaryId,
        );
    }
    public function deleteItBuildingBlockInitiative(
        int $primaryId,
        int $secondaryId,
        int $initiativeId,
    ): void {
        $this->itBuildingBlockService->deleteInitiative(
            $primaryId,
            $secondaryId,
            $initiativeId,
        );
    }
    public function deleteItBuildingBlockMultipleMappings(array $removals): void
    {
        $this->itBuildingBlockService->deleteMultipleMappings($removals);
    }

    private function filterCoeCatalogByType(
        Collection $catalog,
        int $initiativeType,
    ): Collection {
        return $catalog->mapWithKeys(function (MstCoe $coe) use (
            $initiativeType,
        ): array {
            $filteredCoe = clone $coe;
            $filteredCoe->setRelation(
                "initiatives",
                $coe->initiatives
                    ->where("tipe_initiative", $initiativeType)
                    ->values(),
            );
            return [Str::lower($coe->name) => $this->mapCoeCard($filteredCoe)];
        });
    }
    private function buildSectionCards(
        Collection $coeCatalog,
        array $configs,
        bool $showEmpty,
    ): array {
        return collect($configs)
            ->map(
                fn(array $config): ?array => $this->buildSingleCard(
                    $coeCatalog,
                    $config,
                ),
            )
            ->filter(
                fn(?array $card): bool => $card !== null &&
                    ($showEmpty || !$card["is_empty"]),
            )
            ->values()
            ->all();
    }
    private function buildSingleCard(
        Collection $coeCatalog,
        array $config,
    ): ?array {
        $baseCard = $coeCatalog->get(Str::lower($config["name"]));
        if ($baseCard === null) {
            return null;
        }
        return [
            ...$baseCard,
            "display_name" => $config["label"] ?? $baseCard["display_name"],
            "tone" => $config["tone"] ?? $baseCard["tone"],
            "description_lines" => $config["description_lines"] ?? [],
        ];
    }
    private function mapCoeCard(MstCoe $coe): array
    {
        $initiatives = ($coe->initiatives ?? collect())
            ->map(fn($i) => $this->mapInitiativeForSummary($i))
            ->values();
        $previewInitiatives = $initiatives->take(3)->values();
        return [
            "id" => (int) $coe->id,
            "name" => $coe->name,
            "display_name" => $coe->name,
            "tone" => "default",
            "initiatives_count" => $initiatives->count(),
            "is_empty" => $initiatives->isEmpty(),
            "initiatives" => $initiatives->all(),
            "initiatives_preview" => $previewInitiatives->all(),
            "remaining_initiatives_count" => max(
                0,
                $initiatives->count() - $previewInitiatives->count(),
            ),
            "status_breakdown" => $this->buildStatusBreakdown($initiatives),
        ];
    }
    private function buildSummary(
        array $t,
        array $s,
        ?array $f,
        ?array $a,
        ?array $tb,
        array $u,
    ): array {
        $all = collect([...$t, ...$s, ...array_filter([$f, $a, $tb])]);
        return [
            "total_initiatives" =>
                (int) $all->sum("initiatives_count") + count($u),
            "mapped_initiatives" => (int) $all->sum("initiatives_count"),
            "technology_initiatives" => (int) collect($t)->sum(
                "initiatives_count",
            ),
            "active_coe_count" => (int) $all
                ->filter(fn($c) => ($c["initiatives_count"] ?? 0) > 0)
                ->count(),
            "unassigned_count" => count($u),
            "top_coe_name" =>
                $all->sortByDesc("initiatives_count")->first()[
                    "display_name"
                ] ?? "-",
            "top_coe_count" =>
                (int) ($all->sortByDesc("initiatives_count")->first()[
                    "initiatives_count"
                ] ?? 0),
        ];
    }
    private function getDualGrowthGoals(): array
    {
        $direct = $this->getDirectDualGrowthInitiatives();
        $goals = Goal::query()
            ->with([
                "themes" => fn($query) => $query->orderBy("theme_number"),
                "themes.initiativeTaggings.initiative" => fn(
                    $query,
                ) => $query->select(self::INITIATIVE_BASE_SELECT),
                "themes.initiativeTaggings.initiative.coe:id,name",
                "themes.initiativeTaggings.initiative.organization:id,name,groub_id",
                "themes.initiativeTaggings.initiative.latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "themes.initiativeTaggings.initiative.statusImplementations:id,initiative_id,start,end,year,review_status",
                "themes.initiativeTaggings.initiative.mappedProjects:id",
            ])
            ->where("pilar", 2)
            ->orderByRaw(
                "case code when 'A' then 1 when 'B' then 2 else 99 end",
            )
            ->orderBy("code")
            ->get();
        return $goals
            ->map(
                fn($g) => $this->mapDualGrowthGoal(
                    $g,
                    $direct->get(strtoupper((string) $g->code), []),
                ),
            )
            ->values()
            ->all();
    }
    private function getDirectDualGrowthInitiatives(): Collection
    {
        return InitiativeTagging::query()
            ->with([
                "initiative" => fn($query) => $query->select(
                    self::INITIATIVE_BASE_SELECT,
                ),
                "initiative.coe:id,name",
                "initiative.organization:id,name,groub_id",
                "initiative.latestStatusImplementation" => fn(
                    $query,
                ) => $this->selectLatestImplementationStatus($query),
                "initiative.statusImplementations:id,initiative_id,start,end,year,review_status",
                "initiative.mappedProjects:id",
            ])
            ->where("pilar", 2)
            ->whereNull("themes_id")
            ->get()
            ->groupBy(fn($t) => strtoupper((string) $t->goal))
            ->map(
                fn($ts) => ($tappings = $ts
                    ->map(fn($t) => $t->initiative)
                    ->filter()
                    ->unique("id")
                    ->sortBy(
                        fn($i) => sprintf(
                            "%08s-%s",
                            (string) $i->code,
                            (string) $i->name,
                        ),
                    )
                    ->values()
                    ->map(fn($i) => $this->mapDualGrowthInitiative($i))
                    ->all()),
            );
    }
    private function mapDualGrowthGoal(Goal $g, array $d = []): array
    {
        $ts = collect($g->themes ?? [])
            ->map(fn($t) => $this->mapDualGrowthTheme($t))
            ->values();
        return [
            "id" => (int) $g->id,
            "code" => (string) $g->code,
            "title" => (string) $g->title,
            "themes" => $ts->all(),
            "direct_initiatives_count" => count($d),
            "direct_initiatives" => $d,
            "initiatives_count" =>
                (int) $ts->sum("initiatives_count") + count($d),
        ];
    }
    private function mapDualGrowthTheme($t): array
    {
        $is = collect($t->initiativeTaggings ?? [])
            ->map(fn($tg) => $tg->initiative)
            ->filter()
            ->unique("id")
            ->sortBy(
                fn($i) => sprintf(
                    "%08s-%s",
                    (string) $i->code,
                    (string) $i->name,
                ),
            )
            ->values()
            ->map(fn($i) => $this->mapDualGrowthInitiative($i))
            ->all();
        return [
            "id" => (int) $t->id,
            "theme_number" => (int) $t->theme_number,
            "name" => (string) $t->name,
            "label" => (string) $t->name,
            "initiatives_count" => count($is),
            "initiatives" => $is,
        ];
    }
    private function mapDualGrowthInitiative(
        $i,
        bool $includeFullHistory = false,
    ): array {
        $implData = $this->resolveImplementationData($i, $includeFullHistory);

        return [
            "id" => (int) $i->id,
            "code" => $i->code,
            "name" => $i->name,
            "description" => $i->description,
            "coe_id" => $i->coe_id ? (int) $i->coe_id : null,
            "coe_name" => $i->coe?->name,
            "label" => trim(
                collect([$i->code, $i->name])
                    ->filter()
                    ->implode(" - "),
            ),
            "tipe_initiative" => (int) $i->tipe_initiative,
            "business_unit" => $i->organization?->name,
            "groub_id" => $i->organization
                ? (int) $i->organization->groub_id
                : null,
            "implementation_status" => $implData["implementation_status"],
            "statuses" => $implData["statuses"],
            "source" => !is_null($i->source) ? (int) $i->source : null,
            "mapped_project_id" => $i->mappedProjects?->first()?->id,
        ];
    }
    private function getFocusBandGoals(): array
    {
        return Goal::query()
            ->select(["id", "code", "title"])
            ->where("pilar", 2)
            ->orderByRaw(
                "case code when 'A' then 1 when 'B' then 2 else 99 end",
            )
            ->orderBy("code")
            ->get()
            ->map(
                fn(Goal $goal): array => [
                    "id" => (int) $goal->id,
                    "code" => (string) $goal->code,
                    "title" => (string) $goal->title,
                ],
            )
            ->values()
            ->all();
    }
    private function getFocusBands(array $gs): array
    {
        return collect($gs)
            ->map(
                fn($g) => [
                    "id" => is_numeric($g["id"])
                        ? "goal-" . $g["id"]
                        : (string) $g["id"],
                    "code" => $g["code"],
                    "title" => $g["title"],
                    "label" => $g["title"],
                ],
            )
            ->values()
            ->all();
    }
    private function getRoofSection(): array
    {
        $gs = Goal::query()
            ->with(["themes" => fn($q) => $q->orderBy("theme_number")])
            ->where("pilar", "2")
            ->whereIn("code", ["A", "B"])
            ->get()
            ->keyBy("code");
        $m = $gs->get("A");
        $s = $gs->get("B");
        return [
            "main_goal" => $m
                ? [
                    "id" => (int) $m->id,
                    "code" => $m->code,
                    "title" => $m->title,
                ]
                : null,
            "main_goal_themes" => collect($m?->themes ?? [])
                ->take(2)
                ->map(
                    fn($t) => [
                        "id" => (int) $t->id,
                        "theme_number" => (int) $t->theme_number,
                        "name" => $t->name,
                        "label" => $t->name,
                    ],
                )
                ->values()
                ->all(),
            "side_goal" => $s
                ? [
                    "id" => (int) $s->id,
                    "code" => $s->code,
                    "title" => $s->title,
                ]
                : null,
        ];
    }
    private function buildStatusBreakdown(Collection $is): array
    {
        return collect(["drafting", "propose", "review", "approved", "other"])
            ->map(
                fn($s) => [
                    "key" => $s,
                    "label" => $this->statusLabel($s),
                    "count" => (int) $is->where("status", $s)->count(),
                ],
            )
            ->filter(fn($i) => $i["count"] > 0)
            ->values()
            ->all();
    }
    private function normalizeStatus(?string $s): string
    {
        $v = Str::lower(trim((string) $s));
        if (
            $v === "" ||
            in_array($v, ["0", "1", "draft", "drafting", "not start"], true)
        ) {
            return "drafting";
        }
        if ($v === "2" || Str::contains($v, "propose")) {
            return "propose";
        }
        if ($v === "3" || Str::contains($v, "review")) {
            return "review";
        }
        if (in_array($v, ["4", "approve", "approved", "aproved"], true)) {
            return "approved";
        }
        return "other";
    }
    private function statusLabel(string $s): string
    {
        return match ($s) {
            "drafting" => "Drafting",
            "propose" => "Propose",
            "review" => "Review",
            "approved" => "Approved",
            default => "Other",
        };
    }

    private function resolveSafeHeavyProp(
        string $key,
        callable $resolver,
    ): mixed {
        try {
            return $resolver();
        } catch (\Throwable $e) {
            Log::warning(
                "[StrategicHousePageService] Failed to load heavy prop.",
                [
                    "prop" => $key,
                    "message" => $e->getMessage(),
                    "default_connection" => config("database.default"),
                ],
            );

            return $this->fallbackHeavyPropValue($key);
        }
    }

    private function buildFallbackPageProps(
        array $normalizedFilters,
        int $initiativeType,
        array $selectedProps = [],
    ): array {
        $baseProps = [
            "filters" => $normalizedFilters,
            "page" => [
                "title" => "Strategic House",
                "headline" => "Pertamina Group Dual Growth Strategy",
                "visionTitle" => "Visi Pertamina IT",
                "visionText" =>
                    "Meningkatkan peranan IT dari business enabler menjadi strategic value creator, mendorong transformasi digital untuk mendukung ambisi dual growth Pertamina Group.",
                "initiativeLabel" =>
                    $initiativeType === 2
                        ? "IT transformation initiatives"
                        : "Digital transformation initiatives",
                "grandStrategyTitle" => "Grand IT Strategy",
                "grandStrategyText" =>
                    "Single source of truth for groupwide IT reference architecture",
            ],
            "roofSection" => [
                "main_goal" => null,
                "main_goal_themes" => [],
                "side_goal" => null,
            ],
            "focusBands" => [],
            "coeOptions" => collect([]),
            "statusPeriods" => [],
        ];

        $fallbackHeavyProps = [
            "summary" => $this->fallbackHeavyPropValue("summary"),
            "technologyCards" => $this->fallbackHeavyPropValue(
                "technologyCards",
            ),
            "strategyCards" => $this->fallbackHeavyPropValue("strategyCards"),
            "foundationCard" => $this->fallbackHeavyPropValue("foundationCard"),
            "architectureCard" => $this->fallbackHeavyPropValue(
                "architectureCard",
            ),
            "tbcCard" => $this->fallbackHeavyPropValue("tbcCard"),
            "unassignedInitiatives" => $this->fallbackHeavyPropValue(
                "unassignedInitiatives",
            ),
            "mappingBusinessStrategyGroups" => $this->fallbackHeavyPropValue(
                "mappingBusinessStrategyGroups",
            ),
            "mappingBusinessStrategyColumns" => $this->fallbackHeavyPropValue(
                "mappingBusinessStrategyColumns",
            ),
            "mappingBusinessStrategyOrganizationOptions" => $this->fallbackHeavyPropValue(
                "mappingBusinessStrategyOrganizationOptions",
            ),
            "businessStrategyPage" => $this->fallbackHeavyPropValue(
                "businessStrategyPage",
            ),
            "businessStrategySummary" => $this->fallbackHeavyPropValue(
                "businessStrategySummary",
            ),
            "businessStrategyHeaderGoals" => $this->fallbackHeavyPropValue(
                "businessStrategyHeaderGoals",
            ),
            "businessStrategyEnablerGoals" => $this->fallbackHeavyPropValue(
                "businessStrategyEnablerGoals",
            ),
            "businessStrategyGroups" => $this->fallbackHeavyPropValue(
                "businessStrategyGroups",
            ),
            "businessStrategyColumns" => $this->fallbackHeavyPropValue(
                "businessStrategyColumns",
            ),
            "businessStrategyOrganizationOptions" => $this->fallbackHeavyPropValue(
                "businessStrategyOrganizationOptions",
            ),
            "dualGrowthGoals" => $this->fallbackHeavyPropValue(
                "dualGrowthGoals",
            ),
            "digitalInitiativeOptions" => $this->fallbackHeavyPropValue(
                "digitalInitiativeOptions",
            ),
            "itBuildingBlockMatrix" => $this->fallbackHeavyPropValue(
                "itBuildingBlockMatrix",
            ),
            "itInitiativeOptions" => $this->fallbackHeavyPropValue(
                "itInitiativeOptions",
            ),
            "initiativeSupportGroups" => $this->fallbackHeavyPropValue(
                "initiativeSupportGroups",
            ),
            "initiativeSupportDigitalOptions" => $this->fallbackHeavyPropValue(
                "initiativeSupportDigitalOptions",
            ),
            "initiativeSupportItOptions" => $this->fallbackHeavyPropValue(
                "initiativeSupportItOptions",
            ),
            "mapTechnologies" => $this->fallbackHeavyPropValue(
                "mapTechnologies",
            ),
            "mapTechnologyCoeOptions" => $this->fallbackHeavyPropValue(
                "mapTechnologyCoeOptions",
            ),
            "mapTechnologyInitiativeOptions" => $this->fallbackHeavyPropValue(
                "mapTechnologyInitiativeOptions",
            ),
            "strategicPillars" => $this->fallbackHeavyPropValue(
                "strategicPillars",
            ),
            "taggings" => $this->fallbackHeavyPropValue("taggings"),
            "allGoals" => $this->fallbackHeavyPropValue("allGoals"),
            "allOrganizations" => $this->fallbackHeavyPropValue(
                "allOrganizations",
            ),
            "allInitiatives" => $this->fallbackHeavyPropValue("allInitiatives"),
            "allThemes" => $this->fallbackHeavyPropValue("allThemes"),
            "matrixInitiatives" => $this->fallbackHeavyPropValue(
                "matrixInitiatives",
            ),
            "pilarOptions" => $this->fallbackHeavyPropValue("pilarOptions"),
            "pillarFilters" => $this->fallbackHeavyPropValue("pillarFilters"),
            "mstInitiatives" => $this->fallbackHeavyPropValue("mstInitiatives"),
            "initiativeRelations" => $this->fallbackHeavyPropValue(
                "initiativeRelations",
            ),
            "modelRelationOptions" => $this->fallbackHeavyPropValue(
                "modelRelationOptions",
            ),
            "typeRelationOptions" => $this->fallbackHeavyPropValue(
                "typeRelationOptions",
            ),
            "itRoadmapGroups" => $this->fallbackHeavyPropValue(
                "itRoadmapGroups",
            ),
            "itRoadmapStartYear" => $this->fallbackHeavyPropValue(
                "itRoadmapStartYear",
            ),
            "itRoadmapEndYear" => $this->fallbackHeavyPropValue(
                "itRoadmapEndYear",
            ),
            "itRoadmapTotalCount" => $this->fallbackHeavyPropValue(
                "itRoadmapTotalCount",
            ),
            "itRoadmapMilestoneTypeOptions" => $this->fallbackHeavyPropValue(
                "itRoadmapMilestoneTypeOptions",
            ),
            "digitalRoadmapGroups" => $this->fallbackHeavyPropValue(
                "digitalRoadmapGroups",
            ),
            "digitalRoadmapTotalCount" => $this->fallbackHeavyPropValue(
                "digitalRoadmapTotalCount",
            ),
            "digitalRoadmapStartYear" => $this->fallbackHeavyPropValue(
                "digitalRoadmapStartYear",
            ),
            "digitalRoadmapEndYear" => $this->fallbackHeavyPropValue(
                "digitalRoadmapEndYear",
            ),
        ];

        if (empty($selectedProps)) {
            return array_merge($baseProps, $fallbackHeavyProps);
        }

        return array_merge(
            $baseProps,
            array_intersect_key(
                $fallbackHeavyProps,
                array_flip($selectedProps),
            ),
        );
    }

    private function fallbackHeavyPropValue(string $key): mixed
    {
        return match ($key) {
            "summary" => [
                "total_initiatives" => 0,
                "mapped_initiatives" => 0,
                "technology_initiatives" => 0,
                "active_coe_count" => 0,
                "unassigned_count" => 0,
                "top_coe_name" => "-",
                "top_coe_count" => 0,
            ],
            "foundationCard", "architectureCard", "tbcCard" => null,
            "itRoadmapStartYear",
            "itRoadmapEndYear",
            "digitalRoadmapStartYear",
            "digitalRoadmapEndYear"
                => null,
            "itRoadmapTotalCount", "digitalRoadmapTotalCount" => 0,
            default => [],
        };
    }
}
