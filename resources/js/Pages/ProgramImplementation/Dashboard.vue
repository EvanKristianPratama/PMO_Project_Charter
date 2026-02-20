<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Program Implementation Summary</h1>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="item in metricCards"
                    :key="item.key"
                    class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">{{ item.label }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ item.value }}</p>
                    <p class="mt-2 flex-1 text-xs text-slate-500 dark:text-slate-400">{{ item.note }}</p>
                </article>
            </section>

            <ScopeCharterFlowSection
                :digital-steps="digitalStatusFlow"
                :it-steps="itStatusFlow"
                :legend="statusFlowLegend"
            />
            <!-- Scope Charter Status Summary -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Scope Charter Status Summary</h2>
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
                                role="button"
                                tabindex="0"
                                class="cursor-pointer transition-colors"
                                :class="selectedInitiative === row.key
                                    ? 'bg-blue-50/70 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                                @click="toggleInitiativeTable(row.key)"
                                @keydown.enter.prevent="toggleInitiativeTable(row.key)"
                                @keydown.space.prevent="toggleInitiativeTable(row.key)"
                            >
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span>{{ row.label }}</span>
                                        <span class="text-[10px] uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                            {{ selectedInitiative === row.key ? 'Hide' : 'Show' }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    v-for="column in statusSummaryColumns"
                                    :key="`status-summary-cell-${row.key}-${column.key}`"
                                    class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100"
                                >
                                    {{ row.counts[column.key] }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white">{{ row.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Scope Charter Tables -->
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
                    :items="openDigitalInitiatives"
                    :completed-status-id="completedStatusId"
                    :completed-status-label="completedStatusLabel"
                    :status-options="statusOptions"
                />

                <ScopeCharterItTable
                    v-else-if="selectedInitiative === 'it'"
                    :items="openItInitiatives"
                    :completed-status-id="completedStatusId"
                    :completed-status-label="completedStatusLabel"
                    :status-options="statusOptions"
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
            status_options: [],
            status_counts: {},
            digital_status_counts: {},
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

const fallbackStatusOptions = [
    { id: 1, name: 'drafting', label: 'Drafting' },
    { id: 2, name: 'propose', label: 'Propose' },
    { id: 3, name: 'review', label: 'Review' },
    { id: 4, name: 'approve', label: 'Approve' },
];

const statusOptions = computed(() => {
    return Array.isArray(props.overview?.status_options) && props.overview.status_options.length > 0
        ? props.overview.status_options
        : fallbackStatusOptions;
});

const scopeStatusOrder = ['drafting', 'propose', 'review', 'approve'];

const normalizeStatusName = (value) => String(value ?? '').trim().toLowerCase();

const scopeStatusOptions = computed(() => {
    const sourceOptions = Array.isArray(statusOptions.value) ? statusOptions.value : [];
    const mappedStatusByName = new Map();

    sourceOptions.forEach((status) => {
        const candidateNames = [normalizeStatusName(status?.name), normalizeStatusName(status?.label)];

        if (candidateNames.includes('baseline')) {
            return;
        }

        candidateNames.forEach((candidateName) => {
            if (scopeStatusOrder.includes(candidateName) && !mappedStatusByName.has(candidateName)) {
                mappedStatusByName.set(candidateName, status);
            }
        });
    });

    return scopeStatusOrder.map((statusName, index) => {
        const matchedStatus = mappedStatusByName.get(statusName);
        const fallbackStatus = fallbackStatusOptions[index];

        return {
            id: Number(matchedStatus?.id ?? fallbackStatus.id),
            name: statusName,
            label: fallbackStatus.label,
        };
    });
});

const completedStatusId = computed(() => Number(props.completedStatusId || 5));

const completedStatusLabel = computed(() => {
    return statusLabelFromOptions(completedStatusId.value, statusOptions.value);
});

const statusFlowLegend = computed(() => {
    return scopeStatusOptions.value
        .map((status) => status.label)
        .join(' → ');
});

const statusSummaryColumns = computed(() => {
    return scopeStatusOptions.value.map((status) => ({
        key: String(status.id),
        label: status.label,
    }));
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
        buildRow('digital', 'Digital initiative', props.overview?.digital_status_counts || {}),
        buildRow('it', 'IT Initiative', props.overview?.status_counts || {}),
    ];
});

const selectedInitiative = ref(null);

const toggleInitiativeTable = (initiativeKey) => {
    selectedInitiative.value = selectedInitiative.value === initiativeKey ? null : initiativeKey;
};

const metricCards = computed(() => [
    {
        key: 'digital',
        label: 'Total Usulan Digital Initiatives',
        value: props.overview.total_digital_initiatives,
        createHref: '/digital-initiatives/create',
    },
    {
        key: 'it',
        label: 'Total Usulan IT Initiatives',
        value: props.overview.total_projects,
        createHref: '/it-initiatives/create',
    },
 ]);

const mapFlowData = (counts = {}) => {
    return scopeStatusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = String(status.id);

        return {
            key,
            label: status.label,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
};

const itStatusFlow = computed(() => mapFlowData(props.overview?.status_counts || {}));
const digitalStatusFlow = computed(() => mapFlowData(props.overview?.digital_status_counts || {}));
</script>
