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
                                :class="activeTab === 'Prioritas Inisitif Strategis Perusahaan'
                                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'Prioritas Inisitif Strategis Perusahaan'">
                                Prioritas Inisitif Strategis Perusahaan
                            </button>
                            
                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === 'List Strategy'
                                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = 'List Strategy'">
                                List OfStrategy
                            </button>
                            
                            <button type="button"
                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                                :class="activeTab === defaultTab
                                        ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white'
                                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                                    " @click="() => activeTab = defaultTab">
                                Dual Growth Strategy Priotitas Inisiatif
                            </button>
                        </div>
                    </div>
                </aside>

                <main class="flex-1">
                    <div>
                        <BusinessStrategy v-if="activeTab === defaultTab || activeTab === 'Ringkasan Strategi'"
                            :goals="goals" :strategies="strategies" :active-tab="activeTab" />

                        <MisiBumn v-else-if="activeTab === 'Prioritas Inisitif Strategis Perusahaan'" :missions="misiBumn" />

                        <ListOfStrategy v-else-if="activeTab === 'List Strategy'" :strategies="strategies" />
                    </div>
                </main>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BusinessStrategy from '@/Components/ProgramPlanning/BusinessStrategy/BusinessStrategy.vue';
import ListOfStrategy from '@/Components/ProgramPlanning/BusinessStrategy/ListOfStrategy.vue';
import MisiBumn from '@/Components/ProgramPlanning/BusinessStrategy/MisiBumn.vue';
import { ref } from 'vue';

const props = defineProps({
    goals: { type: Array, default: () => [] },
    strategies: { type: Object, default: () => ({}) },
    misiBumn: { type: Array, default: () => [] },
});

const defaultTab = 'Prioritas Initiative Strategis 2025 -2029';
const activeTab = ref(defaultTab);
</script>

<style scoped>
/* minimal styling */
</style>
