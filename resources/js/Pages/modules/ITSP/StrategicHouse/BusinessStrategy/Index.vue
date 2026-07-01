<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="animate-fade-in-up space-y-6 pb-8">
            <BusinessStrategy
                :page="page"
                :summary="summary"
                :header-goals="headerGoals"
                :enabler-goals="enablerGoals"
                :groups="groups"
                :strategy-columns="strategyColumns"
                :organization-options="organizationOptions"
                :status-periods="statusPeriods"
            />

            <!-- Key Enablers Section -->
            <div class="flex flex-col">
                <div class="mb-1 inline-flex">
                    <h3 class="text-[15px] font-extrabold text-[#0f6fb7] pr-8 pb-0.5 dark:text-blue-400 dark:border-blue-400">
                        Key enablers
                    </h3>
                </div>
                
                <div class="flex flex-col md:flex-row border border-[#0f6fb7] bg-white dark:border-blue-800 dark:bg-[#171717]">
                    
                    <!-- Column 1 -->
                    <div class="flex-1 p-3 border-b md:border-b-0 border-dashed border-[#c5d6e8] md:border-r dark:border-slate-700">
                        <h4 class="text-[12px] font-bold text-[#0f6fb7] dark:text-blue-400">HSSE & Sustainability</h4>
                        <p class="mt-1 text-[11px] leading-snug text-slate-700 dark:text-slate-300">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">HSSE</span> Excellence with assurance of biz. sustainability including Net Zero Target
                        </p>
                    </div>
                    
                    <!-- Column 2 -->
                    <div class="flex-1 p-3 border-b md:border-b-0 border-dashed border-[#c5d6e8] md:border-r dark:border-slate-700">
                        <h4 class="text-[12px] font-bold text-[#0f6fb7] dark:text-blue-400">Technology & Innovation</h4>
                        <p class="mt-1 text-[11px] leading-snug text-slate-700 dark:text-slate-300">
                            Technology excellence for Pertamina's competitiveness
                        </p>
                    </div>
                    
                    <!-- Column 3 (Highlighted) -->
                    <div class="flex-1 p-3 border-b md:border-b-0 border-dashed border-[#c5d6e8] md:border-r dark:border-slate-700">
                        <h4 class="text-[12px] font-bold text-[#0f6fb7] dark:text-blue-400">Digital Transformation</h4>
                        <p class="mt-1 text-[11px] leading-snug text-slate-800 dark:text-slate-200">
                            Digital transformation program to support Pertamina Group Technology, Internet of Things (IoT), Automation, Artificial Intelligence (AI)
                        </p>
                    </div>
                    
                    <!-- Column 4 -->
                    <div class="flex-1 p-3 border-b md:border-b-0 border-dashed border-[#c5d6e8] md:border-r dark:border-slate-700">
                        <h4 class="text-[12px] font-bold text-[#0f6fb7] dark:text-blue-400">Policy Advocacy</h4>
                        <p class="mt-1 text-[11px] leading-snug text-slate-700 dark:text-slate-300">
                            Advocating policy and stakeholder management
                        </p>
                    </div>
                    
                    <!-- Column 5 -->
                    <div class="flex-1 p-3">
                        <h4 class="text-[12px] font-bold text-[#0f6fb7] dark:text-blue-400">Human Resources & Execution</h4>
                        <p class="mt-1 text-[11px] leading-snug text-slate-700 dark:text-slate-300">
                            Development of human resources and simplification of business process
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import BusinessStrategy from '@/Components/modules/ITSP/StrategicHouse/BusinessStrategy/BusinessStrategy.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    headerGoals: {
        type: Object,
        default: () => ({}),
    },
    enablerGoals: {
        type: Array,
        default: () => [],
    },
    groups: {
        type: Array,
        default: () => [],
    },
    strategyColumns: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (
    props.embedded
        ? {}
        : { title: props.page?.title ?? 'Business Strategy' }
));
</script>
