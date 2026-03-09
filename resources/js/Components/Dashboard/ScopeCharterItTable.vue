<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ charterLabel }} IT Initiatives</h2>
                </div>
            </div>
            <div v-if="$slots['header-filters']" class="mt-4">
                <slot name="header-filters" />
            </div>
        </div>

        <div class="overflow-x-hidden">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[3%]">
                    <col class="w-[6%]">
                    <col class="w-[12%]">
                    <col class="w-[14%]">
                    <col class="w-[14%]">
                    <col class="w-[8%]">
                    <col v-if="showImplementationColumns" class="w-[9%]">
                    <col v-if="showImplementationColumns" class="w-[9%]">
                    <col v-if="showImplementationColumns" class="w-[14%]">
                    <col class="w-[11%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mapping</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Usulan</th>
                        <th v-if="showImplementationColumns" scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Bulan / Tahun</th>
                        <th v-if="showImplementationColumns" scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Review</th>
                        <th v-if="showImplementationColumns" scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Implementasi</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <template v-for="(item, index) in sortedItems" :key="`it-open-${item.id}`">
                        <tr class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ index + 1 }}</td>
                            <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ item.code || '-' }}</td>
                            <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ item.charter?.category || 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                <span class="font-medium break-words text-slate-700 dark:text-slate-200">{{ item.name || '-' }}</span>
                            </td>
                            <td class="px-3 py-3 text-[11px] text-slate-600 dark:text-slate-300">
                                <template v-if="itemMappings(item).length > 0">
                                    <span
                                        v-for="(mapping, mIdx) in itemMappings(item)"
                                        :key="`mapping-${item.id}-${mapping.id}`"
                                        class="inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-[9px] font-medium text-violet-700 dark:bg-violet-500/20 dark:text-violet-300 mr-1 mb-0.5"
                                    >{{ mapping.code || mapping.name }}</span>
                                </template>
                                <span v-else class="text-[10px] italic text-slate-400">-</span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(item.status)">
                                    {{ statusLabelFromOptions(item.status, statusOptions) }}
                                </span>
                            </td>
                            <td v-if="showImplementationColumns" class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                {{ latestItDate(item) || '-' }}
                            </td>
                            <td v-if="showImplementationColumns" class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                <span
                                    v-if="latestItReviewStatus(item)"
                                    class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-medium break-words whitespace-normal leading-relaxed"
                                    :class="reviewStatusBadgeClass(latestItReviewStatus(item))"
                                    :title="latestItReviewStatus(item)"
                                >
                                    {{ latestItReviewStatus(item) }}
                                </span>
                                <span v-else class="text-xs italic text-slate-400 dark:text-slate-500">-</span>
                            </td>
                            <td v-if="showImplementationColumns" class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                <span
                                    v-if="latestItStatus(item)"
                                    class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-700 capitalize dark:bg-white/10 dark:text-slate-300 break-words whitespace-normal leading-relaxed"
                                    :title="latestItStatus(item)"
                                >
                                    {{ latestItStatus(item) }}
                                </span>
                                <span v-else class="text-xs italic text-slate-400 dark:text-slate-500">-</span>
                            </td>
                            <td class="px-3 py-3 text-[10px] font-medium">
                                <div class="flex flex-col items-start gap-1">
                                    <Link
                                        :href="`/it-initiatives/${item.id}`"
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-sky-100 text-sky-700 transition-colors hover:bg-sky-200 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                        title="View"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="`/it-initiatives/${item.id}/edit`"
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-700 transition-colors hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                        title="Edit"
                                    >
                                        Edit
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr v-if="sortedItems.length === 0">
                        <td :colspan="tableColspan" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
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
    charterLabel: {
        type: String,
        default: 'Scope Charter',
    },
    selectedStatusId: {
        type: [Number, String, null],
        default: null,
    },
});

const lowerCompletedStatusLabel = computed(() => String(props.completedStatusLabel || '').toLowerCase());
const approvedStatusId = computed(() => {
    const matched = (props.statusOptions || []).find((item) => String(item?.name).toLowerCase() === 'approved');
    return Number(matched?.id ?? 4);
});
const showImplementationColumns = computed(() => props.selectedStatusId === null || Number(props.selectedStatusId) === approvedStatusId.value);
const tableColspan = computed(() => (showImplementationColumns.value ? 10 : 7));

const sortedItems = computed(() => {
    return [...(props.items || [])].sort((a, b) => {
        const aId = Number(a?.id);
        const bId = Number(b?.id);
        const aValid = Number.isFinite(aId);
        const bValid = Number.isFinite(bId);

        if (!aValid && !bValid) return 0;
        if (!aValid) return 1;
        if (!bValid) return -1;

        return aId - bId;
    });
});

const itemMappings = (item) => {
    const source = item?.mapped_initiatives ?? item?.mappedInitiatives ?? [];
    return Array.isArray(source) ? source : [];
};

const latestItStatus = (item) => {
    const rawStatus = item?.latest_implementation_status
        ?? item?.latestImplementationStatus
        ?? item?.latest_pc_status_implementation?.status
        ?? item?.latestPcStatusImplementation?.status
        ?? item?.pc_status_implementations?.[0]?.status
        ?? item?.pcStatusImplementations?.[0]?.status
        ?? null;

    const normalizedStatus = String(rawStatus ?? '').trim();

    return normalizedStatus.length > 0 ? normalizedStatus : null;
};

const latestItReviewStatus = (item) => {
    const rawReviewStatus = item?.latest_implementation_status?.review_status
        ?? item?.latestImplementationStatus?.review_status
        ?? item?.latest_pc_status_implementation?.review_status
        ?? item?.latestPcStatusImplementation?.review_status
        ?? item?.pc_status_implementations?.[0]?.review_status
        ?? item?.pcStatusImplementations?.[0]?.review_status
        ?? null;

    const normalizedReviewStatus = String(rawReviewStatus ?? '').trim();

    return normalizedReviewStatus.length > 0 ? normalizedReviewStatus : null;
};

const reviewStatusBadgeClass = (reviewStatus) => {
    const normalized = String(reviewStatus ?? '').trim().toLowerCase();

    if (normalized === 'on track') {
        return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    }

    if (normalized === 'at risk') {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    }

    if (normalized === 'not started') {
        return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
    }

    if (normalized === 'not signed') {
        return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    }

    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const latestItDate = (item) => {
    const rawDate = item?.latest_implementation_status?.date
        ?? item?.latestImplementationStatus?.date
        ?? item?.latest_pc_status_implementation?.date
        ?? item?.latestPcStatusImplementation?.date
        ?? item?.pc_status_implementations?.[0]?.date
        ?? item?.pcStatusImplementations?.[0]?.date
        ?? null;

    if (!rawDate) return null;
    
    const date = new Date(rawDate);
    if (isNaN(date.getTime())) return null;

    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
};
</script>
