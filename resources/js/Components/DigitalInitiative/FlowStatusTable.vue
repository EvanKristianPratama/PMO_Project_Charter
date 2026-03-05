<template>
    <div class="mb-2 flex items-center justify-end">
        <p class="text-[10px] text-slate-500 dark:text-slate-400">
            1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)
        </p>
    </div>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
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
                    <tr v-for="item in items" :key="`digital-${item.id}`" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ cellVal(item, 'no') }}</td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ cellVal(item, 'type') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ cellVal(item, 'projectOwner', 'project_owner') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="break-words">{{ cellVal(item, 'useCase', 'use_case') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="break-words" :title="cellVal(item, 'desc', 'description')">{{ cellVal(item, 'desc', 'description') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="line-clamp-2" :title="displayScore(item, 'value')">{{ displayScore(item, 'value') }}</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ displayScore(item, 'urgency') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ cellVal(item, 'rjjp') }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ cellVal(item, 'coe') }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(item.status)">
                                {{ statusLabelFromOptions(item.status, statusOptions) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    :href="`/digital-initiatives/${item.id}`"
                                    :class="actionCellClass(hasScopeCharter(item))"
                                    title="View Scope Charter"
                                >
                                    Scope Charter
                                </Link>
                                <Link
                                    :href="`/digital-initiatives/${item.id}`"
                                    :class="actionCellClass(hasProjectCharter(item))"
                                    title="View Project Charter"
                                >
                                    Project Charter
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="11" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            <span v-if="activeFlowFilter === null">
                                Silakan klik salah satu status di atas untuk menampilkan data inisiatif.
                            </span>
                            <span v-else>
                                Tidak ada data yang sesuai dengan filter opsi ini.
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    activeFlowFilter: {
        type: [Number, String],
        default: null,
    },
});

function cellVal(item, ...keys) {
    if (!item || typeof item !== 'object') return '-';

    for (const key of keys) {
        const value = item[key];
        if (value !== undefined && value !== null && String(value).trim() !== '') {
            return value;
        }
    }

    return '-';
}

const scoreLabel = (value) => {
    const map = { 1: 'Low', 2: 'Medium', 3: 'High', 4: 'TBC' };
    return map[Number(value)] ?? '-';
};

function displayScore(item, field) {
    const rawValue = cellVal(item, field);
    if (rawValue === '-' || rawValue == null) return rawValue;
    
    // If it's a number 1-4, map it. Otherwise return the value directly (some statuses might already be mapped from parent)
    const num = Number(rawValue);
    if (!isNaN(num) && num >= 1 && num <= 4) {
        return scoreLabel(num);
    }
    return rawValue;
}

function hasFilled(value) {
    return value !== null && value !== undefined && String(value).trim() !== '' && String(value) !== '-';
}

function hasScopeCharter(item) {
    return hasFilled(cellVal(item, 'useCase', 'use_case')) && hasFilled(cellVal(item, 'desc', 'description'));
}

function hasProjectCharter(item) {
    return [
        cellVal(item, 'projectOwner', 'project_owner'),
        cellVal(item, 'value'),
        cellVal(item, 'urgency'),
        cellVal(item, 'rjjp'),
        cellVal(item, 'coe'),
    ].every(hasFilled);
}

function actionCellClass(isReady) {
    if (isReady) {
        return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30 transition-colors cursor-pointer';
    }

    return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30 transition-colors cursor-pointer';
}
</script>
