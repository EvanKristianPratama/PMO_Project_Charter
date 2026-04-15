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
                    :class="viewMode === 'dual-growth'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'dual-growth'">
                    Dual Growth Strategy
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
                    :class="viewMode === 'initiative-relation'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'initiative-relation'">
                    Initiative Relations
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'strategic-pillars'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'strategic-pillars'">
                    Strategic Pillars
                </button>
                <button type="button" class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                    :class="viewMode === 'roadmap'
                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    @click="viewMode = 'roadmap'">
                    Roadmap
                </button>
            </div>

            <!-- Dual Growth Enabler Toggle -->
            <div v-if="viewMode === 'dual-growth'" class="mb-5 flex justify-end">
                <button type="button" 
                    @click="showEnabler = !showEnabler"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all bg-slate-200/50 hover:bg-slate-300/50 dark:bg-white/5 dark:hover:bg-white/10 text-slate-700 dark:text-slate-200">
                    <component :is="showEnabler ? EyeIcon : EyeSlashIcon" class="w-3.5 h-3.5" />
                    {{ showEnabler ? 'Hide Enabler' : 'Show Enabler' }}
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

                    <div v-else-if="viewMode === 'dual-growth'" key="dual-growth">
                        <DualGrowthFull v-if="showEnabler" :goals="dualGrowthGoals" />
                        <DualGrowth v-else :goals="dualGrowthGoals" />
                    </div>

                    <div v-else-if="viewMode === 'digital-transformation-initiatives'"
                        key="digital-transformation-initiatives">
                        <DigitalBuildingBlock :items="digitalInitiativeOptions" :coe-options="coeOptions" />
                    </div>

                    <div v-else-if="viewMode === 'it-building-blocs'" key="it-building-blocs">
                        <div class="space-y-6 animate-fade-in-up">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-col gap-2">                    
                                    <!-- View Mode Switcher -->
                                    <div class="inline-flex items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5 w-fit">
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                            :class="viewModeBuildingBlock === 'mapping' 
                                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                            @click="viewModeBuildingBlock = 'mapping'"
                                        >
                                            IT Enabler
                                        </button>
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                            :class="viewModeBuildingBlock === 'digital-block' 
                                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                            @click="viewModeBuildingBlock = 'digital-block'"
                                        >
                                            Center of Excellence
                                        </button>
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                            :class="viewModeBuildingBlock === 'block' 
                                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                            @click="viewModeBuildingBlock = 'block'"
                                        >
                                            IT Building Blocks
                                        </button>
                                    </div>
                                </div>

                                <div v-if="viewModeBuildingBlock === 'mapping'" class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        v-if="!isEditModeBuildingBlock"
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                                        @click="openAddMappingBuildingBlock"
                                    >
                                        Tambah Primary
                                    </button>
                                    <button
                                        v-if="!isEditModeBuildingBlock"
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                                        @click="isEditModeBuildingBlock = true"
                                    >
                                        Edit Mapping
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                                        @click="isEditModeBuildingBlock = false"
                                    >
                                        Selesai Edit
                                    </button>
                                </div>
                            </div>

                            <div class="relative">
                                <Transition
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="transform opacity-0 translate-y-4"
                                    enter-to-class="transform opacity-100 translate-y-0"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="transform opacity-100 translate-y-0"
                                    leave-to-class="transform opacity-0 translate-y-4"
                                    mode="out-in"
                                >
                                    <ItInitiatives
                                        v-if="viewModeBuildingBlock === 'mapping'"
                                        ref="matrixRefBuildingBlock"
                                        :groups="itBuildingBlockMatrix"
                                        :editable="isEditModeBuildingBlock"
                                        :coe-options="coeOptions"
                                        :initiative-options="itInitiativeOptions"
                                        @cancel-add-mapping="isEditModeBuildingBlock = false"
                                    />
                                    <ItBuildingBlocks
                                        v-else-if="viewModeBuildingBlock === 'block'"
                                        :items="itInitiativeOptions"
                                        :coe-options="coeOptions"
                                    />
                                    <DigitalBuildingBlock
                                        v-else-if="viewModeBuildingBlock === 'digital-block'"
                                        :items="digitalInitiativeOptions"
                                        :coe-options="coeOptions"
                                    />
                                </Transition>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="viewMode === 'it-initiatives'" key="it-initiatives">
                        <ItBuildingBlocks :items="itInitiativeOptions" :coe-options="coeOptions" />
                    </div>

                    <div v-else-if="viewMode === 'initiative-relation'" key="initiative-relation">
                        <div class="space-y-6 animate-fade-in-up">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Initiatives Relations</h1>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Kelola dependensi antar initiative.</p>
                                </div>
                                <Link
                                    :href="initiativeRelationCreatePath"
                                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                                >
                                    Tambah Relation
                                </Link>
                            </div>

                            <InitiativeRelationDependency
                                :mst-initiatives="mstInitiatives"
                                :model-relation-options="modelRelationOptions"
                                @edit-relation="goToEdit"
                            />
                        </div>
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
import { nextTick, ref } from 'vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import DualGrowth from '@/Components/StrategicHouse/DualGrowth.vue';
import DualGrowthFull from '@/Components/StrategicHouse/DualGrowthFull.vue';
import DigitalBuildingBlock from '@/Components/ItBuildingBlocks/DigitalBuildingBlock.vue';
import ItBuildingBlocks from '@/Components/ItBuildingBlocks/ItBuildingBlock.vue';
import ItInitiatives from '@/Components/ItBuildingBlocks/ItBuildingBlocksMatrix.vue';
import StrategicPillarView from '@/Components/StrategicPillar/StrategicPillarView.vue';
import StretegicHouse from '@/Components/StrategicHouse/StretegicHouse.vue';
import InitiativeRelationDependency from '@/Components/InitiativeRelation/InitiativeRelationDependency.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link, router } from '@inertiajs/vue3';

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
    if (typeof window === 'undefined') return 'mapping';
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pilar') || urlParams.has('goal_id') || urlParams.has('org_id')) {
        return 'strategic-pillars';
    }
    if (urlParams.has('model_relasi')) {
        return 'initiative-relation';
    }
    return 'mapping';
};

const viewMode = ref(getInitialViewMode());
const showEnabler = ref(false);

// IT Building Blocks sub-navigation
const isEditModeBuildingBlock = ref(false);
const viewModeBuildingBlock = ref('mapping');
const matrixRefBuildingBlock = ref(null);

const openAddMappingBuildingBlock = async () => {
    if (!isEditModeBuildingBlock.value) {
        isEditModeBuildingBlock.value = true;
        await nextTick();
    }

    matrixRefBuildingBlock.value?.openAddMappingModal?.({
        exitEditOnCancel: true,
    });
};

const initiativeRelationIndexPath = '/program-planning/initiative-relation';
const initiativeRelationCreatePath = `${initiativeRelationIndexPath}/create`;
const initiativeRelationEditPath = (initiativeRelationId) => `${initiativeRelationIndexPath}/${initiativeRelationId}/edit`;

function goToEdit({ relation }) {
    const id = relation?.id;
    if (id != null) {
        router.visit(initiativeRelationEditPath(id));
    }
}
</script>