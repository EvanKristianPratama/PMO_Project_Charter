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
                    <section v-if="viewMode === 'mapping'" key="mapping" class="sh-mockup">
                        <div class="mockup-content">

                            <!-- ═══ ROOF: Focus Bands (Maximize Legacy Business + Build Low Carbon) ═══ -->
                            <div class="roof-section">
                                <div class="roof-headline">{{ page.headline }}</div>

                                <div class="roof-top">
                                    <div class="roof-main">
                                        <div class="roof-main-label">{{ roofSection.main_goal?.title ?? page.headline }}
                                        </div>
                                        <div v-if="roofSection.main_goal_themes?.length" class="roof-sub-items">
                                            <div v-for="theme in roofSection.main_goal_themes" :key="theme.id"
                                                class="roof-sub-item">
                                                {{ theme.label }}
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="roofSection.side_goal" class="roof-side">
                                        <div class="roof-side-label">{{ roofSection.side_goal.title }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- ═══ CONNECTOR: small decorative chain ═══ -->
                            <div class="connector-chain">
                                <svg width="20" height="28" viewBox="0 0 20 28">
                                    <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                    <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5" />
                                    <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                </svg>
                            </div>

                            <!-- ═══ VISION TRAPEZOID ═══ -->
                            <div class="vision-trapezoid">
                                <p class="vision-title">{{ page.visionTitle }}:</p>
                                <p class="vision-text">{{ page.visionText }}</p>
                            </div>

                            <!-- ═══ CONNECTOR ═══ -->
                            <div class="connector-chain">
                                <svg width="20" height="28" viewBox="0 0 20 28">
                                    <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                    <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5" />
                                    <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                </svg>
                            </div>

                            <!-- ═══ DIGITAL TRANSFORMATION INITIATIVES SECTION ═══ -->
                            <div class="dti-section">
                                <div class="dti-header">
                                    <div class="dti-header-copy">
                                        <p class="dti-count">{{ summary.total_initiatives }} Digital transformation
                                            initiatives</p>
                                    </div>
                                    <button type="button" class="dti-toggle" :aria-pressed="showCoeInitiatives"
                                        :aria-label="showCoeInitiatives ? 'Hide initiative names' : 'Show initiative names'"
                                        :title="showCoeInitiatives ? 'Hide initiative names' : 'Show initiative names'"
                                        @click="showCoeInitiatives = !showCoeInitiatives">
                                        <EyeSlashIcon v-if="showCoeInitiatives" class="dti-toggle-icon" />
                                        <EyeIcon v-else class="dti-toggle-icon" />
                                    </button>
                                </div>

                                <div class="dti-cards">
                                    <div v-for="card in technologyCards" :key="card.name" class="dti-card"
                                        :class="{ 'dti-card--compact': !showCoeInitiatives }" :title="coeTooltip(card)">
                                        <div class="dti-card-badge">
                                            {{ card.initiatives_count }}
                                        </div>
                                        <div class="dti-card-title">{{ card.display_name }}</div>
                                        <div v-if="showCoeInitiatives" class="dti-card-preview">
                                            <template v-if="card.initiatives_preview?.length">
                                                <p v-for="initiative in card.initiatives_preview" :key="initiative.id"
                                                    class="dti-card-preview-item">
                                                    {{ initiative.code }} - {{ initiative.name }}
                                                </p>
                                                <p v-if="card.remaining_initiatives_count > 0"
                                                    class="dti-card-preview-more">
                                                    +{{ card.remaining_initiatives_count }} initiative lagi
                                                </p>
                                            </template>
                                            <p v-else class="dti-card-preview-empty">
                                                Belum ada initiative
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ═══ CONNECTOR ═══ -->
                            <div class="connector-chain">
                                <svg width="20" height="28" viewBox="0 0 20 28">
                                    <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                    <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5" />
                                    <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5" />
                                </svg>
                            </div>

                            <!-- ═══ GRAND IT STRATEGY SECTION ═══ -->
                            <div class="gits-section">
                                <div class="gits-header">
                                    <h2 class="gits-title">{{ page.grandStrategyTitle }}</h2>
                                    <p class="gits-subtitle">{{ page.grandStrategyText }}</p>
                                </div>

                                <div class="gits-pillars">
                                    <article v-for="card in strategyCards" :key="card.name" class="gits-pillar">
                                        <h3 class="gits-pillar-title">{{ card.display_name }}</h3>
                                        <div class="gits-pillar-desc">
                                            <p v-for="(line, lineIndex) in (card.description_lines?.length ? card.description_lines : card.initiatives_preview.map(item => item.label))"
                                                :key="`${card.name}-${lineIndex}`">
                                                {{ line }}
                                            </p>
                                            <p v-if="!card.description_lines?.length && card.is_empty"
                                                class="gits-pillar-empty">
                                                Belum ada initiative yang terhubung ke area ini.
                                            </p>
                                        </div>
                                    </article>
                                </div>

                                <!-- ═══ FOUNDATION BAR ═══ -->
                                <div v-if="foundationCard" class="foundation-bar">
                                    <span class="foundation-title">{{ foundationCard.display_name }}:</span>
                                    <span class="foundation-desc">Memungkinkan pelaksanaan efektif dari semua digital
                                        dan IT initiative</span>
                                </div>
                            </div>
                        </div>
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
import { computed, ref } from 'vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import DualGrowth from '@/Components/StrategicHouse/DualGrowth.vue';
import DualGrowthFull from '@/Components/StrategicHouse/DualGrowthFull.vue';
import DigitalBuildingBlock from '@/Components/ItBuildingBlocks/DigitalBuildingBlock.vue';
import ItBuildingBlocks from '@/Components/ItBuildingBlocks/ItBuildingBlock.vue';
import ItInitiatives from '@/Components/ItBuildingBlocks/ItBuildingBlocksMatrix.vue';
import StrategicPillarView from '@/Components/StrategicPillar/StrategicPillarView.vue';
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
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pilar') || urlParams.has('goal_id') || urlParams.has('org_id')) {
        return 'strategic-pillars';
    }
    return 'mapping';
};

const viewMode = ref(getInitialViewMode());
const showCoeInitiatives = ref(true);

const allInitiatives = computed(() => {
    const list = [];

    // Dari technologyCards
    (props.technologyCards || []).forEach(card => {
        (card.initiatives || []).forEach(ini => list.push({
            ...ini,
            coe_name: card.name
        }));
    });

    // Dari strategyCards
    (props.strategyCards || []).forEach(card => {
        (card.initiatives || []).forEach(ini => list.push({
            ...ini,
            coe_name: card.name
        }));
    });

    // Dari foundationCard
    if (props.foundationCard?.initiatives) {
        props.foundationCard.initiatives.forEach(ini => list.push({
            ...ini,
            coe_name: props.foundationCard.name
        }));
    }

    // Dari architectureCard
    if (props.architectureCard?.initiatives) {
        props.architectureCard.initiatives.forEach(ini => list.push({
            ...ini,
            coe_name: props.architectureCard.name
        }));
    }

    // Dari tbcCard
    if (props.tbcCard?.initiatives) {
        props.tbcCard.initiatives.forEach(ini => list.push({
            ...ini,
            coe_name: props.tbcCard.name
        }));
    }

    // Unassigned
    (props.unassignedInitiatives || []).forEach(ini => list.push(ini));

    return list;
});

const coeTooltip = (card) => {
    if (!card?.initiatives?.length) {
        return `${card?.display_name ?? 'CoE'}: belum ada initiative`;
    }

    const lines = card.initiatives.map((initiative) => initiative.label);

    return `${card.display_name} (${card.initiatives_count})\n${lines.join('\n')}`;
};
</script>

<style scoped>
/* ─── MOCKUP WRAPPER ─── */
.sh-mockup {
    border: 1px solid #d9e2ec;
    border-radius: 28px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.mockup-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.mockup-eyebrow {
    margin: 0 0 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #2563eb;
}

.mockup-content {
    padding: 24px;
}

/* ─── ROOF SECTION ─── */
.roof-section {
    margin-bottom: 0;
}

.roof-headline {
    margin-bottom: 8px;
    text-align: center;
    font-size: 15px;
    font-weight: 800;
    color: #1a2a3a;
}

.roof-top {
    display: flex;
    gap: 12px;
    align-items: stretch;
    width: 80%;
    margin: 0 auto;
}

.roof-main {
    flex: 1;
    text-align: center;
}

.roof-side {
    width: 260px;
    display: flex;
}

.roof-main-label {
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 2px 24px;
    font-size: 14px;
    font-weight: 600;
    color: #1a2a3a;
    border-radius: 4px;
}

.roof-sub-items {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.roof-sub-item {
    flex: 1;
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 2px 16px;
    font-size: 12px;
    font-weight: 500;
    color: #2a4a6a;
    text-align: center;
    border-radius: 4px;
}

.roof-side-label {
    width: 100%;
    background: linear-gradient(180deg, #3b64a8 0%, #2f5596 100%);
    color: #fff;
    border-radius: 10px;
    padding: 2px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
}

/* ─── CONNECTORS ─── */
.connector-chain {
    display: flex;
    justify-content: center;
    padding: 4px 0;
}

/* ─── VISION TRAPEZOID ─── */
.vision-trapezoid {
    background: linear-gradient(180deg, #1e6dc0 0%, #184f96 100%);
    color: #fff;
    text-align: center;
    padding: 17px 40px;
    clip-path: polygon(10% 0%, 90% 0%, 100% 100%, 0% 100%);
    border-radius: 6px;
}

.vision-title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.02em;
}

.vision-text {
    font-size: 12px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.92);
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

/* ─── DIGITAL TRANSFORMATION INITIATIVES ─── */
.dti-section {
    background: #b8d4f0;
    border-radius: 6px;
    padding: 20px 20px 24px;
}

.dti-header {
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.dti-header-copy {
    flex: 1;
    text-align: center;
}

.dti-count {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-label {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-header p {
    display: inline;
}

.dti-toggle {
    border: 1px solid rgba(13, 42, 74, 0.16);
    background: rgba(255, 255, 255, 0.9);
    color: #184f96;
    border-radius: 999px;
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.dti-toggle:hover {
    background: #fff;
    border-color: rgba(24, 79, 150, 0.32);
}

.dti-toggle-icon {
    width: 18px;
    height: 18px;
    stroke-width: 1.8;
}

.dti-cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.dti-card {
    background: #fff;
    border: 1.5px solid #3b82c8;
    border-radius: 4px;
    padding: 12px 12px 10px;
    text-align: left;
    color: #184f96;
    position: relative;
    min-height: 118px;
}

.dti-card--compact {
    min-height: 52px;
    display: flex;
    align-items: center;
}

.dti-card--compact .dti-card-title {
    margin-top: 0;
}

.dti-card-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    min-width: 26px;
    height: 26px;
    border-radius: 999px;
    background: #2f5596;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 8px;
    box-shadow: 0 4px 12px rgba(47, 85, 150, 0.16);
}

.dti-card-title {
    padding-right: 42px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
    margin-top: 12px;
}

.dti-card-preview {
    margin-top: 10px;
    border-top: 1px dashed rgba(59, 130, 200, 0.35);
    padding-top: 8px;
}

.dti-card-preview-item,
.dti-card-preview-more,
.dti-card-preview-empty {
    font-size: 10px;
    line-height: 1.35;
}

.dti-card-preview-item {
    color: #365780;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dti-card-preview-more {
    color: #184f96;
    font-weight: 600;
}

.dti-card-preview-empty {
    color: #6b85a8;
    font-style: italic;
}

/* ─── GRAND IT STRATEGY SECTION ─── */
.gits-section {
    background: #dde8f4;
    border-radius: 6px;
    padding: 24px 20px;
}

.gits-header {
    text-align: center;
    margin-bottom: 20px;
}

.gits-title {
    font-size: 17px;
    font-weight: 700;
    color: #0d2a4a;
}

.gits-subtitle {
    font-size: 15px;
    color: #3a5a7a;
}

.gits-pillars {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    margin-bottom: 16px;
}

.gits-pillar {
    background: linear-gradient(180deg, #2567a8 0%, #184f96 100%);
    border-radius: 10px;
    padding: 16px 14px;
    color: #fff;
    display: flex;
    flex-direction: column;
}

.gits-pillar-title {
    font-size: 14px;
    text-align: center;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 8px;
}

.gits-pillar-desc {
    font-size: 10px;
    text-align: center;
    line-height: 1;
    color: rgba(255, 255, 255, 0.85);
    flex: 1;
}

.gits-pillar-desc p {
    margin-bottom: 4px;
}

.gits-pillar-empty {
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

/* ─── FOUNDATION BAR ─── */
.foundation-bar {
    background: linear-gradient(90deg, #1b4f93 0%, #215da8 50%, #1b4f93 100%);
    border-radius: 8px;
    padding: 14px 24px;
    color: #fff;
    text-align: center;
    line-height: 1.5;
}

.foundation-title {
    font-weight: 700;
    font-size: 13px;
    margin-right: 4px;
}

.foundation-desc {
    color: rgba(255, 255, 255, 0.85);
    font-size: 10px;
}

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
    .gits-pillars {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .roof-top {
        flex-direction: column;
        width: 90%;
    }

    .roof-side {
        width: 100%;
    }

    .dti-header {
        flex-direction: column;
        align-items: stretch;
    }

    .dti-header-copy {
        text-align: center;
    }

    .dti-toggle {
        align-self: center;
    }

    .dti-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .gits-pillars {
        grid-template-columns: repeat(2, 1fr);
    }

    .mockup-header {
        padding: 18px 18px 14px;
    }

    .mockup-content {
        padding: 16px;
    }

    .vision-trapezoid {
        clip-path: polygon(5% 0%, 95% 0%, 100% 100%, 0% 100%);
        padding: 20px 24px;
    }
}

@media (max-width: 480px) {
    .dti-cards {
        grid-template-columns: 1fr;
    }

    .gits-pillars {
        grid-template-columns: 1fr;
    }

    .roof-sub-items {
        flex-direction: column;
    }
}

/* ─── DARK MODE ─── */
:deep(.dark) .sh-mockup {
    border-color: rgba(148, 163, 184, 0.16);
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
}

:deep(.dark) .mockup-header {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    background: #111827;
}

:deep(.dark) .roof-main-label,
:deep(.dark) .roof-sub-item {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

:deep(.dark) .roof-headline {
    color: #f8fafc;
}

:deep(.dark) .roof-side-label {
    background: linear-gradient(180deg, #274a87 0%, #1f3e74 100%);
}

:deep(.dark) .dti-section {
    background: #12253f;
}

:deep(.dark) .dti-count,
:deep(.dark) .dti-label {
    color: #e2e8f0;
}

:deep(.dark) .dti-toggle {
    background: rgba(15, 23, 42, 0.8);
    border-color: rgba(148, 163, 184, 0.25);
    color: #bfdbfe;
}

:deep(.dark) .dti-toggle:hover {
    background: rgba(15, 23, 42, 0.95);
    border-color: rgba(96, 165, 250, 0.35);
}

:deep(.dark) .dti-card {
    background: #1e293b;
    border-color: #3b82c8;
    color: #93c5fd;
}

:deep(.dark) .dti-card-badge {
    background: #60a5fa;
    color: #0f172a;
}

:deep(.dark) .dti-card-preview {
    border-top-color: rgba(96, 165, 250, 0.25);
}

:deep(.dark) .dti-card-preview-item {
    color: #cbd5e1;
}

:deep(.dark) .dti-card-preview-more {
    color: #93c5fd;
}

:deep(.dark) .dti-card-preview-empty {
    color: #94a3b8;
}

:deep(.dark) .gits-section {
    background: #0f1d2f;
}

:deep(.dark) .gits-title {
    color: #e2e8f0;
}

:deep(.dark) .gits-subtitle {
    color: #94a3b8;
}
</style>
