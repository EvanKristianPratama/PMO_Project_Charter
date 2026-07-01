<template>
    <div class="space-y-6">
        <div class="flex items-center gap-2 px-1">
            <div class="h-6 w-1 rounded-full bg-blue-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Implementation</h2>
        </div>

        <DigitalInitiativeHeader v-if="unifiedInitiative" :initiative="unifiedInitiative" class="overflow-hidden" />

        <div class="flex items-center gap-2 px-1">
            <div class="h-6 w-1 rounded-full bg-blue-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation</h2>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead
                    class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">End</th>
                        <th class="px-4 py-3">Year</th>
                        <th class="px-4 py-3">Current PIC</th>
                        <th class="px-4 py-3">Review Status</th>
                        <th class="px-4 py-3">Status Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-for="impl in statusImplementations" :key="impl.id"
                        class="transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                        <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                        <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                        <td class="px-4 py-3">{{ impl.pic || '-' }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ impl.review_status || '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ impl.status_updated || '-' }}</td>
                    </tr>
                    <tr v-if="!statusImplementations || !statusImplementations.length">
                        <td colspan="6" class="px-4 py-12 text-center italic text-slate-500">
                            Status Implementation Not Available
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center gap-2 px-1">
            <div class="h-6 w-1 rounded-full bg-purple-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
        </div>

        <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear" :end-year="roadmapEndYear" />
    </div>
</template>

<script setup>
import DigitalInitiativeHeader from '@/Components/modules/ITSP/DigitalInitiative/DigitalInitiativeHeader.vue';
import DigitalRoadmapComponent from '@/Components/modules/ITSP/Roadmap/Digital/DigitalRoadmapComponent.vue';

defineProps({
    unifiedInitiative: { type: Object, default: null },
    statusImplementations: { type: Array, default: () => [] },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },
});
</script>
