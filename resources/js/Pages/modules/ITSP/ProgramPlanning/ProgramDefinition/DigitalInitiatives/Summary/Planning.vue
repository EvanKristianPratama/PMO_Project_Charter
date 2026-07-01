<template>
    <div class="space-y-6">
        <div v-if="initiativeMaster" class="space-y-4">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-[#1e4f8f]"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Planning</h2>
            </div>

            <div
                class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                <div
                    class="border-b border-slate-200 bg-slate-50 px-4 py-2 dark:border-white/10 dark:bg-white/5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Status History
                    </h3>
                </div>
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                        <tr>
                            <th class="w-40 px-4 py-2">Tanggal</th>
                            <th class="w-48 px-4 py-2">Status</th>
                            <th class="px-4 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr v-for="status in (initiativeMaster.status_history ?? [])" :key="status.id"
                            class="transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-2">{{ formatDate(status.tanggal) }}</td>
                            <td class="px-4 py-2">
                                <span
                                    :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-tight capitalize shadow-sm transition-all', getStatusClass(status.status)]">
                                    {{ status.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-slate-500">{{ status.notes || '-' }}</td>
                        </tr>
                        <tr v-if="!initiativeMaster.status_history?.length">
                            <td colspan="3" class="px-4 py-8 text-center italic text-slate-500">
                                No status history available.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DigitalInitiativeCharterDocument :initiative="initiativeMaster" />
        </div>

        <div v-if="roadmapItems && roadmapItems.length"
            class="mt-6 space-y-4 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
            </div>
            <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear" :end-year="roadmapEndYear" />
        </div>

        <div v-else class="mt-6 space-y-1 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
            </div>
            <div
                class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                <h3 class="mb-1 mt-1 text-sm font-bold text-slate-900 dark:text-white">Roadmap Not Available</h3>
            </div>
        </div>

        <div v-if="compendiumData" class="mt-6 space-y-4 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
            </div>
            <CompendiumCharterDocument :form="compendiumData" :editable="false" :coe-options="coeOptions"
                :source-options="sourceOptions" :theme-options="themeOptions" />
        </div>

        <div v-else class="mt-6 space-y-1 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
            </div>
            <div
                class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                <h3 class="mb-1 mt-1 text-sm font-bold text-slate-900 dark:text-white">Compendium Not Available</h3>
            </div>
        </div>

        <div v-if="appendixData" class="mt-6 space-y-4 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
            </div>
            <AppendixCharterDocument :initiative="computedAppendixData" :editable="false" :coe-options="coeOptions"
                :theme-options="themeOptions" :organization-options="organizationOptions" />
        </div>

        <div v-else class="mt-6 space-y-1 border-t border-slate-200 pt-6 dark:border-white/10">
            <div class="flex items-center gap-2 px-1">
                <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
            </div>
            <div
                class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                <h3 class="mb-1 mt-1 text-sm font-bold text-slate-900 dark:text-white">Appendix Not Available</h3>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppendixCharterDocument from '@/Components/modules/ITSP/Appendix/AppendixCharterDocument.vue';
import CompendiumCharterDocument from '@/Components/modules/ITSP/Compendium/CompendiumCharterDocument.vue';
import DigitalInitiativeCharterDocument from '@/Components/modules/ITSP/DigitalInitiative/DigitalInitiativeCharterDocument.vue';
import DigitalRoadmapComponent from '@/Components/modules/ITSP/Roadmap/Digital/DigitalRoadmapComponent.vue';

defineProps({
    initiativeMaster: { type: Object, default: () => ({}) },
    compendiumData: { type: Object, default: null },
    appendixData: { type: Object, default: null },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    computedAppendixData: { type: Object, default: null },
    formatDate: { type: Function, required: true },
    getStatusClass: { type: Function, required: true },
});
</script>
