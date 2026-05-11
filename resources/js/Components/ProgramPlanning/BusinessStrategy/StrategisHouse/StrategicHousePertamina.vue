<template>
    <div class="flex flex-col items-center w-full max-w-6xl mx-auto space-y-1 p-4">
        <!-- Roof: Vision -->
        <div class="relative w-[90%] flex justify-center mt-4" v-if="strategicHousePertamina">
            <div class="w-full bg-[#1c2a5e] text-white pt-12 pb-4 px-12 text-center"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%); min-height: 140px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div class="font-bold text-xs italic mb-1">Vision:</div>
                <div class="font-semibold text-sm max-w-xl mx-auto leading-tight">{{ strategicHousePertamina.vision }}
                </div>
            </div>
        </div>

        <!-- Base of roof: Mission -->
        <div class="w-[90%] bg-[#121f40] text-white text-center py-4 px-8 shadow-md" v-if="strategicHousePertamina">
            <div class="font-bold text-xs italic mb-1">Mission:</div>
            <div class="font-semibold text-sm">{{ strategicHousePertamina.mission }}</div>
        </div>

        <!-- Goals (Fostering Energy Resilience) -->
        <template v-if="pertaminaGoals && pertaminaGoals.length > 0">
            <div v-for="goal in pertaminaGoals" :key="goal.id" class="w-full flex flex-col items-center">

                <div class="w-[90%] bg-[#1c75bc] text-white text-center py-2 px-6 shadow-sm mt-1">
                    <h2 class="text-lg font-bold italic">{{ goal.title }}</h2>
                </div>

                <!-- Themes / Pillars -->
                <div class="w-[90%] flex flex-col md:flex-row gap-2 mt-2 justify-between items-stretch">
                    <template v-for="theme in getPillarThemes(goal.themes)" :key="theme.id">
                        <div class="flex-1 bg-[#1866a5] text-white flex flex-col shadow-sm border border-blue-400/20">
                            <!-- Theme Header -->
                            <div class="border-b border-white/50 py-2 px-4 text-center">
                                <h3 class="font-bold text-base italic">{{ theme.name }}</h3>
                            </div>
                            <!-- Theme Content -->
                            <div class="p-4 flex-1 text-xs">
                                <ul class="space-y-3 list-none pl-1">
                                    <li v-for="pillar in theme.pillar_themes" :key="pillar.id"
                                        class="relative pl-4 leading-relaxed">
                                        <span class="absolute left-0 top-1.5 w-1 h-1 rounded-full bg-white"></span>
                                        <span class="italic text-yellow-300">{{ pillar.title }}</span>
                                        <span class="mx-1 text-blue-200">|</span>
                                        <span class="text-blue-50">{{ pillar.strategy }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Enabler Theme (Bottom Foundation) -->
                <div v-if="getEnablerTheme(goal.themes)"
                    class="w-[90%] bg-[#00a688] text-white text-center py-2 px-6 shadow-sm mt-2">
                    <h3 class="font-bold text-base italic">{{ getEnablerTheme(goal.themes).name }}</h3>
                    <p class="text-blue-50">HSSE Excellence, Sustainability, Technology Innovation,
                        Digital Transformation, Policy Advocacy, Human Capital & Stakeholder Management,
                        Integrated Risk Management</p>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    strategicHousePertamina: {
        type: Object,
        default: () => ({})
    },
    pertaminaGoals: {
        type: Array,
        default: () => []
    }
});

const getPillarThemes = (themes) => {
    if (!themes) return [];
    return themes.filter(t => t.theme_number !== 3);
};

const getEnablerTheme = (themes) => {
    if (!themes) return null;
    return themes.find(t => t.theme_number === 3);
};
</script>
