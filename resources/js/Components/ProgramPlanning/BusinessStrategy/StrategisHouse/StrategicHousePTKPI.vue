<template>
    <div class="mx-auto flex w-full max-w-6xl flex-col items-center space-y-1 px-4 py-4">
        <section class="mt-2 w-full space-y-1">
            <!-- Vision Row -->
            <div class="grid grid-cols-[112px_1fr] min-h-[40px] border border-[#dbe4f0] bg-white">
                <div class="flex items-center justify-center border-r border-[#dbe4f0] bg-[#eef3f9] text-[#12233d] text-[0.68rem] font-extrabold tracking-widest uppercase">
                    VISION
                </div>
                <div class="flex items-center justify-center px-[0.8rem] py-[0.45rem] text-[#0f172a] text-[0.82rem] leading-[1.35] font-semibold text-center">
                    {{ strategicHouse.vision || '-' }}
                </div>
            </div>

            <!-- Mission Row -->
            <div class="grid grid-cols-[112px_1fr] min-h-[40px] border border-[#dbe4f0] bg-white">
                <div class="flex items-center justify-center border-r border-[#dbe4f0] bg-[#eef3f9] text-[#12233d] text-[0.68rem] font-extrabold tracking-widest uppercase">
                    MISSION
                </div>
                <div class="flex flex-col items-center justify-center px-[0.8rem] py-[0.45rem] text-[#0f172a] text-[0.82rem] leading-[1.35] font-semibold text-center">
                    <span v-for="(line, index) in missionLines" :key="`${line}-${index}`" class="block">
                        {{ line }}
                    </span>
                    <span v-if="missionLines.length === 0">-</span>
                </div>
            </div>

            <!-- Strategy Section -->
            <section class="grid grid-cols-[44px_1fr] gap-[0.35rem] items-stretch">
                <!-- Strategy Vertical Rail -->
                <div class="flex items-stretch justify-center border border-[#1d3664] bg-white text-[#111827] shadow-[0_8px_20px_rgba(16,30,58,0.18)]">
                    <span class="flex items-center justify-center [writing-mode:vertical-rl] rotate-180 text-[0.62rem] font-extrabold tracking-[0.16em] min-h-full">
                        STRATEGY
                    </span>
                </div>

                <div class="flex flex-col gap-[0.35rem]">
                    <!-- Tagline Strip -->
                    <section class="flex items-center justify-center gap-[0.75rem] px-[0.8rem] py-[0.55rem] border border-[#18386d] bg-gradient-to-r from-[#16396d] via-[#1f4b8c] to-[#16396d] text-white text-center shadow-[0_10px_24px_rgba(12,31,61,0.12)]">
                        <span class="font-bold leading-[1.35] text-[0.82rem]">
                            {{ strategicHouse.additional_info || '-' }}
                        </span>
                    </section>

                    <!-- Themes Grid -->
                    <section class="grid grid-cols-4 gap-[0.35rem]">
                        <article v-for="theme in strategyThemes" :key="theme.id" 
                            class="border border-[#d8e2ef] bg-gradient-to-b from-[#f8fbff] to-[#eef4fb] shadow-[0_6px_16px_rgba(15,23,42,0.06)]">
                            <div class="flex items-center justify-center px-[0.8rem] py-[0.65rem] text-[#16253d] text-[0.75rem] leading-[1.35] text-center">
                                {{ theme.name }}
                            </div>
                        </article>
                    </section>

                    <!-- Main Content Grid (Perspective + Pillars + Objectives) -->
                    <section class="grid gap-[0.35rem] items-stretch" :style="strategyLayoutStyle">
                        <!-- Perspective Vertical Rail -->
                        <div class="flex items-stretch justify-center border border-[#1d3664] bg-gradient-to-b from-[#1c3766] to-[#14294d] text-white shadow-[0_8px_20px_rgba(16,30,58,0.18)]" 
                             :style="perspectiveRailStyle">
                            <span class="flex items-center justify-center [writing-mode:vertical-rl] rotate-180 text-[0.62rem] font-extrabold tracking-[0.16em] min-h-full">
                                PERSPECTIVE
                            </span>
                        </div>

                        <!-- Perspective Rows -->
                        <template v-for="(row, index) in perspectiveRows" :key="row.id">
                            <div class="flex flex-row gap-[0.35rem]" 
                                 :style="{ gridColumn: '2 / span 2', gridRow: String(index + 1) }">
                                
                                <!-- Perspective Card -->
                                <article class="flex items-center justify-center bg-gradient-to-b from-[#eff4fb] to-[#e5edf8] flex-[0_0_210px] border border-[#d8e2ef]">
                                    <div class="px-[0.8rem] py-[0.65rem] text-[#16253d] text-[0.78rem] font-extrabold leading-[1.35] text-center">
                                        {{ row.name }}
                                    </div>
                                </article>

                                <!-- Pillar Card -->
                                <article class="flex items-start bg-[#eef4fb] flex-1 border border-[#d8e2ef]">
                                    <ul v-if="row.pillarStrategies.length" class=" px-[0.8rem] py-[0.65rem] list-none space-y-[0.35rem]">
                                        <li v-for="pillar in row.pillarStrategies" :key="pillar.id" class="flex items-start gap-[0.35rem] text-[#1a2d47] text-[0.68rem] leading-[1.4]">
                                            <span class="flex-none mt-[0.36rem] w-[0.3rem] h-[0.3rem] rounded-full bg-[#1c75bc]"></span>
                                            <span class="flex-none font-extrabold italic text-[#14325a]">{{ pillar.title }}</span>
                                            <span class="min-w-0 text-[#27364a]">{{ pillar.strategy }}</span>
                                        </li>
                                    </ul>

                                    <div v-else class="flex items-center justify-center w-full p-[0.8rem] text-[#64748b] text-[0.7rem] italic">
                                        -
                                    </div>
                                </article>
                            </div>
                        </template>

                        <!-- Strategic Objectives Card -->
                        <article class="flex flex-col h-full border border-[#d8e2ef] bg-gradient-to-b from-[#f6f7f9] to-[#eceff3] shadow-[0_8px_16px_rgba(15,23,42,0.05)]" 
                                 :style="objectivesStyle">
                            <div class="px-[0.9rem] pt-[0.75rem] pb-[0.65rem] border-b border-[#d8e2ef] text-[#111827] text-[10px] font-[900] text-center uppercase tracking-widest">
                                Strategic Objectives
                            </div>
                            <ul class="m-0 px-[0.95rem] py-[0.8rem] space-y-[0.3rem]">
                                <li v-for="theme in additionalThemes" :key="theme.id" class="flex items-start gap-[0.35rem] text-[#1f2937] text-[0.72rem] leading-[1.45] font-semibold">
                                    <span class="flex-none mt-[0.36rem] w-[0.3rem] h-[0.3rem] rounded-full bg-[#1c75bc]"></span>
                                    <span>{{ theme.name }}</span>
                                </li>
                            </ul>
                        </article>
                    </section>
                </div>
            </section>
        </section>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    strategicHouseKPI: {
        type: Object,
        default: () => ({})
    },
    strategyThemes: {
        type: Array,
        default: () => []
    },
    perspectiveThemes: {
        type: Array,
        default: () => []
    },
    pillarStrategies: {
        type: Array,
        default: () => []
    },
    additionalThemes: {
        type: Array,
        default: () => []
    },
});

const strategicHouse = computed(() => props.strategicHouseKPI || {});

const missionLines = computed(() => {
    const mission = String(strategicHouse.value.mission || '').trim();

    if (!mission) {
        return [];
    }

    return mission
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);
});

const pillarStrategiesByThemeId = computed(() => {
    return (props.pillarStrategies || []).reduce((acc, item) => {
        const key = String(item.themes_id ?? '');

        if (!key) {
            return acc;
        }

        if (!acc[key]) {
            acc[key] = [];
        }

        acc[key].push(item);
        return acc;
    }, {});
});

const perspectiveRows = computed(() => {
    return (props.perspectiveThemes || []).map((theme) => ({
        ...theme,
        pillarStrategies: pillarStrategiesByThemeId.value[String(theme.id)] || [],
    }));
});

const rowsCount = computed(() => Math.max(perspectiveRows.value.length, 1));

const strategyLayoutStyle = computed(() => ({
    gridTemplateColumns: '54px minmax(170px, 0.9fr) minmax(0, 1.45fr) minmax(175px, 0.7fr)',
    gridTemplateRows: `repeat(${rowsCount.value}, auto)`,
}));

const objectivesStyle = computed(() => ({
    gridColumn: '4',
    gridRow: `1 / span ${rowsCount.value}`,
}));

const perspectiveRailStyle = computed(() => ({
    gridColumn: '1',
    gridRow: `1 / span ${rowsCount.value}`,
}));
</script>

<style scoped>
/* All styles migrated to Tailwind CSS utility classes in template */
</style>