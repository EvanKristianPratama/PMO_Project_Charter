<template>
    <UserLayout title="Project Status Approval">
        <div class="space-y-6 animate-fade-in-up">
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
            withProjectStatus: 0,
            withoutProjectStatus: 0,
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
