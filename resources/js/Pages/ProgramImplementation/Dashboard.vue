<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-[#E42313] bg-[#E42313] p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Program <span class="text-green-400">Implementation</span> Summary</h1>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article
                    v-for="item in metricCards"
                    :key="item.key"
                    class="relative flex flex-col rounded-2xl border p-5 shadow-sm"
                    :class="item.cardClass || 'bg-white border-slate-200 dark:border-white/10 dark:bg-[#171717]'"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.08em]"
                        :class="item.labelClass || 'text-slate-500 dark:text-slate-400'"
                        :style="item.textShadow ? { textShadow: '0 1px 3px rgba(0,0,0,0.3)' } : {}"
                    >
                        {{ item.label }}
                    </p>
                    <p
                        class="mt-2 flex items-center justify-between text-3xl font-bold"
                        :class="item.textClass || 'text-slate-900 dark:text-white'"
                        :style="item.textShadow ? { textShadow: '0 2px 6px rgba(0,0,0,0.35)' } : {}"
                    >
                        <span>{{ item.value }}</span>
                        <button
                            v-if="item.actionMethod"
                            class="text-sm font-semibold opacity-80 transition-colors hover:opacity-100"
                            :class="item.labelClass"
                            @click="item.actionMethod"
                        >
                            {{ item.actionLabel }}
                        </button>
                    </p>
                    <p v-if="item.note" class="mt-2 flex-1 text-xs" :class="item.labelClass || 'text-slate-500 dark:text-slate-400'">{{ item.note }}</p>
                </article>
            </section>

            <ScopeCharterFlowSection
                :digital-steps="digitalStatusFlow"
                :it-steps="itStatusFlow"
                charter-label="Project Charter"
                timeline-phase="Implementation"
            />
            <!-- Project Charter Status Summary -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Project Charter Status Summary</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Initiative</th>
                                <th
                                    v-for="column in statusSummaryColumns"
                                    :key="`status-summary-head-${column.key}`"
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    {{ column.label }}
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr
                                v-for="row in statusSummaryRows"
                                :key="row.key"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                            >
                                <td
                                    role="button"
                                    tabindex="0"
                                    class="cursor-pointer px-4 py-3 font-semibold text-slate-900 dark:text-white"
                                    :class="selectedInitiative === row.key && selectedStatusFilter === null ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, null)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, null)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, null)"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>{{ row.label }}</span>
                                        <span class="text-[10px] uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                            {{ selectedInitiative === row.key && selectedStatusFilter === null ? 'Hide' : 'Show All' }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    v-for="column in statusSummaryColumns"
                                    :key="`status-summary-cell-${row.key}-${column.key}`"
                                    role="button"
                                    tabindex="0"
                                    class="cursor-pointer px-4 py-3 text-right font-semibold text-slate-800 transition-colors hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-white/10"
                                    :class="selectedInitiative === row.key && String(selectedStatusFilter) === String(column.key) ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, column.key)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, column.key)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, column.key)"
                                >
                                    {{ row.counts[column.key] }}
                                </td>
                                <td
                                    role="button"
                                    tabindex="0"
                                    class="cursor-pointer px-4 py-3 text-right font-bold text-slate-900 transition-colors hover:bg-slate-100 dark:text-white dark:hover:bg-white/10"
                                    :class="selectedInitiative === row.key && selectedStatusFilter === null ? 'bg-blue-50/70 dark:bg-blue-500/10' : ''"
                                    @click="toggleInitiativeTable(row.key, null)"
                                    @keydown.enter.prevent="toggleInitiativeTable(row.key, null)"
                                    @keydown.space.prevent="toggleInitiativeTable(row.key, null)"
                                >
                                    {{ row.total }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Project Charter Tables -->
            <section class="grid grid-cols-1 gap-5">
                <article
                    v-if="selectedInitiative === null"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center shadow-sm dark:border-white/15 dark:bg-[#171717]"
                >
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Klik baris <span class="font-semibold">Digital initiative</span> atau <span class="font-semibold">IT Initiative</span> untuk menampilkan tabel detail.
                    </p>
                </article>

                <ScopeCharterDigitalTable
                    v-else-if="selectedInitiative === 'digital'"
                    :items="filteredDigitalInitiatives"
                    :completed-status-id="completedStatusId"
                    :completed-status-label="completedStatusLabel"
                    :status-options="statusOptions"
                    charter-label="Project Charter"
                />

                <ScopeCharterItTable
                    v-else-if="selectedInitiative === 'it'"
                    :items="filteredItInitiatives"
                    :completed-status-id="completedStatusId"
                    :completed-status-label="completedStatusLabel"
                    :status-options="statusOptions"
                    charter-label="Project Charter"
                />
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ScopeCharterFlowSection from '@/Components/Dashboard/ScopeCharterFlowSection.vue';
import ScopeCharterDigitalTable from '@/Components/Dashboard/ScopeCharterDigitalTable.vue';
import ScopeCharterItTable from '@/Components/Dashboard/ScopeCharterItTable.vue';
import { statusFlowClassByIndex, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    overview: {
        type: Object,
        default: () => ({
            total_projects: 0,
            total_digital_initiatives: 0,
            total_it_initiatives: 0,
            status_options: [],
            it_status_counts: {},
            digital_status_counts: {},
            total_digital_approved: 0,
            total_it_approved: 0,
        }),
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
    openDigitalInitiatives: {
        type: Array,
        default: () => [],
    },
    openItInitiatives: {
        type: Array,
        default: () => [],
    },
});

const FLOW_NOT_YET_ID = 0;
const fallbackStatusOptions = [
    { id: FLOW_NOT_YET_ID, name: 'not_yet', label: 'Not yet' },
    { id: 1, name: 'drafting', label: 'Drafting' },
    { id: 2, name: 'propose', label: 'Propose' },
    { id: 3, name: 'review', label: 'Review' },
    { id: 5, name: 'baseline', label: 'Baseline' },
    { id: 4, name: 'approved', label: 'Approved' },
];

const normalizeProjectStatusId = (value) => {
    if (value === null || value === '') {
        return FLOW_NOT_YET_ID;
    }

    const parsed = Number(value);
    return Number.isInteger(parsed) && parsed > 0 ? parsed : null;
};

const statusOptions = computed(() => {
    const sourceOptions = Array.isArray(props.overview?.status_options) ? props.overview.status_options : [];
    const sourceById = new Map(
        sourceOptions
            .map((status) => [Number(status?.id), status])
            .filter(([id]) => Number.isInteger(id))
    );

    return fallbackStatusOptions.map((fallback) => {
        const matched = sourceById.get(fallback.id);

        return {
            id: fallback.id,
            name: fallback.name,
            label: fallback.label,
            rawName: matched?.name ?? fallback.name,
        };
    });
});

const scopeStatusOptions = computed(() => {
    return statusOptions.value;
});

const completedStatusId = computed(() => Number(props.completedStatusId || 5));

const completedStatusLabel = computed(() => {
    return statusLabelFromOptions(completedStatusId.value, statusOptions.value);
});

const approvedStatusId = computed(() => {
    const approvedOption = scopeStatusOptions.value.find((status) => status.name === 'approved');
    return Number(approvedOption?.id ?? 4);
});

const statusSummaryColumns = computed(() => {
    return scopeStatusOptions.value.map((status) => ({
        key: status.id,
        countKey: status.name,
        label: status.label,
    }));
});

const statusSummaryRows = computed(() => {
    const columns = statusSummaryColumns.value;

    const buildRow = (rowKey, label, counts) => {
        let total = 0;
        const normalizedCounts = columns.reduce((accumulator, column) => {
            const value = Number(counts?.[column.countKey] ?? 0);
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
        buildRow('digital', 'Digital initiative', props.overview?.digital_status_counts || {}),
        buildRow('it', 'IT Initiative', props.overview?.it_status_counts || {}),
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

const filteredDigitalInitiatives = computed(() => {
    if (selectedStatusFilter.value === null) return props.openDigitalInitiatives;

    const selectedStatusId = Number(selectedStatusFilter.value);

    return props.openDigitalInitiatives.filter((item) => {
        const candidates = [
            item?.status,
            item?.status_id,
            item?.statusRef?.id,
        ];

        if (Array.isArray(item?.statuses)) {
            item.statuses.forEach((statusItem) => {
                candidates.push(statusItem?.id);
                candidates.push(statusItem?.phase_id);
            });
        }

        return candidates.some(
            (statusValue) => normalizeProjectStatusId(statusValue) === selectedStatusId
        );
    });
});

const filteredItInitiatives = computed(() => {
    if (selectedStatusFilter.value === null) return props.openItInitiatives;

    const selectedStatusId = Number(selectedStatusFilter.value);

    return props.openItInitiatives.filter((item) => {
        const candidates = [
            item?.status,
            item?.status_id,
            item?.statusRef?.id,
        ];

        return candidates.some(
            (statusValue) => normalizeProjectStatusId(statusValue) === selectedStatusId
        );
    });
});

const totalDigitalDisetujui = computed(() => {
    return Number(props.overview?.total_digital_approved ?? 0);
});

const totalItDisetujui = computed(() => {
    return Number(props.overview?.total_it_approved ?? 0);
});

const showApprovedDigitalInitiatives = () => {
    selectedInitiative.value = 'digital';
    selectedStatusFilter.value = approvedStatusId.value;

    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth',
    });
};

const showApprovedItInitiatives = () => {
    selectedInitiative.value = 'it';
    selectedStatusFilter.value = approvedStatusId.value;

    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth',
    });
};

const metricCards = computed(() => [
    {
        key: 'digital-approved',
        label: 'Total Digital Inisiatif Disetujui',
        value: totalDigitalDisetujui.value,
        actionLabel: 'Show',
        actionMethod: showApprovedDigitalInitiatives,
        cardClass: 'bg-[#1C75BC] border-[#1C75BC] shadow-[0_4px_16px_rgba(28,117,188,0.3)]',
        textClass: 'text-white',
        labelClass: 'text-white',
        textShadow: true,
    },
    {
        key: 'it-approved',
        label: 'Total IT Inisiatif Disetujui',
        value: totalItDisetujui.value,
        actionLabel: 'Show',
        actionMethod: showApprovedItInitiatives,
        cardClass: 'bg-[#A7C942] border-[#A7C942] shadow-[0_4px_16px_rgba(167,201,66,0.3)]',
        textClass: 'text-white',
        labelClass: 'text-white',
        textShadow: true,
    },
 ]);

const mapFlowData = (counts = {}) => {
    return scopeStatusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = status.name;

        return {
            key,
            label: status.label,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
};

const itStatusFlow = computed(() => mapFlowData(props.overview?.it_status_counts || {}));
const digitalStatusFlow = computed(() => mapFlowData(props.overview?.digital_status_counts || {}));
</script>
