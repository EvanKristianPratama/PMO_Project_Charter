<template>
    <UserLayout title="Review Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Review Dashboard</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <label class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Sort Bulan Proses
                        </label>
                        <select
                            v-model="sortOrder"
                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                        >
                            <option value="desc">Terlama ke Tercepat</option>
                            <option value="asc">Tercepat ke Terlama</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        Total Initiative
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                        {{ summary.total }}
                    </p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        Building Block
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                        {{ summary.buildingBlock }}
                    </p>
                </article>

                <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm dark:border-emerald-500/20 dark:bg-[#171717]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-emerald-600 dark:text-emerald-300">
                        Sudah Ada Status Review
                    </p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-300">
                        {{ summary.withReviewStatus }}
                    </p>
                </article>

                <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm dark:border-amber-500/20 dark:bg-[#171717]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-amber-600 dark:text-amber-300">
                        Belum Ada Status Review
                    </p>
                    <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-300">
                        {{ summary.withoutReviewStatus }}
                    </p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Statistik Status Review</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700 dark:text-slate-200">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-[0.06em] text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Jumlah</th>
                                <th class="px-4 py-3 font-semibold">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in statusBreakdown"
                                :key="`status-row-${item.status}`"
                                class="border-t border-slate-100 dark:border-white/10"
                            >
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold" :class="statusBadgeClass(item.status)">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ item.count }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.percentage }}%
                                </td>
                            </tr>
                            <tr v-if="statusBreakdown.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data status review.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <InitiativesTimelineTable :items="sortedRows" />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import InitiativesTimelineTable from '@/Components/ProgramEvaluation/ReviewDashboard/InitiativesTimelineTable.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({
            total: 0,
            buildingBlock: 0,
            withReviewStatus: 0,
            withoutReviewStatus: 0,
            statusBreakdown: [],
        }),
    },
});

const sortOrder = ref('desc');

const sortedRows = computed(() => {
    const rows = Array.isArray(props.rows) ? [...props.rows] : [];

    return rows.sort((left, right) => {
        const leftValue = Number.isFinite(Number(left?.process_month_value)) ? Number(left.process_month_value) : null;
        const rightValue = Number.isFinite(Number(right?.process_month_value)) ? Number(right.process_month_value) : null;

        if (leftValue === null && rightValue === null) {
            return Number(left?.no ?? 0) - Number(right?.no ?? 0);
        }

        if (leftValue === null) {
            return 1;
        }

        if (rightValue === null) {
            return -1;
        }

        if (leftValue === rightValue) {
            return Number(left?.no ?? 0) - Number(right?.no ?? 0);
        }

        return sortOrder.value === 'asc'
            ? leftValue - rightValue
            : rightValue - leftValue;
    });
});

const statusBreakdown = computed(() => (
    Array.isArray(props.summary?.statusBreakdown) ? props.summary.statusBreakdown : []
));

const statusBadgeClass = (status) => ({
    'On Track': 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300',
    'At Risk': 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
    'Delayed': 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
    'Done': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    'On Progress': 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
    'On Review': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-300',
    'Not Started': 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
    'Not Signed': 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    'Belum Ada Status': 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400',
}[status] ?? 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300');
</script>
