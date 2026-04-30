<template>
    <div class="space-y-6">
        <div class="flex items-center gap-2 px-1">
            <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Evaluation</h2>
        </div>

        <DigitalInitiativeEvaluation v-if="unifiedInitiative" :initiative="unifiedInitiative" class="overflow-hidden" />

        <div class="flex items-center gap-2 px-1">
            <div class="h-6 w-1 rounded-full bg-blue-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation Analysis</h2>
        </div>

        <div class="flex justify-start">
            <div class="score-panel flex w-full max-w-4xl flex-col overflow-hidden border border-[#3b82f6]">
                <div v-if="roadmapDuration"
                    class="group flex cursor-pointer border-b border-[#3b82f6] hover:bg-slate-50"
                    @click="isRoadmapExpanded = !isRoadmapExpanded">
                    <div class="flex flex-1 border-r border-[#3b82f6]">
                        <div
                            class="bar-sub-mini flex min-w-[130px] shrink-0 items-center justify-center border-r border-[#3b82f6] group-hover:bg-[#255b8a]">
                            Project Duration
                        </div>
                        <div class="panel-body-mini flex flex-1 items-center justify-start px-4 text-left">
                            <div class="flex w-full items-center justify-between">
                                <span>{{ roadmapDuration }}</span>
                                <svg class="h-4 w-4 text-[#3b82f6] transition-transform duration-200"
                                    :class="{ 'rotate-180': isRoadmapExpanded }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19 9-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body-mini relative min-h-[32px] flex-1 overflow-hidden p-0">
                        <div class="absolute inset-0 flex">
                            <div v-for="year in roadmapYears" :key="year"
                                class="flex flex-1 flex-col border-r-[1.5px] border-r-[#3b82f6] last:border-0">
                                <div
                                    class="flex h-[18px] items-center justify-center border-b border-[#3b82f6] bg-slate-50 text-[10px] font-bold uppercase leading-none tracking-wider text-slate-500">
                                    {{ year }}
                                </div>
                                <div class="flex flex-1">
                                    <div v-for="q in 4" :key="q" class="flex-1 border-r border-slate-100 last:border-0"></div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 top-[18px] flex items-center">
                            <div class="relative h-2 w-full">
                                <div class="absolute h-full rounded-sm bg-[#1e4f8f] shadow-sm"
                                    :style="roadmapBarStyle" :title="roadmapDuration"></div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="panel-body-mini w-[100px] items-center justify-center border-l border-[#3b82f6] bg-slate-50 text-center text-[10px] font-bold uppercase text-[#1e4f8f]">
                        Status Updated
                    </div>
                </div>

                <div v-if="isRoadmapExpanded" class="animate-fade-in overflow-hidden border-b border-[#3b82f6] bg-white">
                    <DigitalRoadmapSummary :items="roadmapItems" :start-year="roadmapStartYear" :end-year="roadmapEndYear" />
                </div>

                <div v-if="computedAppendixData && computedAppendixData.urgency_expected !== '-'"
                    class="flex border-b border-[#3b82f6] last:border-0">
                    <div class="flex flex-1 border-r border-[#3b82f6]">
                        <div class="bar-sub-mini flex min-w-[130px] shrink-0 items-center justify-center border-r border-[#3b82f6]">
                            Expected Go Live
                        </div>
                        <div class="panel-body-mini flex flex-1 items-center justify-start px-4 text-left">
                            {{ computedAppendixData.urgency_expected }}
                        </div>
                    </div>

                    <div class="panel-body-mini relative min-h-[32px] flex-1 overflow-hidden p-0">
                        <div v-if="isRoadmapExpanded" class="absolute inset-0 flex">
                            <div v-for="year in roadmapYears" :key="year"
                                class="flex flex-1 flex-col border-r-[1.5px] border-r-[#3b82f6] last:border-0">
                                <div class="flex flex-1">
                                    <div v-for="q in 4" :key="q" class="flex-1 border-r border-slate-100 last:border-0"></div>
                                </div>
                            </div>
                        </div>
                        <div v-if="isRoadmapExpanded" class="absolute inset-0">
                            <div class="absolute h-full bg-emerald-500"
                                :style="goLiveBarStyle"
                                :title="'Expected Go Live: ' + computedAppendixData.urgency_expected"></div>
                        </div>
                    </div>

                    <div class="panel-body-mini w-[100px] border-l border-[#3b82f6] bg-slate-50/30"></div>
                </div>

                <div v-if="computedAppendixData && computedAppendixData.urgency_expected !== '-' && statusReviewMarkers.length > 0"
                    class="relative">
                    <!-- Merged Status Review Label -->
                    <div
                        class="absolute bottom-0 left-0 top-0 z-20 flex w-[130px] items-center justify-center border-r border-[#3b82f6] bg-[#2e6ea2] px-3 text-center text-[11px] font-medium leading-tight text-white">
                        Status Review
                    </div>

                    <DigitalRoadmapStatus v-for="(marker, idx) in statusReviewMarkers" :key="`status-row-${idx}`" :show="true"
                        :is-roadmap-expanded="isRoadmapExpanded" :roadmap-years="roadmapYears" :markers="[marker]"
                        :status-review-data="`${marker.label} - ${marker.status}`" :status-updated="marker.statusUpdated"
                        :is-first="idx === 0" :is-last="idx === statusReviewMarkers.length - 1" :hide-label-text="true" />
                </div>
            </div>
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
            <div class="h-6 w-1 rounded-full bg-red-600"></div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Summary Review</h2>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                <form @submit.prevent="$emit('submit-note')" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500">Month</label>
                            <select v-model="noteForm.month"
                                class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5">
                                <option v-for="month in months" :key="month" :value="month">{{ month }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500">Year</label>
                            <select v-model="noteForm.year"
                                class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5">
                                <option v-for="year in noteYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">Notes</label>
                        <textarea v-model="noteForm.notes" rows="3"
                            class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5"
                            placeholder="Enter review notes..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button v-if="editingNoteId" type="button" @click="$emit('cancel-edit-note')"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5">
                            Cancel
                        </button>
                        <button type="submit" :disabled="noteForm.processing"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                            {{ editingNoteId ? 'Update Note' : 'Add Note' }}
                        </button>
                    </div>
                </form>
            </div>

            <div v-if="summaryReviewNotes && summaryReviewNotes.length > 0" class="space-y-3">
                <div v-for="note in summaryReviewNotes" :key="note.id"
                    class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="text-xs font-bold uppercase text-blue-600">{{ note.month }} {{ note.year }}</span>
                            </div>
                            <p class="whitespace-pre-wrap text-sm text-slate-700 dark:text-slate-300">{{ note.notes }}</p>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                            <button @click="$emit('edit-note', note)" class="p-1 text-slate-400 transition-colors hover:text-blue-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button @click="$emit('delete-note', note.id)" class="p-1 text-slate-400 transition-colors hover:text-red-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="rounded-xl border border-dashed border-slate-200 p-8 text-center dark:border-white/10">
                <p class="text-sm italic text-slate-500">No review notes available for this initiative.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import DigitalInitiativeEvaluation from '@/Components/DigitalInitiative/DigitalInitiativeEvaluation.vue';
import DigitalRoadmapStatus from '@/Components/Roadmap/Digital/DigitalRoadmapStatus.vue';
import DigitalRoadmapSummary from '@/Components/Roadmap/Digital/DigitalRoadmapSummary.vue';

defineEmits(['submit-note', 'edit-note', 'cancel-edit-note', 'delete-note']);

defineProps({
    unifiedInitiative: { type: Object, default: null },
    statusImplementations: { type: Array, default: () => [] },
    summaryReviewNotes: { type: Array, default: () => [] },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },
    computedAppendixData: { type: Object, default: null },
    months: { type: Array, default: () => [] },
    noteYears: { type: Array, default: () => [] },
    noteForm: { type: Object, required: true },
    editingNoteId: { type: [Number, String], default: null },
    roadmapDuration: { type: String, default: null },
    roadmapYears: { type: Array, default: () => [] },
    roadmapBarStyle: { type: Object, default: () => ({}) },
    goLiveBarStyle: { type: Object, default: () => ({}) },
    statusReviewMarkers: { type: Array, default: () => [] },
});

const isRoadmapExpanded = ref(false);
</script>

<style scoped>
.score-panel {
    display: flex;
    background: #fff;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 12px;
    font-size: 11px;
    line-height: 1.2;
    text-align: center;
    min-height: 32px;
}

.panel-body-mini {
    padding: 3px 12px;
    background: #fff;
    min-height: 32px;
    display: flex;
    align-items: center;
    font-size: 12px;
    color: #0f172a;
}
</style>
