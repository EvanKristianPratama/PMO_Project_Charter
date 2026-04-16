<template>
    <UserLayout :title="page.title">
        <div class="strategic-house animate-fade-in">
            <!-- View Mode Switcher -->
            <div
                class="inline-flex items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5 w-fit mb-5"
            >
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'mapping'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'mapping'"
                >
                    Strategic House
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'dual-growth'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'dual-growth'"
                >
                    Dual Growth Strategy
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'digital-transformation-initiatives'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'digital-transformation-initiatives'"
                >
                    Digital Transformation Initiatives
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'it-building-blocs'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'it-building-blocs'"
                >
                    IT Building Blocks
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'it-initiatives'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'it-initiatives'"
                >
                    IT Initiatives
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'initiative-relation'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'initiative-relation'"
                >
                    Initiative Relations
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'roadmap'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'roadmap'"
                >
                    Roadmap
                </button>
                <button
                    type="button"
                    class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="
                        viewMode === 'strategic-pillars'
                            ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                    "
                    @click="viewMode = 'strategic-pillars'"
                >
                    Strategic Pillars
                </button>
            </div>

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

            <!-- Conditional View Rendering -->
            <div class="relative min-h-[400px]">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform opacity-0 translate-y-4"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-4"
                    mode="out-in"
                >
                    <section v-if="viewMode === 'mapping'" key="mapping">
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
                        />
                    </section>

                    <div
                        v-else-if="viewMode === 'dual-growth'"
                        key="dual-growth"
                    >
                        <DualGrowthFull
                            v-if="showEnabler"
                            :goals="dualGrowthGoals"
                        />
                        <DualGrowth v-else :goals="dualGrowthGoals" />
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
                            :groups="itRoadmapGroups"
                            :start-year="itRoadmapStartYear"
                            :end-year="itRoadmapEndYear"
                            :total-count="itRoadmapTotalCount"
                            :milestone-type-options="
                                itRoadmapMilestoneTypeOptions
                            "
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
                            base-url-route="strategic-house.index"
                        />
                    </div>
                </Transition>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from "vue";
import { EyeIcon, EyeSlashIcon } from "@heroicons/vue/24/outline";
import ItInitiativeRoadmapContent from "@/Components/StrategicHouse/RoadMap/ItInitiativeRoadmapContent.vue";
import DualGrowth from "@/Components/StrategicHouse/DualGrowth.vue";
import DualGrowthFull from "@/Components/StrategicHouse/DualGrowthFull.vue";
import DigitalBuildingBlock from "@/Components/ItBuildingBlocks/DigitalBuildingBlock.vue";
import ItBuildingBlocks from "@/Components/ItBuildingBlocks/ItBuildingBlock.vue";
import StrategicHouseItBuildingBlocksPage from "@/Pages/ProgramPlanning/StrategicHouse/ItBuildingBlocks/Index.vue";
import StrategicHouseInitiativeRelationPage from "@/Pages/ProgramPlanning/StrategicHouse/InitiativeRelation/Index.vue";
import StrategicHouseStrategicPillarPage from "@/Pages/ProgramPlanning/StrategicHouse/StrategicPillar/Index.vue";
import StretegicHouse from "@/Components/StrategicHouse/StretegicHouse.vue";
import UserLayout from "@/Layouts/UserLayout.vue";

const props = defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
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
    // IT Roadmap props (pre-loaded, no HTTP request saat switch tab)
    itRoadmapGroups: { type: Array, default: () => [] },
    itRoadmapStartYear: { type: Number, default: 2025 },
    itRoadmapEndYear: { type: Number, default: 2029 },
    itRoadmapTotalCount: { type: Number, default: 0 },
    itRoadmapMilestoneTypeOptions: { type: Array, default: () => [] },

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

const getInitialViewMode = () => {
    if (typeof window === "undefined") return "mapping";
    const urlParams = new URLSearchParams(window.location.search);
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
    return "mapping";
};

const viewMode = ref(getInitialViewMode());
const showEnabler = ref(false);
</script>
