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
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
                            Building Block: {{ summary.buildingBlock }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-200">
                            Total: {{ summary.total }}
                        </span>
                    </div>
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
</script>