<template>
    <div class="flex flex-col items-center w-full max-w-6xl mx-auto space-y-1 p-4">
        <!-- Roof: Vision -->
        <div class="relative w-[90%] flex justify-center mt-4" v-if="strategicHouseIML">
            <div class="w-full bg-[#1c2a5e] text-white pt-12 pb-4 px-12 text-center"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%); min-height: 140px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div class="font-bold text-xs italic mb-1">Vision:</div>
                <div class="font-semibold text-sm max-w-xl mx-auto leading-tight">{{ strategicHouseIML.vision || '-' }}</div>
            </div>
        </div>

        <!-- Base of roof: Mission -->
        <div class="w-[90%] bg-[#121f40] text-white text-center py-4 px-8 shadow-md" v-if="strategicHouseIML">
            <div class="font-bold text-xs italic mb-1">Mission:</div>
            <div class="font-semibold text-sm">{{ strategicHouseIML.mission || '-' }}</div>
        </div>

        <!-- Goals (pillar = 9, excluding enabler goal id=36) -->
        <template v-if="filteredGoals && filteredGoals.length > 0">
            <div v-for="goal in filteredGoals" :key="goal.id" class="w-full flex flex-col items-center">

                <div class="w-[90%] bg-[#1c75bc] text-white text-center py-2 px-6 shadow-sm mt-1">
                    <h2 class="font-bold text-base italic">{{ goal.title }}</h2>
                </div>

                <!-- Themes per Goal -->
                <div class="w-[90%] flex flex-col md:flex-row gap-2 mt-2 justify-between items-stretch">
                    <template v-for="theme in goal.themes" :key="theme.id">
                        <div class="flex-1 bg-[#1866a5] text-white flex flex-col shadow-sm border border-blue-400/20">
                            <!-- Theme Header -->
                            <div class="border-b border-white/50 py-2 px-4 text-center">
                                <h3 class="font-bold text-base italic">{{ theme.name }}</h3>
                            </div>
                            <!-- Theme Content (pillar_themes from MstBusinessStrategy) -->
                            <div class="p-4 flex-1 text-xs">
                                <ul v-if="theme.pillar_themes && theme.pillar_themes.length" class="space-y-3 list-none pl-1">
                                    <li v-for="pillar in theme.pillar_themes" :key="pillar.id"
                                        class="relative pl-4 leading-relaxed">
                                        <span class="absolute left-0 top-1.5 w-1 h-1 rounded-full bg-white"></span>
                                        <span class="italic text-yellow-300">{{ pillar.title }}</span>
                                        <span class="mx-1 text-blue-200">|</span>
                                        <span class="text-blue-50">{{ pillar.strategy }}</span>
                                    </li>
                                </ul>
                                <div v-else class="text-blue-100">No strategies available</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Enabler Goal (id = 36) - Bottom Foundation with per-row display -->
        <div v-if="enablerGoal" class="w-[90%]">
            <div class="bg-[#eef3f8] border border-[#d3dee8]">
                <div class="bg-gradient-to-b from-[#0c5f88] to-[#0a4e72] text-white border border-[#d3dee8] shadow-[0_8px_18px_rgba(15,23,42,0.06)]">
                    <div class="p-[0.30rem_0.7rem] text-center">
                        <div class="font-bold text-base italic mb-2">{{ enablerGoal.title }}</div>
                        <ul v-if="enablerThemes && enablerThemes.length" class="list-none p-0 m-0">
                            <li v-for="theme in enablerThemes" :key="theme.id" class="mb-0.5 last:mb-0">
                                <div v-if="theme.pillar_themes && theme.pillar_themes.length">
                                    <div v-for="pillar in theme.pillar_themes" :key="pillar.id" class="text-[0.64rem] font-bold py-1">
                                        {{ theme.name }} - {{ pillar.strategy }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    strategicHouseIML: {
        type: Object,
        default: () => ({})
    },
    imlGoals: {
        type: Array,
        default: () => []
    }
});

// Filter goals: pillar = 9 and exclude enabler goal with id = 36
const filteredGoals = computed(() => {
    if (!props.imlGoals) return [];
    return props.imlGoals.filter(goal => Number(goal.id) !== 36);
});

// Get enabler goal with id = 36
const enablerGoal = computed(() => {
    if (!props.imlGoals) return null;
    return props.imlGoals.find(goal => Number(goal.id) === 36);
});

// Get all themes from enabler goal
const enablerThemes = computed(() => {
    if (!enablerGoal.value || !enablerGoal.value.themes) return [];
    return enablerGoal.value.themes;
});
</script>