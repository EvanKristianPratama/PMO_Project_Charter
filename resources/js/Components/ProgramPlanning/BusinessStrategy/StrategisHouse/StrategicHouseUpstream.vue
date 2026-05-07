<template>
    <div class="flex flex-col items-center w-full max-w-6xl mx-auto space-y-1 p-4">
        <!-- Roof: Vision -->
        <div class="relative w-[90%] flex justify-center mt-4" v-if="strategicHouseUpstream">
            <div class="w-full bg-[#1c2a5e] text-white pt-12 pb-4 px-12 text-center"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%); min-height: 140px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div class="font-bold text-xs italic mb-1">Vision:</div>
                <div class="font-semibold text-sm max-w-xl mx-auto leading-tight">{{ strategicHouseUpstream.vision }}
                </div>
            </div>
        </div>

        <!-- Base of roof: Mission -->
        <div class="w-[90%] bg-[#121f40] text-white text-center py-4 px-8 shadow-md" v-if="strategicHouseUpstream">
            <div class="font-bold text-xs italic mb-1">Mission:</div>
            <div class="font-semibold text-sm">{{ strategicHouseUpstream.mission }}</div>
        </div>

        <!-- Goals & Pillar Building Structure (All goals side by side) -->
        <template v-if="upstreamGoals && upstreamGoals.length > 0">

            <!-- Row 1: Goal Bars (side by side) -->
            <div class="w-[90%] flex gap-2 mt-1">
                <div v-for="goal in getMainGoals(upstreamGoals)" :key="'goal-' + goal.id"
                    class="flex-1 bg-[#1c75bc] text-white text-center py-2 px-4 shadow-sm">
                    <h2 class="text-sm font-bold italic">{{ goal.title }}</h2>
                </div>
            </div>

            <!-- Row 2: Pillar Building Columns (side by side per goal) -->
            <div class="w-[90%] flex gap-2 mt-0">
                <div v-for="goal in getMainGoals(upstreamGoals)" :key="'pillar-' + goal.id"
                    class="flex-1 flex flex-col">

                    <!-- Theme Pillar Columns -->
                    <div class="w-full flex items-stretch" :style="{ minHeight: maxPillarHeight + 'px' }">
                        <template v-for="(theme, index) in getPillarThemes(goal.themes)" :key="theme.id">
                            <div class="flex-1 flex flex-col">
                                <!-- Pillar Top Beam (Entablature) -->
                                <div
                                    class="w-full h-3 bg-gradient-to-b from-[#0e4d7a] to-[#1866a5] rounded-t-sm shadow-md">
                                </div>

                                <!-- Pillar Capital -->
                                <div class="w-full">
                                    <div class="bg-[#0e4d7a] h-2 mx-1"></div>
                                    <div class="bg-[#1256a0] h-1 mx-2"></div>
                                </div>

                                <!-- Pillar Column Body -->
                                <div class="relative flex-1 mx-1 flex flex-col pillar-column">
                                    <!-- Pillar Shaft with Fluting Effect -->
                                    <div class="absolute inset-0 flex">
                                        <div class="flex-1 bg-[#1866a5]"></div>
                                    </div>

                                    <!-- Theme Header -->
                                    <div
                                        class="relative z-10 border-b border-white/30 py-3 px-2 text-center bg-[#1256a0]/80">
                                        <h3 class="font-bold text-xs italic text-white drop-shadow-sm">{{ theme.name }}
                                        </h3>
                                    </div>

                                    <!-- Pillar Strategies -->
                                    <div class="relative z-10 p-2 flex-1 text-[10px]">
                                        <ul class="space-y-2 list-none pl-0">
                                            <li v-for="pillar in theme.pillar_themes" :key="pillar.id"
                                                class="relative pl-3 leading-relaxed">
                                                <span
                                                    class="absolute left-0 top-1.5 w-1 h-1 rounded-full bg-yellow-300 shadow-sm shadow-yellow-300/50"></span>
                                                <span class="italic text-yellow-300 font-semibold">{{ pillar.title
                                                    }}</span>
                                                <span class="text-blue-50">{{ pillar.strategy }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Pillar Base -->
                                <div class="w-full">
                                    <div class="bg-[#1256a0] h-1 mx-2"></div>
                                    <div class="bg-[#0e4d7a] h-2 mx-1"></div>
                                </div>

                                <!-- Foundation / Base Platform -->
                                <div
                                    class="w-full h-3 bg-gradient-to-b from-[#0a3a5e] to-[#071e30] shadow-lg rounded-b-sm">
                                </div>
                            </div>

                            <!-- Gap between theme pillars within same goal -->
                            <div v-if="index < getPillarThemes(goal.themes).length - 1" class="w-1 flex-shrink-0"></div>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Global Enabler Goals (Goals with code 'D') -->
            <div v-for="enablerGoal in getEnablerGoals(upstreamGoals)" :key="'enabler-goal-' + enablerGoal.id"
                class="w-[90%] mt-1">
                <div class="bg-[#00a688] text-white text-center py-2 px-6 shadow-sm">
                    <h2 class="text-sm font-bold italic">{{ enablerGoal.title }}</h2>
                </div>

                <div v-if="enablerGoal.themes && enablerGoal.themes.length > 0"
                    class="w-full flex flex-wrap justify-start gap-x-[1.33%]">
                    <div v-for="theme in enablerGoal.themes" :key="theme.id"
                        class="w-[32.44%] bg-[#1c75bc] text-white text-center py-4 mt-2 rounded-sm flex items-center justify-center">
                        <h5 class="text-xs font-bold italic">{{ theme.name }}</h5>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    strategicHouseUpstream: {
        type: Object,
        default: () => ({})
    },
    upstreamGoals: {
        type: Array,
        default: () => []
    }
});

const maxPillarHeight = ref(320);

const getMainGoals = (goals) => {
    if (!goals) return [];
    return goals.filter(g => g.code !== 'D');
};

const getEnablerGoals = (goals) => {
    if (!goals) return [];
    return goals.filter(g => g.code === 'D');
};

const getPillarThemes = (themes) => {
    if (!themes) return [];
    return themes.filter(t => t.idGoal !== 20)
};

const getEnablerTheme = (themes) => {
    if (!themes) return null;
    return themes.find(t => t.idGoal === 20);
};

onMounted(async () => {
    await nextTick();
    // Calculate max pillar height based on content
    const pillars = document.querySelectorAll('.pillar-column');
    let maxH = 320;
    pillars.forEach(p => {
        if (p.scrollHeight > maxH) maxH = p.scrollHeight;
    });
    maxPillarHeight.value = maxH;
});
</script>

<style scoped>
.pillar-column {
    box-shadow: inset 2px 0 6px rgba(0, 0, 0, 0.15),
        inset -2px 0 6px rgba(0, 0, 0, 0.15),
        2px 0 8px rgba(0, 0, 0, 0.1),
        -2px 0 8px rgba(0, 0, 0, 0.1);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    border-right: 1px solid rgba(0, 0, 0, 0.2);
}
</style>
