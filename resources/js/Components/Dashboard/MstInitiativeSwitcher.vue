<template>
    <div class="space-y-4">
        <section class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ title }}</h2>
                <div class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 p-1 dark:border-white/10 dark:bg-[#1f1f1f]">
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                        :class="viewMode === 'table'
                            ? 'bg-white text-slate-900 shadow-sm dark:bg-[#2a2a2a] dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                        @click="viewMode = 'table'"
                    >
                        Table Mode
                    </button>
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                        :class="viewMode === 'block'
                            ? 'bg-white text-slate-900 shadow-sm dark:bg-[#2a2a2a] dark:text-white'
                            : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                        @click="viewMode = 'block'"
                    >
                        Block Mode
                    </button>
                </div>
            </div>
        </section>

        <InitiativeStatusTable
            v-if="viewMode === 'table'"
            :items="itemsWithInitialType"
            :title="title"
            :initial-tipe-filter="initialTipeFilter"
        />

        <section v-else class="space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Group By</h4>
                    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 p-1 dark:border-white/10 dark:bg-[#1f1f1f]">
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="blockGroupBy === 'coe'
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#2a2a2a] dark:text-white'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            @click="blockGroupBy = 'coe'"
                        >
                            CoE
                        </button>
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                            :class="blockGroupBy === 'organization'
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#2a2a2a] dark:text-white'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            @click="blockGroupBy = 'organization'"
                        >
                            Project Owner / Organisasi
                        </button>
                    </div>
                </div>
            </section>

            <article
                v-for="section in boardSections"
                :key="`board-section-${section.key}`"
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-4 shadow-sm dark:border-white/20 dark:bg-[#171717]"
            >
                <header class="mb-3 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ section.title }}</h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:bg-white/10 dark:text-slate-300">
                        {{ section.total }} initiatives
                    </span>
                </header>

                <div v-if="section.columns.length > 0" class="space-y-3">
                    <article
                        v-for="column in section.columns"
                        :key="`board-column-${section.key}-${column.key}`"
                        class="rounded-xl border border-slate-300 bg-slate-50 p-2.5 dark:border-white/10 dark:bg-[#1d1d1d]"
                    >
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h4 class="text-[12px] font-bold uppercase tracking-tight text-[#132f66] dark:text-[#9ec8ff]">{{ column.title }}</h4>
                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                {{ column.items.length }} initiatives
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                            <div
                                v-for="initiative in column.items"
                                :key="`board-item-${initiative.id}`"
                                class="rounded-lg border px-2 py-1.5 text-center text-[10px] font-semibold leading-tight"
                                :class="initiativeBlockClass(initiative)"
                            >
                                {{ initiative.code ?? '-' }}. {{ initiative.name ?? '-' }}
                            </div>
                        </div>
                    </article>
                </div>
                <p v-else class="rounded-lg bg-slate-100 px-3 py-6 text-center text-xs text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    Tidak ada data untuk section ini.
                </p>
            </article>

            <section class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Legend</h4>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span
                        v-for="legend in statusLegend"
                        :key="`legend-${legend.key}`"
                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-semibold"
                        :class="legend.className"
                    >
                        {{ legend.label }}: {{ legend.count }}
                    </span>
                </div>
            </section>
        </section>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import InitiativeStatusTable from '@/Components/Dashboard/InitiativeStatusTable.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: 'Initiatives',
    },
    initialTipeFilter: {
        type: String,
        default: '',
    },
});

const viewMode = ref('table');
const blockGroupBy = ref('coe');

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();
const normalizeLabel = (value) => String(value ?? '').trim();
const slugify = (value) => normalizeLabel(value).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

const itemsWithInitialType = computed(() => {
    const baseItems = Array.isArray(props.items) ? props.items : [];
    if (!props.initialTipeFilter) {
        return baseItems;
    }

    return baseItems.filter((item) => String(item?.tipe_initiative ?? '') === String(props.initialTipeFilter));
});

const sortedItems = computed(() => {
    return [...itemsWithInitialType.value].sort((left, right) => {
        const leftCode = Number(left?.code ?? 0);
        const rightCode = Number(right?.code ?? 0);

        if (leftCode !== rightCode) {
            return leftCode - rightCode;
        }

        return Number(left?.id ?? 0) - Number(right?.id ?? 0);
    });
});

const typeLabel = computed(() => {
    const tipe = String(props.initialTipeFilter ?? '').trim();
    if (tipe === '1') return 'Digital Initiatives';
    if (tipe === '2') return 'IT Initiatives';
    return 'Initiatives';
});

const sectionKey = (item) => {
    const sourceName = String(item?.source_data?.name ?? item?.sourceData?.name ?? '').toLowerCase();
    const groupName = String(item?.organization?.groub?.name ?? '').toLowerCase();

    if (sourceName.includes('new')) {
        if (groupName.includes('sub')) {
            return 'new-subholding';
        }

        if (groupName.includes('holding')) {
            return 'new-holding';
        }

        return 'new';
    }

    if (groupName.includes('sub')) {
        return 'subholding';
    }

    if (groupName.includes('holding')) {
        return 'holding';
    }

    return 'new';
};

const sectionOrder = ['holding', 'subholding', 'new-holding', 'new-subholding', 'new'];
const sectionTitleMap = {
    holding: 'Holding',
    subholding: 'SubHolding',
    'new-holding': 'New Initiatives Holding',
    'new-subholding': 'New Initiatives SubHolding',
    new: 'New Initiatives',
};

const organizationLabel = (initiative) => {
    const organizationName = normalizeLabel(initiative?.organization?.name);
    const groubName = normalizeLabel(initiative?.organization?.groub?.name);
    return [organizationName, groubName].filter(Boolean).join(' - ') || 'Unassigned';
};

const groupMeta = (initiative) => {
    if (blockGroupBy.value === 'organization') {
        const title = organizationLabel(initiative);
        const organizationId = Number(initiative?.organization?.id ?? initiative?.organization_id ?? 0);

        return {
            key: organizationId > 0 ? `org-${organizationId}` : `org-${slugify(title) || 'unassigned'}`,
            title,
            sortNumber: Number.MAX_SAFE_INTEGER,
            sortText: title.toLowerCase(),
        };
    }

    const coeId = Number(initiative?.coe?.id ?? initiative?.coe_id ?? 0);
    const title = normalizeLabel(initiative?.coe?.name) || 'Unassigned';

    return {
        key: coeId > 0 ? `coe-${coeId}` : `coe-${slugify(title) || 'unassigned'}`,
        title,
        sortNumber: coeId > 0 ? coeId : Number.MAX_SAFE_INTEGER,
        sortText: title.toLowerCase(),
    };
};

const boardSections = computed(() => {
    const sectionBuckets = sectionOrder.reduce((accumulator, key) => {
        accumulator[key] = [];
        return accumulator;
    }, {});

    for (const item of sortedItems.value) {
        const key = sectionKey(item);
        if (!sectionBuckets[key]) {
            sectionBuckets[key] = [];
        }
        sectionBuckets[key].push(item);
    }

    return sectionOrder
        .map((key) => {
            const initiatives = sectionBuckets[key] ?? [];
            const columnMap = new Map();

            for (const initiative of initiatives) {
                const meta = groupMeta(initiative);
                if (!columnMap.has(meta.key)) {
                    columnMap.set(meta.key, {
                        key: meta.key,
                        title: meta.title,
                        sortNumber: meta.sortNumber,
                        sortText: meta.sortText,
                        items: [],
                    });
                }
                columnMap.get(meta.key).items.push(initiative);
            }

            const columns = Array.from(columnMap.values()).sort((left, right) => {
                if (blockGroupBy.value === 'coe' && left.sortNumber !== right.sortNumber) {
                    return left.sortNumber - right.sortNumber;
                }

                return left.sortText.localeCompare(right.sortText);
            });

            return {
                key,
                title: `${sectionTitleMap[key]} ${typeLabel.value}`,
                columns,
                total: initiatives.length,
            };
        })
        .filter((section) => section.total > 0);
});

const statusClassMap = {
    drafting: 'border-[#D97706] bg-[#F59E0B] text-white',
    propose: 'border-[#0EA5E9] bg-[#0284C7] text-white',
    review: 'border-[#DC2626] bg-[#F87171] text-white',
    approved: 'border-[#22C55E] bg-[#BBF7D0] text-[#0f5132]',
    postpone: 'border-[#EAB308] bg-[#FEF08A] text-[#7a6000]',
};

const defaultBlockClass = 'border-[#0284C7] bg-[#0284C7] text-white';

const initiativeBlockClass = (initiative) => {
    const status = normalizeStatus(initiative?.latest_status?.status);
    return statusClassMap[status] ?? defaultBlockClass;
};

const statusLegend = computed(() => {
    const order = ['drafting', 'propose', 'review', 'approved', 'postpone'];
    const labels = {
        drafting: 'Drafting',
        propose: 'Propose',
        review: 'Review',
        approved: 'Approved',
        postpone: 'Postpone',
    };

    const counts = order.reduce((accumulator, key) => {
        accumulator[key] = 0;
        return accumulator;
    }, {});

    for (const item of sortedItems.value) {
        const key = normalizeStatus(item?.latest_status?.status);
        if (counts[key] !== undefined) {
            counts[key] += 1;
        }
    }

    return order.map((key) => ({
        key,
        label: labels[key],
        count: counts[key],
        className: statusClassMap[key] ?? defaultBlockClass,
    }));
});
</script>
