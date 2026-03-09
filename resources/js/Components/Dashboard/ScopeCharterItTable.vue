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
                    <col class="w-[12%]">
                    <col class="w-[8%]">
                    <col class="w-[10%]">
                    <col class="w-[11%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mapping</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Project Charter</th>
                        <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Status</th>
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
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(item.project_status_id ?? 0)">
                                    {{ statusLabelFromOptions(item.project_status_id ?? 0, statusOptions) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-[11px] text-slate-600 dark:text-slate-300">
                                {{ statusDateText(item) }}
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
const tableColspan = 7;

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

const statusDateText = (item) => {
    const rawValue = String(item?.project_status_date ?? '').trim();

    if (!rawValue) {
        return '-';
    }

    const date = new Date(rawValue);
    if (Number.isNaN(date.getTime())) {
        return rawValue;
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

</script>
