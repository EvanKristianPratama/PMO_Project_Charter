<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-[#E42313] bg-[#E42313] p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Program <span class="text-yellow-400">Planning</span>
                            Summary</h1>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article v-for="item in metricCards" :key="item.key"
                    class="relative flex flex-col rounded-2xl border p-5 shadow-sm"
                    :class="item.cardClass || 'bg-white border-slate-200 dark:border-white/10 dark:bg-[#171717]'">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em]"
                        :class="item.labelClass || 'text-slate-500 dark:text-slate-400'"
                        :style="item.textShadow ? { textShadow: '0 1px 3px rgba(0,0,0,0.3)' } : {}">
                        {{ item.label }}
                    </p>
                    <p class="mt-2 flex items-center justify-between text-3xl font-bold"
                        :class="item.textClass || 'text-slate-900 dark:text-white'"
                        :style="item.textShadow ? { textShadow: '0 2px 6px rgba(0,0,0,0.35)' } : {}">
                        <span>{{ item.value }}</span>
                        <button v-if="item.actionMethod"
                            class="text-sm font-semibold opacity-80 transition-colors hover:opacity-100"
                            :class="item.labelClass" @click="item.actionMethod">
                            {{ item.actionLabel }}
                        </button>
                    </p>
                    <p v-if="item.note" class="mt-2 flex-1 text-xs"
                        :class="item.labelClass || 'text-slate-500 dark:text-slate-400'">{{ item.note }}</p>
                </article>
            </section>

            <ScopeCharterFlowSection :digital-steps="digitalStatusFlow" :it-steps="itStatusFlow" />

            <!-- Initiative Status Summary -->
            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Initiative Status Summary</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Initiative</th>
                                <th v-for="column in statusSummaryColumns" :key="`status-summary-head-${column.key}`"
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    {{ column.label }}
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="row in statusSummaryRows" :key="row.key"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td role="button" tabindex="0"
                                    class="cursor-pointer px-4 py-3 font-semibold text-slate-900 dark:text-white"
                                    :class="selectedInitiative === row.key && selectedStatusFilter === null ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, null)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, null)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, null)">
                                    <div class="flex items-center gap-2">
                                        <span>{{ row.label }}</span>
                                        <span
                                            class="text-[10px] uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                            {{ selectedInitiative === row.key && selectedStatusFilter === null ? 'Hide'
                                                : 'Show All' }}
                                        </span>
                                    </div>
                                </td>
                                <td v-for="column in statusSummaryColumns"
                                    :key="`status-summary-cell-${row.key}-${column.key}`" role="button" tabindex="0"
                                    class="cursor-pointer px-4 py-3 text-right font-semibold text-slate-800 transition-colors hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-white/10"
                                    :class="selectedInitiative === row.key && String(selectedStatusFilter) === String(column.key) ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, column.key)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, column.key)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, column.key)">
                                    {{ row.counts[column.key] }}
                                </td>
                                <td role="button" tabindex="0"
                                    class="cursor-pointer px-4 py-3 text-right font-bold text-slate-900 transition-colors hover:bg-slate-100 dark:text-white dark:hover:bg-white/10"
                                    :class="selectedInitiative === row.key && selectedStatusFilter === null ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, null)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, null)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, null)">
                                    {{ row.total }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Initiative View -->
            <section class="grid grid-cols-1 gap-5">
                <article v-if="selectedInitiative === null"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center shadow-sm dark:border-white/15 dark:bg-[#171717]">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Klik baris <span class="font-semibold">Digital Initiative</span> atau <span
                            class="font-semibold">IT Initiative</span> untuk menampilkan tabel detail.
                    </p>
                </article>

                <MstInitiativeSwitcher
                    v-else
                    :items="filteredTableItems"
                    :title="selectedInitiative === 'digital' ? 'Digital Initiatives' : selectedInitiative === 'it' ? 'IT Initiatives' : 'All Initiatives'"
                    :initial-tipe-filter="selectedInitiative === 'digital' ? '1' : selectedInitiative === 'it' ? '2' : ''"
                />
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ScopeCharterFlowSection from '@/Components/Dashboard/ScopeCharterFlowSection.vue';
import MstInitiativeSwitcher from '@/Components/Dashboard/MstInitiativeSwitcher.vue';
import { statusFlowClassByIndex } from '@/Composables/initiativeStatus';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_it_initiatives: 0,
            total_digital_initiatives: 0,
            total_all_initiatives: 0,
            status_options: [],
            it_status_counts: {},
            digital_status_counts: {},
        }),
    },
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
    categoryOptions: {
        type: Array,
        default: () => [],
    },
});

const normalizeStatusName = (value) => String(value ?? '').trim().toLowerCase();
const resolvePlanningStatusKey = (item) => {
    const projectStatusKey = normalizeStatusName(item?.project_status_key);
    return projectStatusKey || 'not_start';
};

const statusSummaryColumns = computed(() => {
    return [
        { key: 'not_start', label: 'Not Start' },
        { key: 'drafting', label: 'Drafting' },
        { key: 'propose',  label: 'Propose' },
        { key: 'review',   label: 'Review' },
        { key: 'baseline', label: 'Baseline' },
        { key: 'approved', label: 'Approved' },
    ];
});

const statusSummaryRows = computed(() => {
    const columns = statusSummaryColumns.value;

    const buildRow = (rowKey, label, counts) => {
        let total = 0;
        const normalizedCounts = columns.reduce((accumulator, column) => {
            const value = Number(counts?.[column.key] ?? 0);
            accumulator[column.key] = value;
            total += value;

            return accumulator;
        }, {});

        return {
            key: rowKey,
            label,
            counts: normalizedCounts,
            total,
        };
    };

    return [
        buildRow('digital', 'Digital Initiative', props.summary?.digital_status_counts || {}),
        buildRow('it', 'IT Initiative', props.summary?.it_status_counts || {}),
    ];
});

const selectedInitiative = ref(null);
const selectedStatusFilter = ref(null);

const toggleInitiativeTable = (initiativeKey, statusId = null) => {
    if (selectedInitiative.value === initiativeKey && selectedStatusFilter.value === statusId) {
        selectedInitiative.value = null;
        selectedStatusFilter.value = null;
    } else {
        selectedInitiative.value = initiativeKey;
        selectedStatusFilter.value = statusId;
    }
};

// Filter mstInitiatives by tipe and optionally by status
const filteredTableItems = computed(() => {
    let items = props.mstInitiatives || [];

    // Filter by tipe_initiative based on selected row
    if (selectedInitiative.value === 'digital') {
        items = items.filter((i) => Number(i.tipe_initiative) === 1);
    } else if (selectedInitiative.value === 'it') {
        items = items.filter((i) => Number(i.tipe_initiative) === 2);
    }

    // Filter by status if a column was clicked
    if (selectedStatusFilter.value === '__all__') {
        // Metric card "Show" click — show ALL items (with or without status)
        // No additional filtering
    } else if (selectedStatusFilter.value) {
        items = items.filter((i) => {
            return resolvePlanningStatusKey(i) === selectedStatusFilter.value;
        });
    } else {
        items = items.filter((i) => resolvePlanningStatusKey(i).length > 0);
    }

    return items;
});

const totalDigitalOverall = computed(() => Number(props.summary?.total_digital_initiatives ?? 0));

const totalItOverall = computed(() => Number(props.summary?.total_it_initiatives ?? 0));

const showAllDigitalInitiatives = () => {
    if (selectedInitiative.value === 'digital' && selectedStatusFilter.value === '__all__') {
        selectedInitiative.value = null;
        selectedStatusFilter.value = null;
    } else {
        selectedInitiative.value = 'digital';
        selectedStatusFilter.value = '__all__';

        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const showAllItInitiatives = () => {
    if (selectedInitiative.value === 'it' && selectedStatusFilter.value === '__all__') {
        selectedInitiative.value = null;
        selectedStatusFilter.value = null;
    } else {
        selectedInitiative.value = 'it';
        selectedStatusFilter.value = '__all__';

        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const metricCards = computed(() => [
    {
        key: 'digital-total',
        label: 'Total Usulan Digital Inisiatif',
        value: totalDigitalOverall.value,
        actionLabel: selectedInitiative.value === 'digital' && selectedStatusFilter.value === '__all__' ? 'Hide' : 'Show',
        actionMethod: showAllDigitalInitiatives,
        cardClass: 'bg-[#1C75BC] border-[#1C75BC] shadow-[0_4px_16px_rgba(28,117,188,0.3)]',
        textClass: 'text-white',
        labelClass: 'text-white',
        textShadow: true,
    },
    {
        key: 'it-total',
        label: 'Total Usulan IT Inisiatif',
        value: totalItOverall.value,
        actionLabel: selectedInitiative.value === 'it' && selectedStatusFilter.value === '__all__' ? 'Hide' : 'Show',
        actionMethod: showAllItInitiatives,
        cardClass: 'bg-[#A7C942] border-[#A7C942] shadow-[0_4px_16px_rgba(167,201,66,0.3)]',
        textClass: 'text-white',
        labelClass: 'text-white',
        textShadow: true,
    },
]);

const mapFlowData = (counts = {}) => {
    const flowSteps = [
        { key: 'not_start', label: 'Not Start' },
        { key: 'drafting',  label: 'Drafting' },
        { key: 'propose',   label: 'Propose' },
        { key: 'review',    label: 'Review' },
        { key: 'baseline',  label: 'Baseline' },
        { key: 'approved',  label: 'Approved' },
    ];

    return flowSteps.map((step, index) => {
        const flowClass = statusFlowClassByIndex(index);
        return {
            key: step.key,
            label: step.label,
            count: Number(counts?.[step.key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
};

const itStatusFlow = computed(() => mapFlowData(props.summary?.it_status_counts || {}));
const digitalStatusFlow = computed(() => mapFlowData(props.summary?.digital_status_counts || {}));
</script>
