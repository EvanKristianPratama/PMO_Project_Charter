<template>
    <ModulLayout title="Operating Model">
        <div class="animate-fade-in-up space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col justify-between gap-4 border-b border-slate-200 pb-4 dark:border-white/10 sm:flex-row sm:items-center">
                <!-- Tab Controls -->
                <div class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto">
                    <button v-for="tab in tabs" :key="tab"
                        @click="activeTab = tab" 
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="activeTab === tab 
                            ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400' 
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'">
                        {{ tab }}
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="transition-all duration-300">
                
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref } from 'vue';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import {
    ShieldCheckIcon,
    PresentationChartBarIcon,
    CircleStackIcon,
    InformationCircleIcon
} from '@heroicons/vue/24/outline';

const tabs = ['COBIT 2019', 'RACI Analysis'];
const activeTab = ref('COBIT 2019');
</script>
