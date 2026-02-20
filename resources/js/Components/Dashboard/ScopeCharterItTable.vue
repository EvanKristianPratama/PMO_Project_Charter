<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div>
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Scope Charter IT Initiatives</h2>
            </div>
            <Link
                :href="`/it-initiatives?status=${completedStatusId}`"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                Lihat {{ completedStatusLabel }}
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Initiative</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <tr v-for="item in items" :key="`it-open-${item.id}`">
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ item.code || '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="max-w-xs">
                                <p class="truncate font-medium text-slate-800 dark:text-slate-100">{{ item.name || '-' }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ item.charter?.category || 'Uncategorized' }}</p>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(item.status)">
                                {{ statusLabelFromOptions(item.status, statusOptions) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <Link
                                    :href="`/it-initiatives/${item.id}`"
                                    class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-indigo-400"
                                    title="View"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </Link>
                                <Link
                                    :href="`/it-initiatives/${item.id}/edit`"
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
                        <td colspan="4" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                            Semua IT initiatives sudah {{ lowerCompletedStatusLabel }}.
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
});

const lowerCompletedStatusLabel = computed(() => String(props.completedStatusLabel || '').toLowerCase());
</script>
