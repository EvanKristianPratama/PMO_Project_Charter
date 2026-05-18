<template>
    <div class="mx-auto flex w-full max-w-6xl flex-col items-center space-y-1 p-4">
        <div class="relative mt-4 flex w-[90%] justify-center" v-if="strategicHouseCT">
            <div
                class="flex min-h-[128px] w-full flex-col justify-end bg-[#0f5f88] px-10 pt-10 pb-4 text-center text-white"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%);">
                <div class="mb-1 text-xs font-bold italic tracking-wide">Vision:</div>
                <div class="mx-auto max-w-xl text-sm font-semibold leading-tight">
                    {{ strategicHouseCT.vision || '-' }}
                </div>
            </div>
        </div>

        <div v-if="filteredGoals.length" class="w-[90%]">
            <div class="grid gap-1" :style="goalsHeaderGridStyle">
                <div v-for="goal in filteredGoals" :key="`goal-${goal.id}`" 
                    class="flex items-center justify-center min-h-[40px] p-[0.7rem_0.75rem] bg-gradient-to-b from-[#6c9b9f] to-[#7fb2b6] text-white border border-[#d3dee8] shadow-[0_8px_18px_rgba(15,23,42,0.06)] w-full">
                    <div class="text-center text-[0.8rem] font-extrabold leading-[1.35]">{{ goal.title }}</div>
                </div>
            </div>

            <div v-for="themeRow in themeRows" :key="themeRow.key" class="grid gap-1" :style="goalsHeaderGridStyle">
                <div
                    v-for="(cell, index) in themeRow.cells"
                    :key="`${themeRow.key}-${index}`"
                    class="flex flex-col overflow-hidden bg-[#f5f8fb] text-[#14253b]">
                        <template v-if="cell">
                            <div class="flex items-center gap-[0.35rem] p-[0.55rem_0.7rem] text-[0.72rem] font-extrabold leading-[1.3]">
                                <span v-if="cell.theme_number" class="flex-none rounded-full bg-[#c81e1e] px-2 py-[0.1rem] text-[0.6rem] text-white">
                                    {{ cell.theme_number }}
                                </span>
                                <span class="text-center">{{ cell.name }}</span>
                            </div>

                            <ul v-if="cell.pillar_themes && cell.pillar_themes.length" class="m-0 list-none p-[0.5rem_0.65rem_0.65rem]">
                                <li v-for="(pillar, pIndex) in cell.pillar_themes" :key="pillar.id" 
                                    class="flex items-start gap-[0.35rem] text-[0.64rem] leading-[1.35] text-[#1e2d43] text-left"
                                    :class="{ 'mt-[0.28rem]': pIndex > 0 }">
                                    <span class="min-w-0">{{ pillar.title }}</span>
                                </li>
                            </ul>
                        </template>
                </div>
            </div>
        </div>

        <div v-if="enablerGoal" class="w-[90%]">
            <div class="bg-[#eef3f8] border border-[#d3dee8]">
                <div class="bg-gradient-to-b from-[#0c5f88] to-[#0a4e72] text-white border border-[#d3dee8] shadow-[0_8px_18px_rgba(15,23,42,0.06)]">
                    <ul v-if="enablerGoal.themes && enablerGoal.themes.length" class="m-0 list-none p-[0.55rem_0.7rem_0.7rem]">
                        <li v-for="(theme, tIndex) in enablerGoal.themes" :key="theme.id" 
                            class="flex gap-[0.35rem] items-start text-[0.64rem] leading-[1.35] text-[#eef6ff]"
                            :class="{ 'mt-[0.28rem]': tIndex > 0 }">
                            <span class="mt-[0.34rem] flex-none w-[0.3rem] h-[0.3rem] rounded-full bg-white"></span>
                            <span class="min-w-0">
                                <template v-if="theme.pillar_themes && theme.pillar_themes.length">
                                    <span v-for="(pillar, index) in theme.pillar_themes" :key="pillar.id">
                                        {{ theme.name }} - {{ pillar.strategy }}
                                        <span v-if="index < theme.pillar_themes.length - 1">, </span>
                                    </span>
                                </template>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    strategicHouseCT: {
        type: Object,
        default: () => ({})
    },
    ctGoals: {
        type: Array,
        default: () => []
    }
});

const filteredGoals = computed(() => {
    if (!props.ctGoals) return [];
    return props.ctGoals.filter(goal => Number(goal.id) !== 31);
});

const enablerGoal = computed(() => {
    if (!props.ctGoals) return null;
    return props.ctGoals.find(goal => Number(goal.id) === 31);
});

const goalThemes = (goal) => goal?.themes || [];

const maxThemesCount = computed(() => {
    return Math.max(...filteredGoals.value.map((goal) => goalThemes(goal).length), 0);
});

const themeRows = computed(() => {
    return Array.from({ length: maxThemesCount.value }, (_, rowIndex) => ({
        key: `theme-row-${rowIndex}`,
        cells: filteredGoals.value.map((goal) => goalThemes(goal)[rowIndex] || null),
    }));
});

const goalsHeaderGridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${Math.max(filteredGoals.value.length, 1)}, minmax(0, 1fr))`,
}));

</script>