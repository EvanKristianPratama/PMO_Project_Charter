<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
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
});

const search = ref('');
const selectedGroup = ref('');
const selectedOrganization = ref('');
const selectedCompleteness = ref('');
const showDescriptions = ref(false);

const normalizedGroups = computed(() => props.groups || []);

const filteredGroups = computed(() => normalizedGroups.value
    .map((group) => {
        const rows = (group.rows || []).filter((row) => {
            const matchesGroup = !selectedGroup.value || row.group_key === selectedGroup.value;
            const matchesOrganization = !selectedOrganization.value
                || String(row.business_unit_id ?? '') === String(selectedOrganization.value);
            const completionCount = Number(row.completion_count ?? 0);
            const totalColumns = props.strategyColumns.length;
            const matchesCompleteness = (
                !selectedCompleteness.value
                || (selectedCompleteness.value === 'complete' && completionCount === totalColumns)
                || (selectedCompleteness.value === 'partial' && completionCount > 0 && completionCount < totalColumns)
                || (selectedCompleteness.value === 'empty' && completionCount === 0)
            );

            const keyword = String(search.value ?? '').trim().toLowerCase();
            const haystack = [
                row.business_unit,
                row.group_label,
                ...Object.values(row.values || {}),
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            const matchesSearch = keyword === '' || haystack.includes(keyword);

            return matchesGroup && matchesOrganization && matchesCompleteness && matchesSearch;
        });

        return {
            ...group,
            count: rows.length,
            rows,
        };
    })
    .filter((group) => group.rows.length > 0));

const visibleRows = computed(() => filteredGroups.value.flatMap((group) => group.rows || []));

const visibleStrategyCoverage = computed(() => props.strategyColumns.map((column) => ({
    ...column,
    filled_count: visibleRows.value.filter((row) => Boolean(row.values?.[column.key])).length,
})));

const visibleCompleteCount = computed(() => visibleRows.value.filter(
    (row) => Number(row.completion_count ?? 0) === props.strategyColumns.length,
).length);

const visiblePartialCount = computed(() => visibleRows.value.filter((row) => {
    const completionCount = Number(row.completion_count ?? 0);
    return completionCount > 0 && completionCount < props.strategyColumns.length;
}).length);

const visibleEmptyCount = computed(() => visibleRows.value.filter(
    (row) => Number(row.completion_count ?? 0) === 0,
).length);

const completionLabel = (row) => `${row.completion_count ?? 0}/${props.strategyColumns.length}`;

const toneClass = (tone) => `strategy-tone-${tone || 'slate'}`;
</script>

<template>
    <div class="space-y-4">
        <div v-if="filteredGroups.length > 0" class="space-y-4">
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div
                        v-for="strategy in visibleStrategyCoverage"
                        :key="`strategy-legend-${strategy.key}`"
                        class="flex items-center gap-1.5"
                    >
                        <span class="legend-swatch" :class="toneClass(strategy.tone)"></span>
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                            {{ strategy.label }}
                            <span class="font-medium text-slate-400 dark:text-slate-500">
                                ({{ strategy.filled_count }})
                            </span>
                        </span>
                    </div>

                    <div class="ml-1 flex items-center gap-1.5 border-l border-slate-300 pl-4 dark:border-white/10">
                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">
                            Visible Business Unit
                            <span class="font-medium text-slate-500 dark:text-slate-400">
                                ({{ visibleRows.length }})
                            </span>
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-100 pt-1 dark:border-white/5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Coverage
                    </span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        Complete ({{ visibleCompleteCount }})
                    </span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        Partial ({{ visiblePartialCount }})
                    </span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        Empty ({{ visibleEmptyCount }})
                    </span>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">
                        Total data: {{ summary.total_business_units ?? visibleRows.length }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <input
                        v-model="search"
                        type="text"
                        class="initiative-view-input"
                        placeholder="Cari business unit atau isi strategi"
                    >

                    <select v-model="selectedGroup" class="initiative-view-select">
                        <option value="">All Scope</option>
                        <option value="holding">Holding</option>
                        <option value="subholding">Sub Holding</option>
                        <option value="other">Other Organization</option>
                    </select>

                    <select v-model="selectedOrganization" class="initiative-view-select">
                        <option value="">All Business Unit</option>
                        <option
                            v-for="organization in organizationOptions"
                            :key="organization.value"
                            :value="organization.value"
                        >
                            {{ organization.label }}
                        </option>
                    </select>

                    <select v-model="selectedCompleteness" class="initiative-view-select">
                        <option value="">All Coverage</option>
                        <option value="complete">Complete</option>
                        <option value="partial">Partial</option>
                        <option value="empty">Empty</option>
                    </select>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showDescriptions }"
                        @click="showDescriptions = !showDescriptions"
                    >
                        <span>Column Notes</span>
                    </button>
                </div>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <h1 class="mb-1 mt-4 mb-4 text-center text-lg font-bold">
                        Dual Growth Business Strategy 2025 -2029
                    </h1>

                    <table class="strategy-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="top-head" style="width: 18%; vertical-align: middle;">
                                    Business Unit
                                </th>
                                <th colspan="3" class="top-head">
                                    Business Strategy Direction
                                </th>
                            </tr>
                            <tr>
                                <th
                                    v-for="column in strategyColumns"
                                    :key="column.key"
                                    class="top-head border-t-0"
                                    :class="toneClass(column.tone)"
                                >
                                    <div class="top-head__content">
                                        <span>{{ column.label }}</span>
                                        <small v-if="showDescriptions">{{ column.description }}</small>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <template v-for="group in filteredGroups" :key="group.key">
                                <tr class="group-row">
                                    <td colspan="4">
                                        <div class="group-row__content">
                                            <span>{{ group.label }}</span>
                                            <span class="count-capsule">{{ group.count }}</span>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-for="row in group.rows" :key="row.id">
                                    <td class="primary-cell">
                                        <div class="primary-cell__content">
                                            <div class="primary-label-wrapper">
                                                <span class="text-xs">{{ row.business_unit }}</span>
                                                <span class="count-capsule">{{ completionLabel(row) }}</span>
                                            </div>
                                            <span class="primary-cell__meta">{{ row.group_label }}</span>
                                        </div>
                                    </td>

                                    <td
                                        v-for="column in strategyColumns"
                                        :key="`${row.id}-${column.key}`"
                                        class="strategy-cell"
                                    >
                                        <article
                                            class="strategy-box"
                                            :class="[
                                                toneClass(column.tone),
                                                { 'strategy-box--empty': !row.values?.[column.key] },
                                            ]"
                                        >
                                            <p v-if="row.values?.[column.key]" class="strategy-box__value">
                                                {{ row.values[column.key] }}
                                            </p>
                                            <p v-else class="strategy-box__empty">
                                                Belum diisi
                                            </p>
                                        </article>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section
            v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
        >
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Tidak ada data business strategy untuk filter ini.
            </p>
            <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                Matrix akan tampil otomatis saat tabel <code>trs_business_strategy</code> memiliki data yang sesuai.
            </p>
        </section>
    </div>
</template>

<style scoped>
.strategy-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    min-width: 1180px;
}

.strategy-table th,
.strategy-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.top-head {
    padding: 10px 12px;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    text-align: center;
    letter-spacing: 0.05em;
}

.top-head__content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.top-head__content small {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0;
    opacity: 0.9;
}

.group-row td {
    background: #e8f2fb;
    padding: 8px 12px;
}

.group-row__content {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #0f4c81;
}

.primary-cell {
    min-width: 220px;
    background: #f8fbff;
    vertical-align: middle !important;
}

.primary-cell__content {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 12px;
    color: #1e293b;
}

.primary-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.primary-cell__meta {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.strategy-cell {
    padding: 10px;
    background: #f8fafc;
    min-width: 260px;
}

.strategy-box {
    min-height: 108px;
    border: 1px solid #cbd5e1;
    border-left-width: 5px;
    background: #ffffff;
    padding: 12px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.strategy-box:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}

.strategy-box__value {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.45;
    color: #1f2937;
    white-space: pre-line;
}

.strategy-box__empty {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    font-style: italic;
}

.strategy-box--empty {
    background: #ffffff;
    border-style: dashed;
}

.count-capsule {
    display: inline-flex;
    min-width: 18px;
    height: 18px;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 0 5px;
    background: rgba(15, 23, 42, 0.08);
    font-size: 9px;
    font-weight: 800;
    color: inherit;
}

.initiative-view-switch {
    display: flex;
    width: 100%;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 2px;
    scrollbar-width: none;
}

.initiative-view-switch::-webkit-scrollbar {
    display: none;
}

.initiative-view-select,
.initiative-view-input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
}

.initiative-view-select {
    appearance: none;
    cursor: pointer;
    padding-right: 24px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
}

.initiative-view-input {
    min-width: 260px;
}

.initiative-view-select:hover,
.initiative-view-input:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.initiative-view-select:focus,
.initiative-view-input:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
    cursor: pointer;
}

.bu-toggle-btn:hover {
    border-color: #0f6fb7;
    background: #f8fafc;
}

.bu-toggle-btn--active {
    background: #0f6fb7;
    border-color: #0f6fb7;
    color: #ffffff;
}

.bu-toggle-btn--active:hover {
    background: #0d5ea1;
    border-color: #0d5ea1;
}

.legend-swatch {
    display: block;
    width: 12px;
    min-width: 12px;
    height: 12px;
    border-radius: 2px;
}

.strategy-tone-sky {
    border-color: #1d4ed8 !important;
}

.strategy-box.strategy-tone-sky {
    background: #eff6ff;
}

.legend-swatch.strategy-tone-sky,
.top-head.strategy-tone-sky {
    background: #1d4ed8;
}

.strategy-tone-amber {
    border-color: #b45309 !important;
}

.strategy-box.strategy-tone-amber {
    background: #fffbeb;
}

.legend-swatch.strategy-tone-amber,
.top-head.strategy-tone-amber {
    background: #b45309;
}

.strategy-tone-emerald {
    border-color: #047857 !important;
}

.strategy-box.strategy-tone-emerald {
    background: #ecfdf5;
}

.legend-swatch.strategy-tone-emerald,
.top-head.strategy-tone-emerald {
    background: #047857;
}

.strategy-tone-slate {
    border-color: #475569 !important;
}

.strategy-box.strategy-tone-slate {
    background: #f8fafc;
}

.legend-swatch.strategy-tone-slate,
.top-head.strategy-tone-slate {
    background: #475569;
}

:deep(.dark) .group-row td {
    background: rgba(15, 111, 183, 0.16);
}

:deep(.dark) .primary-cell {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .strategy-cell {
    background: rgba(15, 23, 42, 0.4);
}

:deep(.dark) .strategy-box {
    color: #e2e8f0;
}

:deep(.dark) .strategy-box__value {
    color: #e2e8f0;
}

:deep(.dark) .strategy-box--empty {
    background: rgba(15, 23, 42, 0.45);
}

:deep(.dark) .initiative-view-select,
:deep(.dark) .initiative-view-input,
:deep(.dark) .bu-toggle-btn {
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(15, 23, 42, 0.55);
    color: #cbd5e1;
}
</style>
