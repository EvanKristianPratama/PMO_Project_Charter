<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div>
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ charterLabel }} Digital Initiatives</h2>
            </div>
            <Link
                :href="`/digital-initiatives?status=${completedStatusId}`"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                Lihat {{ completedStatusLabel }}
            </Link>
        </div>

        <div class="overflow-x-auto overflow-y-visible">
            <table class="min-w-max w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                        <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                        <th class="min-w-[280px] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Desc</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rjjp</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Coe</th>
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="sticky right-0 z-10 whitespace-nowrap bg-slate-50 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <tr v-for="item in items" :key="`digital-open-${item.id}`">
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ cellVal(item, 'no') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'type') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'projectOwner', 'project_owner') }}</td>
                        <td class="min-w-[180px] max-w-[320px] px-4 py-3 text-slate-700 dark:text-slate-200">
                            <span class="whitespace-normal break-words">{{ cellVal(item, 'useCase', 'use_case') }}</span>
                        </td>
                        <td class="min-w-[280px] max-w-[400px] px-4 py-3 text-slate-700 dark:text-slate-200">
                            <span class="whitespace-normal break-words" :title="cellVal(item, 'desc', 'description')">{{ cellVal(item, 'desc', 'description') }}</span>
                        </td>
                        <td class="max-w-[150px] px-4 py-3 text-slate-700 dark:text-slate-200">
                            <span class="line-clamp-2" :title="cellVal(item, 'value')">{{ cellVal(item, 'value') }}</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'urgency') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'rjjp') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'coe') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(item.status)">
                                {{ statusLabelFromOptions(item.status, statusOptions) }}
                            </span>
                        </td>
                        <td class="sticky right-0 z-10 whitespace-nowrap bg-white px-4 py-3 text-right shadow-[-4px_0_8px_rgba(0,0,0,0.05)] dark:bg-[#171717] dark:shadow-[-4px_0_8px_rgba(0,0,0,0.2)]">
                            <div class="flex items-center justify-end gap-1">
                                <Link
                                    :href="`/digital-initiatives/${item.id}`"
                                    class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-indigo-400"
                                    title="View"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </Link>
                                <Link
                                    :href="`/digital-initiatives/${item.id}/edit`"
                                    class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-amber-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-amber-400"
                                    title="Edit"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="11" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                            Semua digital initiatives sudah {{ lowerCompletedStatusLabel }}.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
    completedStatusLabel: {
        type: String,
        default: 'Baseline',
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    charterLabel: {
        type: String,
        default: 'Scope Charter',
    },
});

const lowerCompletedStatusLabel = computed(() => String(props.completedStatusLabel || '').toLowerCase());

function cellVal(item, ...keys) {
    if (!item || typeof item !== 'object') return '-';
    for (const key of keys) {
        const value = item[key];
        if (value !== undefined && value !== null && String(value).trim() !== '') return value;
    }

    return '-';
}
</script>
