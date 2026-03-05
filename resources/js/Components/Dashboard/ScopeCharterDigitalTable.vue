<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ charterLabel }} Digital Initiatives</h2>
                    <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">
                        1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)
                    </p>
                </div>
                <Link
                    :href="`/digital-initiatives?status=${completedStatusId}`"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    Lihat {{ completedStatusLabel }}
                </Link>
            </div>
            <div v-if="$slots['header-filters']" class="mt-4">
                <slot name="header-filters" />
            </div>
        </div>

        <div class="overflow-x-hidden">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[4%]">
                    <col class="w-[7%]">
                    <col class="w-[9%]">
                    <col class="w-[12%]">
                    <col class="w-[17%]">
                    <col class="w-[10%]">
                    <col class="w-[7%]">
                    <col class="w-[7%]">
                    <col class="w-[9%]">
                    <col class="w-[8%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Desc</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rjjp</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Coe</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <tr v-for="item in items" :key="`digital-open-${item.id}`" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ displayVal(item, 'no') }}</td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ displayVal(item, 'type') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ displayVal(item, 'projectOwner') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="break-words">{{ displayVal(item, 'useCase') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="break-words" :title="displayVal(item, 'desc')">{{ displayVal(item, 'desc') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="line-clamp-2" :title="displayVal(item, 'value')">{{ displayVal(item, 'value') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ displayVal(item, 'urgency') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ displayVal(item, 'rjjp') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ displayVal(item, 'coe') }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(statusBadgeItem(item))">
                                {{ statusDisplay(item) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    :href="`/digital-initiatives/${item.id}`"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-sky-100 text-sky-700 transition-colors hover:bg-sky-200 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                    title="View"
                                >
                                    View
                                </Link>
                                <Link
                                    :href="`/digital-initiatives/${item.id}/edit`"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-700 transition-colors hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                    title="Edit"
                                >
                                    Edit
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
    categoryOptions: {
        type: Array,
        default: () => [],
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

const categoryLabel = (value) => {
    const found = (props.categoryOptions || []).find((c) => Number(c.id) === Number(value));
    return found ? found.label : '-';
};

const scoreLabel = (value) => {
    const map = { 1: 'Low', 2: 'Medium', 3: 'High', 4: 'TBC' };
    return map[Number(value)] ?? '-';
};

function displayVal(item, field) {
    if (!item || typeof item !== 'object') return '-';
    const v = (x) => x !== undefined && x !== null && String(x).trim() !== '';
    switch (field) {
        case 'no':
            return v(item.no) ? item.no : (item.id ?? '-');
        case 'type':
            return item.category_fase != null ? categoryLabel(item.category_fase) : cellVal(item, 'type');
        case 'projectOwner':
            if (item.organizations?.length) {
                return item.organizations.map((o) => o.name).join(', ');
            }
            return cellVal(item, 'projectOwner', 'project_owner');
        case 'useCase':
            return item.use_case?.name ?? cellVal(item, 'useCase', 'use_case');
        case 'desc':
            return item.useCase_description ?? cellVal(item, 'desc', 'description');
        case 'value':
            return item.value != null ? scoreLabel(item.value) : cellVal(item, 'value');
        case 'urgency':
            return item.urgency != null ? scoreLabel(item.urgency) : cellVal(item, 'urgency');
        case 'rjjp':
            if (item.rjpps?.length) return item.rjpps.map((r) => r.name).join(', ');
            return cellVal(item, 'rjjp');
        case 'coe':
            return item.use_case?.coe?.name ?? cellVal(item, 'coe');
        default:
            return cellVal(item, field);
    }
}

function statusDisplay(item) {
    if (item.statuses?.length) {
        return item.statuses.map((s) => s.name).join(', ');
    }
    return props.statusOptions?.length ? statusLabelFromOptions(item.status, props.statusOptions) : (item.status ?? '-');
}

function statusBadgeItem(item) {
    if (item.statuses?.length && item.statuses[0]) {
        return item.statuses[0].id;
    }
    return item.status;
}
</script>
