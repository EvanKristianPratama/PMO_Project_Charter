<template>
    <UserLayout title="Dashboard Monitoring">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Program Planing Monitoring</h1>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total Digital Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_digital_initiatives }}</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total IT Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_it_initiatives }}</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total Semua Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_all_initiatives }}</p>
                </article>
            </section>

            <ScopeCharterFlowSection
                :digital-steps="digitalStatusFlow"
                :it-steps="itStatusFlow"
                :legend="statusFlowLegend"
            />

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Scope Charter Status Summary</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Timeline Scope Charter Status </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Scope Charter Digital</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Scope Charter IT</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total All Scope Charter</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="row in statusRows" :key="row.key">
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(row.id)">
                                        {{ row.label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.digital }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.it }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ row.total }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Jumlah</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.digital }}</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.it }}</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.total }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ScopeCharterFlowSection from '@/Components/Dashboard/ScopeCharterFlowSection.vue';
import { statusBadgeClassById, statusFlowClassByIndex, statusLabelFromOptions } from '@/Composables/initiativeStatus';

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
            combined_status_counts: {},
            status_rows: [],
        }),
    },
});

const fallbackStatusOptions = [
    { id: 1, name: 'drafting', label: 'Drafting' },
    { id: 2, name: 'propose', label: 'Propose' },
    { id: 3, name: 'review', label: 'Review' },
    { id: 4, name: 'approve', label: 'Approve' },
    { id: 5, name: 'baseline', label: 'Baseline' },
];

const statusOptions = computed(() => {
    return Array.isArray(props.summary?.status_options) && props.summary.status_options.length > 0
        ? props.summary.status_options
        : fallbackStatusOptions;
});

// ── Scope flow: only show 4 statuses (exclude Baseline) ──────────────────────
const scopeStatusOrder = ['drafting', 'propose', 'review', 'approve'];

const normalizeStatusName = (value) => String(value ?? '').trim().toLowerCase();

const scopeStatusOptions = computed(() => {
    const sourceOptions = Array.isArray(statusOptions.value) ? statusOptions.value : [];
    const mappedStatusByName = new Map();

    sourceOptions.forEach((status) => {
        const candidateNames = [normalizeStatusName(status?.name), normalizeStatusName(status?.label)];

        if (candidateNames.includes('baseline')) return;

        candidateNames.forEach((candidateName) => {
            if (scopeStatusOrder.includes(candidateName) && !mappedStatusByName.has(candidateName)) {
                mappedStatusByName.set(candidateName, status);
            }
        });
    });

    return scopeStatusOrder.map((statusName, index) => {
        const matchedStatus = mappedStatusByName.get(statusName);
        const fallback = fallbackStatusOptions[index];

        return {
            id: Number(matchedStatus?.id ?? fallback.id),
            name: statusName,
            label: fallback.label,
        };
    });
});

const statusFlowLegend = computed(() =>
    scopeStatusOptions.value.map((s) => s.label).join(' → ')
);

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

const digitalStatusFlow = computed(() => mapFlowData(props.summary?.digital_status_counts || {}));
const itStatusFlow      = computed(() => mapFlowData(props.summary?.it_status_counts || {}));

// ── Status summary table ──────────────────────────────────────────────────────
const statusRows = computed(() => {
    if (Array.isArray(props.summary?.status_rows) && props.summary.status_rows.length > 0) {
        return props.summary.status_rows.map((row) => ({
            key: String(row.id),
            id: Number(row.id),
            label: row.label || statusLabelFromOptions(row.id, statusOptions.value),
            it: Number(row.it ?? 0),
            digital: Number(row.digital ?? 0),
            total: Number(row.total ?? 0),
        }));
    }

    return statusOptions.value.map((status) => {
        const key = String(status.id);
        const itCount      = Number(props.summary.it_status_counts?.[key] ?? 0);
        const digitalCount = Number(props.summary.digital_status_counts?.[key] ?? 0);

        return {
            key,
            id: Number(status.id),
            label: statusLabelFromOptions(status.id, statusOptions.value),
            it: itCount,
            digital: digitalCount,
            total: itCount + digitalCount,
        };
    });
});

const statusTotals = computed(() => {
    return statusRows.value.reduce(
        (acc, row) => ({
            digital: acc.digital + Number(row.digital ?? 0),
            it: acc.it + Number(row.it ?? 0),
            total: acc.total + Number(row.total ?? 0),
        }),
        { digital: 0, it: 0, total: 0 }
    );
});
</script>
