<template>
    <ModulLayout :title="page.title">
        <div class="strategic-house animate-fade-in">
            <!-- View Mode Switcher -->
            <div class="inline-flex items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5 w-fit mb-2">
                <button
                    v-for="mode in viewModeEntries"
                    :key="mode.key"
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        activeViewMode === mode.key
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="setViewMode(mode.key)"
                >
                    {{ mode.label }}
                </button>
            </div>

            <!-- Progress Bar (0-100%) -->
            <ProgressBar
                :progress="loadingProgress"
                :visible="isReloading"
                :label="`Processing ${pendingViewLabel}`"
            />

            <!-- Dual Growth Enabler Toggle -->
            <div
                v-if="viewMode === 'dual-growth'"
                class="mb-5 flex justify-end"
            >
                <button
                    type="button"
                    @click="showEnabler = !showEnabler"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all bg-slate-200/50 hover:bg-slate-300/50 dark:bg-white/5 dark:hover:bg-white/10 text-slate-700 dark:text-slate-200"
                >
                    <component
                        :is="showEnabler ? EyeIcon : EyeSlashIcon"
                        class="w-3.5 h-3.5"
                    />
                    {{ showEnabler ? "Hide Enabler" : "Show Enabler" }}
                </button>
            </div>

            <!-- Roadmap Summary Button in Roadmap Tab -->
            <div
                v-if="viewMode === 'roadmap'"
                class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="inline-flex w-fit items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-[10px] font-bold tracking-wider transition-all"
                        :class="
                            activeRoadmapMode === 'all'
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        "
                        @click="setRoadmapMode('all')"
                    >
                        All
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-[10px] font-bold tracking-wider transition-all"
                        :class="
                            activeRoadmapMode === 'digital'
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        "
                        @click="setRoadmapMode('digital')"
                    >
                        Digital Initiative
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-[10px] font-bold tracking-wider transition-all"
                        :class="
                            activeRoadmapMode === 'it'
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        "
                        @click="setRoadmapMode('it')"
                    >
                        IT Initiative
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        v-if="roadmapMode !== 'all'"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-[#0B2A8A] bg-white px-3 py-1.5 text-[10px] font-bold tracking-wider text-[#0B2A8A] transition hover:bg-[#0B2A8A] hover:text-white dark:border-[#53BDE6] dark:bg-transparent dark:text-[#53BDE6] dark:hover:bg-[#53BDE6] dark:hover:text-[#171717]"
                        :title="showRoadmapFilters ? 'Hide Filters' : 'Show Filters'"
                        @click="showRoadmapFilters = !showRoadmapFilters"
                    >
                        <component :is="showRoadmapFilters ? EyeIcon : EyeSlashIcon" class="h-3.5 w-3.5" />
                        {{ showRoadmapFilters ? 'Hide Filters' : 'Show Filters' }}
                    </button>
                    <Link
                        :href="route('itsp.strategic-house.roadmap-summary.index')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-[#0B2A8A] bg-white px-3 py-1.5 text-[10px] font-bold tracking-wider text-[#0B2A8A] transition-all hover:bg-[#0B2A8A] hover:text-white dark:border-[#53BDE6] dark:bg-transparent dark:text-[#53BDE6] dark:hover:bg-[#53BDE6] dark:hover:text-[#171717]"
                    >
                        📊 Roadmap Summary
                    </Link>
                </div>
            </div>

            <!-- Conditional View Rendering -->
            <div class="relative min-h-100">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform opacity-0 translate-y-4"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-4"
                    mode="out-in"
                >
                    <!-- Loading / Skeleton State -->
                    <div v-if="isLoading || (isReloading && !hasLoadedView(viewMode))" key="loading">
                        <StrategicHouseSkeleton />
                    </div>

                    <section v-else-if="viewMode === 'mapping'" key="mapping">
                        <StretegicHouse
                            :page="page"
                            :summary="summary"
                            :roof-section="roofSection"
                            :technology-cards="technologyCards"
                            :strategy-cards="strategyCards"
                            :foundation-card="foundationCard"
                            :architecture-card="architectureCard"
                            :tbc-card="tbcCard"
                            :unassigned-initiatives="unassignedInitiatives"
                            :mapping-business-strategy-groups="mappingBusinessStrategyGroups"
                            :mapping-business-strategy-columns="mappingBusinessStrategyColumns"
                            :mapping-business-strategy-organization-options="mappingBusinessStrategyOrganizationOptions"
                            :status-periods="statusPeriods"
                        />
                    </section>

                    <div
                        v-else-if="viewMode === 'business-strategy'"
                        key="business-strategy"
                    >
                        <StrategicHouseBusinessStrategyPage
                            :embedded="true"
                            :page="businessStrategyPage"
                            :summary="businessStrategySummary"
                            :header-goals="businessStrategyHeaderGoals"
                            :enabler-goals="businessStrategyEnablerGoals"
                            :groups="businessStrategyGroups"
                            :strategy-columns="businessStrategyColumns"
                            :organization-options="
                                businessStrategyOrganizationOptions
                            "
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'dual-growth'"
                        key="dual-growth"
                    >
                        <DualGrowthFull
                            v-if="showEnabler"
                            :goals="dualGrowthGoals"
                            :status-periods="statusPeriods"
                        />
                        <DualGrowth
                            v-else
                            :goals="dualGrowthGoals"
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="
                            viewMode === 'digital-transformation-initiatives'
                        "
                        key="digital-transformation-initiatives"
                    >
                        <DigitalBuildingBlock
                            :items="digitalInitiativeOptions"
                            :coe-options="coeOptions"
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'initiative-support'"
                        key="initiative-support"
                    >
                        <StrategicHouseInitiativeSupportPage
                            :embedded="true"
                            :groups="initiativeSupportGroups"
                            :digital-initiative-options="
                                initiativeSupportDigitalOptions
                            "
                            :it-initiative-options="initiativeSupportItOptions"
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'it-building-blocs'"
                        key="it-building-blocs"
                    >
                        <StrategicHouseItBuildingBlocksPage
                            :embedded="true"
                            :groups="itBuildingBlockMatrix"
                            :coe-options="coeOptions"
                            :initiative-options="itInitiativeOptions"
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'it-initiatives'"
                        key="it-initiatives"
                    >
                        <ItBuildingBlocks
                            :items="itInitiativeOptions"
                            :coe-options="coeOptions"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'map-technology'"
                        key="map-technology"
                    >
                        <StrategicHouseMapTechnologyPage
                            :embedded="true"
                            :map-technologies="mapTechnologies"
                            :coe-options="mapTechnologyCoeOptions"
                            :initiative-options="mapTechnologyInitiativeOptions"
                            :status-periods="statusPeriods"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'initiative-relation'"
                        key="initiative-relation"
                    >
                        <StrategicHouseInitiativeRelationPage
                            :embedded="true"
                            :mst-initiatives="mstInitiatives"
                            :initiative-relations="initiativeRelations"
                            :model-relation-options="modelRelationOptions"
                        />
                    </div>

                    <div v-else-if="viewMode === 'roadmap'" key="roadmap">
                        <ItInitiativeRoadmapContent
                            v-if="roadmapMode === 'it'"
                            :show-filters="showRoadmapFilters"
                            :groups="itRoadmapGroups"
                            :start-year="itRoadmapStartYear"
                            :end-year="itRoadmapEndYear"
                            :total-count="itRoadmapTotalCount"
                            :milestone-type-options="
                                itRoadmapMilestoneTypeOptions
                            "
                        />
                        <ItInitiativeRoadmapContent
                            v-else-if="roadmapMode === 'digital'"
                            :show-filters="showRoadmapFilters"
                            :groups="digitalRoadmapGroups"
                            :start-year="digitalRoadmapStartYear"
                            :end-year="digitalRoadmapEndYear"
                            :total-count="digitalRoadmapTotalCount"
                            group-header-label="COE"
                            initiative-header-label="Digital Initiatives"
                            :show-organization-filter="true"
                            :show-roadmap-legend="false"
                            empty-text="Belum ada data roadmap Digital Initiative."
                        />
                        <AllInitiativeRoadmapContent
                            v-else
                            :it-groups="itRoadmapGroups"
                            :digital-groups="digitalRoadmapGroups"
                            :start-year="allRoadmapStartYear"
                            :end-year="allRoadmapEndYear"
                            :it-total-count="itRoadmapTotalCount"
                            :digital-total-count="digitalRoadmapTotalCount"
                            :milestone-type-options="itRoadmapMilestoneTypeOptions"
                        />
                    </div>

                    <div
                        v-else-if="viewMode === 'strategic-pillars'"
                        key="strategic-pillars"
                    >
                        <StrategicHouseStrategicPillarPage
                            :embedded="true"
                            :strategic-pillars="strategicPillars"
                            :all-goals="allGoals"
                            :taggings="taggings"
                            :all-initiatives="allInitiatives"
                            :all-themes="allThemes"
                            :matrix-initiatives="matrixInitiatives"
                            :all-organizations="allOrganizations"
                            :pilar-options="pilarOptions"
                            :filters="pillarFilters"
                            base-url-route="itsp.strategic-house.index"
                        />
                    </div>
                </Transition>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, defineAsyncComponent } from "vue";
import ProgressBar from "@/Components/Loading/ProgressBar.vue";
import StrategicHouseSkeleton from "@/Components/Loading/StrategicHouseSkeleton.vue";
import { Link, router, useRemember } from "@inertiajs/vue3";
import { EyeIcon, EyeSlashIcon } from "@heroicons/vue/24/outline";
import ModulLayout from "@/Layouts/ModulLayout.vue";

const loadMappingView = () => import("@/Components/modules/ITSP/StrategicHouse/StretegicHouse.vue");
const loadBusinessStrategyView = () => import("@/Pages/modules/ITSP/StrategicHouse/BusinessStrategy/Index.vue");
const loadDualGrowthView = () => import("@/Components/modules/ITSP/StrategicHouse/DualGrowth.vue");
const loadDualGrowthFullView = () => import("@/Components/modules/ITSP/StrategicHouse/DualGrowthFull.vue");
const loadDigitalBuildingBlockView = () => import("@/Components/modules/ITSP/ItBuildingBlocks/DigitalBuildingBlock.vue");
const loadItBuildingBlocksView = () => import("@/Components/modules/ITSP/ItBuildingBlocks/ItBuildingBlock.vue");
const loadInitiativeSupportView = () => import("@/Pages/modules/ITSP/StrategicHouse/InitiativeSupport/Index.vue");
const loadItBuildingBlocksManageView = () => import("@/Pages/modules/ITSP/StrategicHouse/ItBuildingBlocks/Index.vue");
const loadMapTechnologyView = () => import("@/Pages/modules/ITSP/StrategicHouse/MapTechnology/Index.vue");
const loadInitiativeRelationView = () => import("@/Pages/modules/ITSP/StrategicHouse/InitiativeRelation/Index.vue");
const loadItRoadmapContentView = () => import("@/Components/modules/ITSP/StrategicHouse/RoadMap/ItInitiativeRoadmapContent.vue");
const loadAllRoadmapContentView = () => import("@/Components/modules/ITSP/StrategicHouse/RoadMap/AllInitiativeRoadmapContent.vue");
const loadStrategicPillarView = () => import("@/Pages/modules/ITSP/StrategicHouse/StrategicPillar/Index.vue");

const StretegicHouse = defineAsyncComponent(loadMappingView);
const StrategicHouseBusinessStrategyPage = defineAsyncComponent(loadBusinessStrategyView);
const DualGrowth = defineAsyncComponent(loadDualGrowthView);
const DualGrowthFull = defineAsyncComponent(loadDualGrowthFullView);
const DigitalBuildingBlock = defineAsyncComponent(loadDigitalBuildingBlockView);
const ItBuildingBlocks = defineAsyncComponent(loadItBuildingBlocksView);
const StrategicHouseInitiativeSupportPage = defineAsyncComponent(loadInitiativeSupportView);
const StrategicHouseItBuildingBlocksPage = defineAsyncComponent(loadItBuildingBlocksManageView);
const StrategicHouseMapTechnologyPage = defineAsyncComponent(loadMapTechnologyView);
const StrategicHouseInitiativeRelationPage = defineAsyncComponent(loadInitiativeRelationView);
const ItInitiativeRoadmapContent = defineAsyncComponent(loadItRoadmapContentView);
const AllInitiativeRoadmapContent = defineAsyncComponent(loadAllRoadmapContentView);
const StrategicHouseStrategicPillarPage = defineAsyncComponent(loadStrategicPillarView);

const loadingProgress = ref(0);
let progressInterval = null;

function startProgress() {
    loadingProgress.value = 0;
    if (progressInterval) clearInterval(progressInterval);
    
    // Fast to 30%, then slower to 90%
    progressInterval = setInterval(() => {
        if (loadingProgress.value < 30) {
            loadingProgress.value += Math.random() * 15;
        } else if (loadingProgress.value < 70) {
            loadingProgress.value += Math.random() * 5;
        } else if (loadingProgress.value < 95) {
            loadingProgress.value += Math.random() * 1.5;
        }
    }, 200);
}

onBeforeUnmount(() => {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
});

const viewModeEntries = [
    { key: 'mapping', label: 'Strategic House' },
    { key: 'business-strategy', label: 'Business Strategy' },
    { key: 'dual-growth', label: 'Dual Growth Strategy' },
    { key: 'digital-transformation-initiatives', label: 'Digital Transformation Initiatives' },
    { key: 'it-building-blocs', label: 'IT Building Blocks' },
    { key: 'it-initiatives', label: 'IT Initiatives' },
    { key: 'map-technology', label: 'Map Technology' },
    { key: 'initiative-relation', label: 'Initiative Relations' },
    { key: 'initiative-support', label: 'Initiative Support' },
    { key: 'roadmap', label: 'Roadmap' },
    { key: 'strategic-pillars', label: 'Strategic Pillars' },
];

function finishProgress() {
    if (progressInterval) clearInterval(progressInterval);
    loadingProgress.value = 100;
    setTimeout(() => {
        if (!isReloading.value) loadingProgress.value = 0;
    }, 500);
}

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    businessStrategyPage: {
        type: Object,
        default: () => ({}),
    },
    businessStrategySummary: {
        type: Object,
        default: () => ({}),
    },
    businessStrategyHeaderGoals: {
        type: Object,
        default: () => ({}),
    },
    businessStrategyEnablerGoals: {
        type: Array,
        default: () => [],
    },
    businessStrategyGroups: {
        type: Array,
        default: () => [],
    },
    businessStrategyColumns: {
        type: Array,
        default: () => [],
    },
    businessStrategyOrganizationOptions: {
        type: Array,
        default: () => [],
    },
    // Lightweight mapping-tab business strategy props
    mappingBusinessStrategyGroups: {
        type: Array,
        default: () => [],
    },
    mappingBusinessStrategyColumns: {
        type: Array,
        default: () => [],
    },
    mappingBusinessStrategyOrganizationOptions: {
        type: Array,
        default: () => [],
    },
    roofSection: {
        type: Object,
        default: () => ({
            main_goal: null,
            main_goal_themes: [],
            side_goal: null,
        }),
    },
    focusBands: {
        type: Array,
        default: () => [],
    },
    dualGrowthGoals: {
        type: Array,
        default: () => [],
    },
    technologyCards: {
        type: Array,
        default: () => [],
    },
    strategyCards: {
        type: Array,
        default: () => [],
    },
    foundationCard: {
        type: Object,
        default: null,
    },
    architectureCard: {
        type: Object,
        default: null,
    },
    tbcCard: {
        type: Object,
        default: null,
    },
    unassignedInitiatives: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    digitalInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    itBuildingBlockMatrix: {
        type: Array,
        default: () => [],
    },
    itInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeSupportGroups: {
        type: Array,
        default: () => [],
    },
    initiativeSupportDigitalOptions: {
        type: Array,
        default: () => [],
    },
    initiativeSupportItOptions: {
        type: Array,
        default: () => [],
    },
    mapTechnologies: {
        type: [Array, Object],
        default: () => ({}),
    },
    mapTechnologyCoeOptions: {
        type: Array,
        default: () => [],
    },
    mapTechnologyInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
    // Roadmap props
    itRoadmapGroups: { type: Array, default: () => [] },
    itRoadmapStartYear: { type: Number, default: 2025 },
    itRoadmapEndYear: { type: Number, default: 2029 },
    itRoadmapTotalCount: { type: Number, default: 0 },
    itRoadmapMilestoneTypeOptions: { type: Array, default: () => [] },
    digitalRoadmapGroups: { type: Array, default: () => [] },
    digitalRoadmapTotalCount: { type: Number, default: 0 },
    digitalRoadmapStartYear: { type: Number, default: 2024 },
    digitalRoadmapEndYear: { type: Number, default: 2029 },

    // Strategic Pillar props
    strategicPillars: { type: Array, default: () => [] },
    allGoals: { type: Array, default: () => [] },
    taggings: { type: Array, default: () => [] },
    allInitiatives: { type: Array, default: () => [] },
    allThemes: { type: Array, default: () => [] },
    matrixInitiatives: { type: Array, default: () => [] },
    allOrganizations: { type: Array, default: () => [] },
    pilarOptions: { type: Array, default: () => [] },
    pillarFilters: { type: Object, default: () => ({}) },

    // Initiative Relation props
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
    initiativeRelations: {
        type: Array,
        default: () => [],
    },
    modelRelationOptions: {
        type: Array,
        default: () => [],
    },
    typeRelationOptions: {
        type: Array,
        default: () => [],
    },
});

const availableViewModes = new Set([
    "mapping",
    "business-strategy",
    "dual-growth",
    "digital-transformation-initiatives",
    "it-building-blocs",
    "it-initiatives",
    "map-technology",
    "initiative-relation",
    "initiative-support",
    "roadmap",
    "strategic-pillars",
]);

const getInitialViewMode = () => {
    if (typeof window === "undefined") return "mapping";
    const urlParams = new URLSearchParams(window.location.search);
    const requestedView = urlParams.get("view");

    if (requestedView && availableViewModes.has(requestedView)) {
        return requestedView;
    }

    if (
        urlParams.has("pilar") ||
        urlParams.has("goal_id") ||
        urlParams.has("org_id")
    ) {
        return "strategic-pillars";
    }
    if (urlParams.has("model_relasi")) {
        return "initiative-relation";
    }
    return null; // Let useRemember decide if no URL params
};

const roadmapModes = new Set(["it", "digital", "all"]);
const VIEW_CACHE_TTL_MS = 5 * 60 * 1000;
const viewAssetLoaders = {
    mapping: [loadMappingView],
    "business-strategy": [loadBusinessStrategyView],
    "dual-growth": [loadDualGrowthView, loadDualGrowthFullView],
    "digital-transformation-initiatives": [loadDigitalBuildingBlockView],
    "it-building-blocs": [loadItBuildingBlocksManageView],
    "it-initiatives": [loadItBuildingBlocksView],
    "initiative-support": [loadInitiativeSupportView],
    "map-technology": [loadMapTechnologyView],
    "initiative-relation": [loadInitiativeRelationView],
    roadmap: [loadItRoadmapContentView, loadAllRoadmapContentView],
    "strategic-pillars": [loadStrategicPillarView],
};
const viewAssetPromises = new Map();

const getInitialRoadmapMode = () => {
    if (typeof window === "undefined") return "it";

    const urlParams = new URLSearchParams(window.location.search);
    const requestedMode = urlParams.get("roadmap");

    return requestedMode && roadmapModes.has(requestedMode)
        ? requestedMode
        : null; // Let useRemember decide if no URL params
};

function getInitialServerView() {
    return props.filters?.view || "mapping";
}

function getInitialServerRoadmapMode() {
    return props.filters?.roadmap || "it";
}

function preloadViewAssets(mode) {
    const loaders = viewAssetLoaders[mode] || [];

    if (loaders.length === 0) {
        return Promise.resolve();
    }

    if (viewAssetPromises.has(mode)) {
        return viewAssetPromises.get(mode);
    }

    const promise = Promise.all(loaders.map((loader) => loader())).catch((error) => {
        viewAssetPromises.delete(mode);
        throw error;
    });

    viewAssetPromises.set(mode, promise);

    return promise;
}

// Use Inertia's useRemember to cache state client-side
const viewMode = useRemember(getInitialViewMode() || "mapping", "StrategicHouse/viewMode");
const roadmapMode = useRemember(getInitialRoadmapMode() || "it", "StrategicHouse/roadmapMode");

function getViewCacheKey(mode, selectedRoadmapMode = roadmapMode.value) {
    if (mode === "roadmap") {
        return `roadmap:${selectedRoadmapMode || "it"}`;
    }

    return mode;
}

const initialCacheKey = getViewCacheKey(
    getInitialServerView(),
    getInitialServerRoadmapMode(),
);
const loadedViews = new Set([initialCacheKey]);
const viewFetchedAt = new Map([[initialCacheKey, Date.now()]]);

// Loading state
const isLoading = ref(true);
const isReloading = ref(false);
const pendingViewMode = ref(null);
const pendingRoadmapMode = ref(null);

// Human-readable tab labels for loading title
const viewModeLabels = {
    mapping: "Strategic House",
    "business-strategy": "Business Strategy",
    "dual-growth": "Dual Growth Strategy",
    "digital-transformation-initiatives": "Digital Transformation Initiatives",
    "it-building-blocs": "IT Building Blocks",
    "it-initiatives": "IT Initiatives",
    "map-technology": "Map Technology",
    "initiative-relation": "Initiative Relations",
    "initiative-support": "Initiative Support",
    roadmap: "Roadmap",
    "strategic-pillars": "Strategic Pillars",
};
const roadmapModeLabels = {
    all: "All",
    digital: "Digital Initiative",
    it: "IT Initiative",
};


const activeViewMode = computed(() => pendingViewMode.value ?? viewMode.value);
const activeRoadmapMode = computed(
    () => pendingRoadmapMode.value ?? roadmapMode.value,
);
const pendingViewLabel = computed(() => {
    if (pendingViewMode.value) {
        return viewModeLabels[pendingViewMode.value] || "Data";
    }

    if (pendingRoadmapMode.value) {
        return `Roadmap ${roadmapModeLabels[pendingRoadmapMode.value] || ""}`.trim();
    }

    return "Data";
});
const showEnabler = useRemember(false, "StrategicHouse/showEnabler");
const showRoadmapFilters = useRemember(false, "StrategicHouse/showRoadmapFilters");

function markViewLoaded(mode, selectedRoadmapMode = roadmapMode.value) {
    const cacheKey = getViewCacheKey(mode, selectedRoadmapMode);

    loadedViews.add(cacheKey);
    viewFetchedAt.set(cacheKey, Date.now());
}

function hasLoadedView(mode, selectedRoadmapMode = roadmapMode.value) {
    return loadedViews.has(getViewCacheKey(mode, selectedRoadmapMode));
}

function isViewStale(mode, selectedRoadmapMode = roadmapMode.value) {
    const fetchedAt = viewFetchedAt.get(getViewCacheKey(mode, selectedRoadmapMode));

    if (!fetchedAt) {
        return true;
    }

    return (Date.now() - fetchedAt) > VIEW_CACHE_TTL_MS;
}

function reloadViewData(mode, selectedRoadmapMode = roadmapMode.value, callbacks = {}) {
    const requestData = {
        ...props.filters,
        view: mode,
        // Sanitize: JS boolean `true` serializes to string "true" in URL,
        // which fails Laravel's boolean validation. Use 1/0 instead.
        show_empty: props.filters.show_empty ? 1 : 0,
    };

    if (mode === "roadmap") {
        requestData.roadmap = selectedRoadmapMode;
    } else {
        delete requestData.roadmap;
    }

    return new Promise((resolve) => {
        router.get(route("itsp.strategic-house.index"), requestData, {
            only: getPropsForView(mode, selectedRoadmapMode),
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
                markViewLoaded(mode, selectedRoadmapMode);
                callbacks.onSuccess?.();
            },
            onError: () => {
                callbacks.onError?.();
            },
            onFinish: () => {
                callbacks.onFinish?.();
                resolve();
            },
        });
    });
}

function reloadRoadmapData(mode, callbacks = {}) {
    return reloadViewData("roadmap", mode, callbacks);
}

// Sync URL on mount if useRemember restored a value that isn't in URL
onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const hasViewParam = urlParams.has("view");
    const hasRoadmapParam = urlParams.has("roadmap");
    const initialView = getInitialServerView();
    const initialRoadmapMode = getInitialServerRoadmapMode();

    if (!hasViewParam && viewMode.value !== "mapping") {
        isLoading.value = true;
        isReloading.value = true;
        startProgress();
        pendingViewMode.value = viewMode.value;
        pendingRoadmapMode.value = viewMode.value === "roadmap" ? roadmapMode.value : null;

        await Promise.all([
            preloadViewAssets(viewMode.value),
            reloadViewData(
                viewMode.value,
                viewMode.value === "roadmap" ? roadmapMode.value : undefined,
            ),
        ]);

        pendingRoadmapMode.value = null;
        pendingViewMode.value = null;
        finishProgress();
        isLoading.value = false;
        isReloading.value = false;
        return;
    }

    if (
        viewMode.value === "roadmap"
        && !hasRoadmapParam
        && roadmapMode.value !== initialRoadmapMode
    ) {
        isLoading.value = true;
        isReloading.value = true;
        startProgress();
        pendingViewMode.value = "roadmap";
        pendingRoadmapMode.value = roadmapMode.value;

        await Promise.all([
            preloadViewAssets("roadmap"),
            reloadRoadmapData(roadmapMode.value),
        ]);

        pendingRoadmapMode.value = null;
        pendingViewMode.value = null;
        finishProgress();
        isLoading.value = false;
        isReloading.value = false;
        return;
    }

    await preloadViewAssets(initialView);
    markViewLoaded(initialView, initialRoadmapMode);
    isLoading.value = false;
    syncViewModeInUrl(viewMode.value);
    if (viewMode.value === "roadmap") {
        syncRoadmapModeInUrl(roadmapMode.value);
    }
});

const allRoadmapStartYear = computed(() =>
    Math.min(
        Number(props.itRoadmapStartYear) || 2025,
        Number(props.digitalRoadmapStartYear) || 2024,
    ),
);

const allRoadmapEndYear = computed(() =>
    Math.max(
        Number(props.itRoadmapEndYear) || 2029,
        Number(props.digitalRoadmapEndYear) || 2029,
    ),
);

const syncViewModeInUrl = (mode) => {
    if (typeof window === "undefined") return;

    const url = new URL(window.location.href);
    url.searchParams.set("view", mode);

    if (mode !== "roadmap") {
        url.searchParams.delete("roadmap");
    }

    window.history.replaceState({}, "", url.toString());
};

const syncRoadmapModeInUrl = (mode) => {
    if (typeof window === "undefined") return;

    const url = new URL(window.location.href);
    url.searchParams.set("roadmap", mode);
    window.history.replaceState({}, "", url.toString());
};

const setViewMode = async (mode) => {
    if (mode === viewMode.value && !pendingViewMode.value) {
        return;
    }

    const selectedRoadmapMode = mode === "roadmap" ? roadmapMode.value : undefined;
    const loaded = hasLoadedView(mode, selectedRoadmapMode);
    const stale = loaded && isViewStale(mode, selectedRoadmapMode);

    if (loaded && !stale) {
        viewMode.value = mode;
        syncViewModeInUrl(mode);
        if (mode === "roadmap") {
            syncRoadmapModeInUrl(roadmapMode.value);
        }
        return;
    }

    pendingViewMode.value = mode;
    pendingRoadmapMode.value = mode === "roadmap" ? roadmapMode.value : null;
    isReloading.value = true;
    startProgress();

    if (loaded) {
        viewMode.value = mode;
        syncViewModeInUrl(mode);
        if (mode === "roadmap") {
            syncRoadmapModeInUrl(roadmapMode.value);
        }
    }

    try {
        await Promise.all([
            preloadViewAssets(mode),
            reloadViewData(mode, selectedRoadmapMode, {
                onSuccess: () => {
                    if (!loaded) {
                        viewMode.value = mode;
                        syncViewModeInUrl(mode);
                        if (mode === "roadmap") {
                            syncRoadmapModeInUrl(roadmapMode.value);
                        }
                    }
                },
            }),
        ]);
        finishProgress();
    } catch (error) {
        console.error("Navigation error:", error);
        if (progressInterval) clearInterval(progressInterval);
        loadingProgress.value = 0;
    } finally {
        setTimeout(() => {
            isReloading.value = false;
            pendingRoadmapMode.value = null;
            pendingViewMode.value = null;
        }, 300);
    }
};

const getPropsForView = (mode, selectedRoadmapMode = roadmapMode.value) => {
    const baseProps = ["filters", "page", "roofSection", "focusBands", "coeOptions", "statusPeriods"];
    const roadmapProps = {
        all: ["itRoadmapGroups", "itRoadmapStartYear", "itRoadmapEndYear", "itRoadmapTotalCount", "itRoadmapMilestoneTypeOptions", "digitalRoadmapGroups", "digitalRoadmapTotalCount", "digitalRoadmapStartYear", "digitalRoadmapEndYear"],
        digital: ["digitalRoadmapGroups", "digitalRoadmapTotalCount", "digitalRoadmapStartYear", "digitalRoadmapEndYear"],
        it: ["itRoadmapGroups", "itRoadmapStartYear", "itRoadmapEndYear", "itRoadmapTotalCount", "itRoadmapMilestoneTypeOptions"],
    };

    const viewProps = {
        mapping: ["summary", "technologyCards", "strategyCards", "foundationCard", "architectureCard", "tbcCard", "unassignedInitiatives", "mappingBusinessStrategyGroups", "mappingBusinessStrategyColumns", "mappingBusinessStrategyOrganizationOptions"],
        "business-strategy": ["businessStrategyPage", "businessStrategySummary", "businessStrategyHeaderGoals", "businessStrategyEnablerGoals", "businessStrategyGroups", "businessStrategyColumns", "businessStrategyOrganizationOptions"],
        "dual-growth": ["dualGrowthGoals"],
        "digital-transformation-initiatives": ["digitalInitiativeOptions"],
        "it-building-blocs": ["itBuildingBlockMatrix", "itInitiativeOptions"],
        "it-initiatives": ["itInitiativeOptions"],
        "initiative-support": ["initiativeSupportGroups", "initiativeSupportDigitalOptions", "initiativeSupportItOptions"],
        "map-technology": ["mapTechnologies", "mapTechnologyCoeOptions", "mapTechnologyInitiativeOptions"],
        "initiative-relation": ["mstInitiatives", "initiativeRelations", "modelRelationOptions", "typeRelationOptions"],
        roadmap: roadmapProps[selectedRoadmapMode] || roadmapProps.it,
        "strategic-pillars": ["strategicPillars", "allGoals", "taggings", "allInitiatives", "allThemes", "matrixInitiatives", "allOrganizations", "pilarOptions", "pillarFilters"],
    };

    return [...baseProps, ...(viewProps[mode] || [])];
};

const setRoadmapMode = async (mode) => {
    if (mode === roadmapMode.value && !pendingRoadmapMode.value) {
        return;
    }

    const loaded = hasLoadedView("roadmap", mode);
    const stale = loaded && isViewStale("roadmap", mode);

    if (loaded && !stale) {
        roadmapMode.value = mode;
        syncRoadmapModeInUrl(mode);
        return;
    }

    pendingRoadmapMode.value = mode;
    isReloading.value = true;
    startProgress();

    if (loaded) {
        roadmapMode.value = mode;
        syncRoadmapModeInUrl(mode);
    }

    try {
        await Promise.all([
            preloadViewAssets("roadmap"),
            reloadRoadmapData(mode, {
                onSuccess: () => {
                    if (!loaded) {
                        roadmapMode.value = mode;
                        syncRoadmapModeInUrl(mode);
                    }
                },
            }),
        ]);
        finishProgress();
    } catch (error) {
        console.error("Roadmap navigation error:", error);
        if (progressInterval) clearInterval(progressInterval);
        loadingProgress.value = 0;
    } finally {
        setTimeout(() => {
            isReloading.value = false;
            pendingRoadmapMode.value = null;
        }, 300);
    }
};
</script>
