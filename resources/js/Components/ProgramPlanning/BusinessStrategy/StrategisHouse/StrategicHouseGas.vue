<template>
    <div class="flex flex-col items-center w-full max-w-6xl mx-auto space-y-1 p-4">
        <!-- Roof: Vision -->
        <div class="relative w-[90%] flex justify-center mt-4" v-if="strategicHouseGas">
            <div class="w-full bg-[#0f3d3d] text-white pt-12 pb-4 px-12 text-center"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%); min-height: 140px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div class="font-bold text-xs italic mb-1">Vision:</div>
                <div class="font-semibold text-sm max-w-xl mx-auto leading-tight">{{ strategicHouseGas.vision }}
                </div>
            </div>
        </div>

        <!-- Base of roof: Mission -->
        <div class="w-[90%] bg-[#0a4e4e] text-white text-center py-2 px-8 shadow-md" v-if="strategicHouseGas">
            <div class="font-bold text-xs italic mb-1">Mission:</div>
            <div class="font-semibold text-sm">
                <div v-for="(line, index) in strategicHouseGas.mission.split('\\n')" :key="index">
                    {{ line }}
                </div>
            </div>
        </div>

        <!-- Base of roof: Ambition -->
        <div class="w-[90%] bg-[#0a4e4e] text-white text-center py-2 px-8 shadow-md flex items-center" v-if="strategicHouseGas">
            <div class="font-semibold text-xs max-w-65 text-left">
                <div>Connecting Indonesia to a Cleaner and Sustainable Energy Future</div>
            </div>
            <div class="font-semibold text-xs px-4 border-l-2 border-white/30 text-center">
                <div class="opacity-80">2029</div>
                <div class="font-bold">2,0x Revenue, $0.7 Bn Net Income</div>
            </div>
            <div class="font-semibold text-xs px-4 border-l-2 border-white/30 text-center">
                <div class="opacity-80">2034</div>
                <div class="font-bold">2,4x Revenue, $0.9 Bn Net Income</div>
            </div>
        </div>

        <!-- Goals & Pillar Building Structure (All goals side by side) -->
        <template v-if="gasGoals && gasGoals.length > 0">
            <!-- Row 2: Pillar Building Columns (side by side per goal) -->
            <div class="w-[90%] flex gap-2">
                <div v-for="goal in getMainGoals(gasGoals)" :key="'pillar-' + goal.id" class="flex-1 flex flex-col">

                    <!-- Theme Pillar Column -->
                    <div class="w-full flex items-stretch">
                        <template v-for="(theme, index) in getPillarThemes(goal.themes)" :key="theme.id">
                            <div class="flex-1 flex flex-col">
                                <!-- Pillar Column Body -->
                                <div class="relative flex-1 flex flex-col pillar-column-gas">
                                    <!-- Pillar Shaft with Fluting Effect -->
                                    <div class="absolute inset-0 flex">
                                        <div class="flex-1 bg-green-50"></div>
                                    </div>

                                    <!-- Theme Header -->
                                    <div
                                        class="relative z-10 border-b border-white/30 px-2 text-center bg-[#0a6e6e]/80 min-h-[48px] flex items-center justify-center">
                                        <h3 class="font-semibold text-xs text-white leading-tight">{{ theme.name
                                            }}</h3>
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
                                                <span class="text-black font-bold">{{ pillar.strategy }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>

                            <!-- Gap between theme pillars within same goal -->
                            <div v-if="index < getPillarThemes(goal.themes).length - 1" class="w-1 flex-shrink-0"></div>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Global Enabler Goals (Goals with code 'D') -->
            <div v-for="enablerGoal in getEnablerGoals(gasGoals)" :key="'enabler-goal-' + enablerGoal.id"
                class="space-y-1 w-full flex flex-col items-center mt-1">

                <div v-if="enablerGoal.themes && enablerGoal.themes.length > 0"
                    class="space-y-1 w-full flex flex-col items-center">
                    <div v-for="theme in enablerGoal.themes" :key="theme.id"
                        :style="{ width: (90 + getThemeGlobalIndex(theme.id) * 3) + '%' }"
                        class="bg-[#0e7272] text-white py-1.5 px-3 flex flex-row items-center justify-center text-center text-[10px] leading-relaxed shadow-sm gap-x-2">
                        <span class="font-bold whitespace-nowrap">{{ theme.name }}:</span>
                        <div class="flex flex-wrap justify-center gap-x-1.5">
                            <template v-for="(pillar, pIndex) in theme.pillar_themes" :key="pillar.id">
                                <div class="flex items-center">
                                    <span class="italic text-white">{{ pillar.strategy }}</span>
                                    <span v-if="pIndex < theme.pillar_themes.length - 1"
                                        class="ml-1.5 text-white/40 font-bold">|</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    strategicHouseGas: {
        type: Object,
        default: () => ({})
    },
    gasGoals: {
        type: Array,
        default: () => []
    }
});

const getMainGoals = (goals) => {
    if (!goals) return [];
    return goals.filter(g => g.code !== 'B');
};

const getEnablerGoals = (goals) => {
    if (!goals) return [];
    return goals.filter(g => g.code === 'B');
};

const getPillarThemes = (themes) => {
    if (!themes) return [];
    return themes.filter(t => t.theme_number !== 22);
};

const getEnablerTheme = (themes) => {
    if (!themes) return null;
    return themes.find(t => t.theme_number === 22);
};

const getThemeGlobalIndex = (themeId) => {
    const allThemes = getEnablerGoals(props.gasGoals).flatMap(g => g.themes || []);
    return allThemes.findIndex(t => t.id === themeId);
};
</script>

<style scoped>
.pillar-column-gas {
    box-shadow: inset 2px 0 6px rgba(0, 0, 0, 0.15),
        inset -2px 0 6px rgba(0, 0, 0, 0.15),
        2px 0 8px rgba(0, 0, 0, 0.1),
        -2px 0 8px rgba(0, 0, 0, 0.1);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    border-right: 1px solid rgba(0, 0, 0, 0.2);
}
</style>
