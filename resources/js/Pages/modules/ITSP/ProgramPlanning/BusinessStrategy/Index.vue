<template>
    <ModulLayout title="Business Strategy">
        <div class="w-full max-w-full px-0">

            <div class="flex gap-6">

                <aside class="w-56">
                    <div class="sticky top-40">
                        <div
                            class="inline-flex flex-col items-start gap-2 rounded-xl bg-slate-200/50 p-2 dark:bg-white/5 w-full">

                            
                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'KBUMN Mission'
                                
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'KBUMN Mission'">
                                KBUMN Mission
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Dual Growth'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Dual Growth'">
                                Dual Growth Strategy
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Prioritas Inisiatif Strategis Perusahaan'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Prioritas Inisiatif Strategis Perusahaan'">
                                Prioritas Inisiatif Strategis Perusahaan
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Priority Strategic Initiatives'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Priority Strategic Initiatives'">
                                    
                                Priority Strategic Initiatives of Pertamina 2026
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House Pertamina'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Strategic House Pertamina'">
                                Strategic House Pertamina
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House Upstream'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Strategic House Upstream'">
                                Strategic House Upstream
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House Gas'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Strategic House Gas'">
                                Strategic House Gas
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House PT KPI'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                @click="() => activeTab = 'Strategic House PT KPI'">
                                Strategic House PT KPI
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House C&T'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                @click="() => activeTab = 'Strategic House C&T'">
                                Strategic House C&T
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House IML'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                @click="() => activeTab = 'Strategic House IML'">
                                Strategic House IML
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Strategic House PNRE'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                @click="() => activeTab = 'Strategic House PNRE'">
                                Strategic House PNRE
                            </button>
                        </div>
                    </div>
                </aside>

                <main class="flex-1">
                    <div v-if="activeTab === 'KBUMN Mission' || activeTab === 'Strategic House Pertamina'"
                        class="p-4 flex justify-center">
                        <img src="/icon/MisiBUMN.png" alt="Misi BUMN Icon" class="h-80 w-auto object-contain" />
                    </div>
                    <div>
                        <StrategicHousePertamina v-if="activeTab === 'Strategic House Pertamina'"
                            :strategicHousePertamina="strategicHousePertamina"
                            :pertaminaGoals="pertaminaGoals" />

                        <StrategicHouseUpstream v-else-if="activeTab === 'Strategic House Upstream'"
                            :strategicHouseUpstream="strategicHouseUpstream"
                            :upstreamGoals="upstreamGoals" />

                        <StrategicHouseGas v-else-if="activeTab === 'Strategic House Gas'"
                            :strategicHouseGas="strategicHouseGas"
                            :gasGoals="gasGoals" />

                        <ListMisiBumn v-else-if="activeTab === 'KBUMN Mission'"
                            :missions="misiBumn" />

                        <StrategicHouseBusinessStrategy v-else-if="activeTab === 'Dual Growth'"
                            v-bind="dualGrowthProps" :readonly="true" />

                        <div v-else-if="activeTab === 'Prioritas Inisiatif Strategis Perusahaan'" class="space-y-4">
                            <!-- Sub-tab switcher -->
                            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg w-max">
                                <button type="button"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                                    :class="subTab === 'kbumn' 
                                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-slate-700 dark:text-white' 
                                        : 'text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
                                    @click="subTab = 'kbumn'">
                                    KBUMN Mission
                                </button>
                                
                                <button type="button"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                                    :class="subTab === 'dual_growth' 
                                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-slate-700 dark:text-white' 
                                        : 'text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
                                    @click="subTab = 'dual_growth'">
                                    Dual Growth Strategy
                                </button>
                            </div>

                            <BusinessStrategy v-if="subTab === 'dual_growth'"
                                :goals="goals" :strategies="strategies" :active-tab="activeTab" />

                            <KBUMN v-else-if="subTab === 'kbumn'"
                                :missions="misiBumn" />
                        </div>

                        <StrategicInitiative v-else-if="activeTab === 'Priority Strategic Initiatives'"
                            :initiatives="priorityStrategicInitiatives" />

                        <StrategicHousePTKPI v-else-if="activeTab === 'Strategic House PT KPI'"
                            :strategicHouseKPI="strategicHouseKPI"
                            :strategyThemes="strategyThemes"
                            :perspectiveThemes="perspectiveThemes"
                            :pillarStrategies="pillarStrategies"
                            :additionalThemes="additionalThemes" />

                        <StrategicHouseCT v-else-if="activeTab === 'Strategic House C&T'"
                            :strategicHouseCT="strategicHouseCT"
                            :ctGoals="ctGoals" />

                        <StrategicHouseIML v-else-if="activeTab === 'Strategic House IML'"
                            :strategicHouseIML="strategicHouseIML"
                            :imlGoals="imlGoals" />

                        <StrategicHousePNRE v-else-if="activeTab === 'Strategic House PNRE'"
                            :strategicHousePNRE="strategicHousePNRE"
                            :pnreGoals="pnreGoals" />
                    </div>
                </main>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import BusinessStrategy from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/BusinessStrategy.vue';
import KBUMN from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/KBUMN.vue';
import ListMisiBumn from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/ListMisiBumn.vue';
import StrategicHouseBusinessStrategy from '@/Components/modules/ITSP/StrategicHouse/BusinessStrategy/BusinessStrategy.vue';
import StrategicInitiative from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategicInitiative.vue';
import StrategicHousePertamina from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHousePertamina.vue';
import StrategicHouseUpstream from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHouseUpstream.vue';
import StrategicHouseGas from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHouseGas.vue';
import StrategicHousePTKPI from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHousePTKPI.vue';
import StrategicHouseCT from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHouseC&T.vue';
import StrategicHouseIML from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHouseIML.vue';
import StrategicHousePNRE from '@/Components/modules/ITSP/ProgramPlanning/BusinessStrategy/StrategisHouse/StrategicHousePNRE.vue';
import { ref } from 'vue';

const props = defineProps({
    goals: { type: Array, default: () => [] },
    strategies: { type: Object, default: () => ({}) },
    misiBumn: { type: Array, default: () => [] },
    dualGrowthProps: { type: Object, default: () => ({}) },
    priorityStrategicInitiatives: { type: Array, default: () => [] },
    strategicHousePertamina: { type: Object, default: () => ({}) },
    pertaminaGoals: { type: Array, default: () => [] },
    strategicHouseUpstream: { type: Object, default: () => ({}) },
    upstreamGoals: { type: Array, default: () => [] },
    strategicHouseGas: { type: Object, default: () => ({}) },
    gasGoals: { type: Array, default: () => [] },
    strategicHouseKPI: { type: Object, default: () => ({}) },
    strategyThemes: { type: Array, default: () => [] },
    perspectiveThemes: { type: Array, default: () => [] },
    pillarStrategies: { type: Array, default: () => [] },
    additionalThemes: { type: Array, default: () => [] },
    strategicHouseCT: { type: Object, default: () => ({}) },
    ctGoals: { type: Array, default: () => [] },
    strategicHouseIML: { type: Object, default: () => ({}) },
    imlGoals: { type: Array, default: () => [] },
    strategicHousePNRE: { type: Object, default: () => ({}) },
    pnreGoals: { type: Array, default: () => [] },
});

const defaultTab = 'KBUMN Mission';
const activeTab = ref(defaultTab);
const subTab = ref('kbumn');
</script>

<style scoped>
/* minimal styling */
</style>
