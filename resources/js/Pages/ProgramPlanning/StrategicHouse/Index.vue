<template>
    <UserLayout :title="page.title">
        <div class="strategic-house animate-fade-in">
            <!-- View Mode Switcher -->
            <div class="inline-flex items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5 w-fit mb-5">
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'mapping'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'mapping'">
                    Strategic House
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'digital-block'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'digital-block'">
                    Dual Growth Strategy V1
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'block'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'block'">
                    Dual Growth Strategy V2
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'digital-transformation-initiatives'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'digital-transformation-initiatives'">
                    Digital Transformation Initiatives
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'it-building-blocs'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'it-building-blocs'">
                    IT Building Blocks
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'it-initiatives'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'it-initiatives'">
                    IT Initiatives
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'strategic-pillars'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'strategic-pillars'">
                    Strategic Pillars
                </button>

            </div>

            <!-- Conditional View Rendering -->
            <div class="relative min-h-[400px]">
                <Transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform opacity-0 translate-y-4"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-4" mode="out-in">
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

                    <div v-else-if="viewMode === 'digital-block'" key="digital-block">
                        <DualGrowth :goals="dualGrowthGoals" />
                    </div>

                    <div v-else-if="viewMode === 'block'" key="block">
                        <DualGrowthFull :goals="dualGrowthGoals" />
                    </div>

                    <div v-else-if="viewMode === 'digital-transformation-initiatives'"
                        key="digital-transformation-initiatives">
                        <DigitalBuildingBlock :items="digitalInitiativeOptions" :coe-options="coeOptions" />
                    </div>

                    <div v-else-if="viewMode === 'it-building-blocs'" key="it-building-blocs">
                        <ItInitiatives :groups="itBuildingBlockMatrix" :coe-options="coeOptions"
                            :initiative-options="digitalInitiativeOptions" />
                    </div>

                    <div v-else-if="viewMode === 'it-initiatives'" key="it-initiatives">
                        <ItBuildingBlocks :items="itInitiativeOptions" :coe-options="coeOptions" />
                    </div>

                    <div v-else-if="viewMode === 'strategic-pillars'" key="strategic-pillars">
                        <StrategicPillarView
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
import { ref } from 'vue';
import DualGrowth from '@/Components/StrategicHouse/DualGrowth.vue';
import DualGrowthFull from '@/Components/StrategicHouse/DualGrowthFull.vue';
import DigitalBuildingBlock from '@/Components/ItBuildingBlocks/DigitalBuildingBlock.vue';
import ItBuildingBlocks from '@/Components/ItBuildingBlocks/ItBuildingBlock.vue';
import ItInitiatives from '@/Components/ItBuildingBlocks/ItBuildingBlocksMatrix.vue';
import StrategicPillarView from '@/Components/StrategicPillar/StrategicPillarView.vue';
import StretegicHouse from '@/Components/StrategicHouse/StretegicHouse.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

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
    // Strategic Pillar props
    strategicPillars: { type: Array, default: () => [] },
    allGoals:         { type: Array, default: () => [] },
    taggings:         { type: Array, default: () => [] },
    allInitiatives:   { type: Array, default: () => [] },
    allThemes:        { type: Array, default: () => [] },
    matrixInitiatives:{ type: Array, default: () => [] },
    allOrganizations: { type: Array, default: () => [] },
    pilarOptions:     { type: Array, default: () => [] },
    pillarFilters:    { type: Object, default: () => ({}) },
});

const getInitialViewMode = () => {
    if (typeof window === 'undefined') return 'mapping';
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pilar') || urlParams.has('goal_id') || urlParams.has('org_id')) {
        return 'strategic-pillars';
    }
    return 'mapping';
};

const viewMode = ref(getInitialViewMode());
</script>