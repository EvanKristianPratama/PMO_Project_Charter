<template>
    <UserLayout title="Business Strategy">
        <div class="w-full max-w-full px-0">

            <div class="flex gap-6">

                <aside class="w-56">
                    <div class="sticky top-20">
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
                                :class="activeTab === defaultTab
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = defaultTab">
                                Priotitas Inisiatif Berdasarkan Dual Growth Strategy 
                            </button>

                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'Prioritas Inisiatif Strategis Perusahaan'
                                    ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Prioritas Inisiatif Strategis Perusahaan'">
                                Priotitas Inisiatif Berdasarkan KBUMN Mission
                            </button>
                        </div>
                    </div>
                </aside>

                <main class="flex-1">
                    <div class="p-4 flex justify-center bg-white dark:bg-[#171717]">
                        <img src="/icon/MisiBUMN.png" alt="Misi BUMN Icon" class="h-80 w-auto object-contain" />
                    </div>
                    <div>
                        <BusinessStrategy v-if="activeTab === defaultTab || activeTab === 'Ringkasan Strategi'"
                            :goals="goals" :strategies="strategies" :active-tab="activeTab" />

                        <MisiBumn v-else-if="activeTab === 'Prioritas Inisiatif Strategis Perusahaan'"
                            :missions="misiBumn" />

                        <ListMisiBumn v-else-if="activeTab === 'KBUMN Mission'"
                            :missions="misiBumn" />

                        <StrategicHouseBusinessStrategy v-else-if="activeTab === 'Dual Growth'"
                            v-bind="dualGrowthProps" :readonly="true" />

                    </div>
                </main>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BusinessStrategy from '@/Components/ProgramPlanning/BusinessStrategy/BusinessStrategy.vue';
import MisiBumn from '@/Components/ProgramPlanning/BusinessStrategy/MisiBumn.vue';
import ListMisiBumn from '@/Components/ProgramPlanning/BusinessStrategy/ListMisiBumn.vue';
import StrategicHouseBusinessStrategy from '@/Components/StrategicHouse/BusinessStrategy/BusinessStrategy.vue';
import { ref } from 'vue';

const props = defineProps({
    goals: { type: Array, default: () => [] },
    strategies: { type: Object, default: () => ({}) },
    misiBumn: { type: Array, default: () => [] },
    dualGrowthProps: { type: Object, default: () => ({}) },
});

const defaultTab = 'Prioritas Initiative Strategis 2025 -2029';
const activeTab = ref(defaultTab);
</script>

<style scoped>
/* minimal styling */
</style>
