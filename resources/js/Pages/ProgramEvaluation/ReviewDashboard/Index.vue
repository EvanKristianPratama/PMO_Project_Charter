<template>
    <UserLayout title="Review Approval">
        <div class="space-y-6 animate-fade-in-up">
            <InitiativesTimelineTable :items="sortedRows" />

            <!-- Duration Statistics Table Component -->
            <DurationStatsTable
                v-if="durationStats"
                :stats="durationStats.stats"
                :totalApproved="durationStats.totalApproved"
                :totalNotApproved="durationStats.totalNotApproved"
                :notApprovedInitiatives="durationStats.notApprovedInitiatives"
                :getCircleColor="getCircleColor"
            />

            <!-- Project Owner Breakdown Table Component -->
            <OwnerBreakdownTable
                :data="ownerBreakdown"
                :getCircleColor="getCircleColor"
            />

            <!-- Project Leader Breakdown Table Component -->
            <LeaderBreakdownTable
                :data="leaderBreakdown"
                :getCircleColor="getCircleColor"
            />

            <!-- Footer Note -->
            <footer class="mt-12 mb-8 flex justify-center border-t border-slate-100 pt-8 dark:border-white/5">
                <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                    TBD dengan IEDCC
                </div>
            </footer>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import InitiativesTimelineTable from '@/Components/ProgramEvaluation/ReviewDashboard/InitiativesTimelineTable.vue';
import DurationStatsTable from '@/Components/ProgramEvaluation/ReviewDashboard/DurationStatsTable.vue';
import OwnerBreakdownTable from '@/Components/ProgramEvaluation/ReviewDashboard/OwnerBreakdownTable.vue';
import LeaderBreakdownTable from '@/Components/ProgramEvaluation/ReviewDashboard/LeaderBreakdownTable.vue';
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

const durationStats = computed(() => {
    // Separate approved and not approved rows
    const approvedRows = props.rows.filter(
        (row) =>
            row.process_month_value !== null &&
            row.process_month_value !== undefined &&
            row.process_month_value !== "",
    );
    const notApprovedRows = props.rows.filter(
        (row) =>
            row.process_month_value === null ||
            row.process_month_value === undefined ||
            row.process_month_value === "",
    );

    const durations = approvedRows
        .map((row) => Number(row.process_month_value))
        .filter((val) => Number.isFinite(val))
        .sort((a, b) => a - b);

    if (durations.length === 0) {
        return {
            totalApproved: 0,
            totalNotApproved: notApprovedRows.length,
            notApprovedInitiatives: notApprovedRows
                .map((r) => ({ no: r.no, status: r.latest_review_status })),
            stats: [],
        };
    }

    const min = durations[0];
    const max = durations[durations.length - 1];

    // Median (P50)
    const mid = Math.floor(durations.length / 2);
    const p50 =
        durations.length % 2 !== 0
            ? durations[mid]
            : Math.round(((durations[mid - 1] + durations[mid]) / 2) * 10) / 10;

    // P90
    const p90Index = Math.ceil(0.9 * durations.length) - 1;
    const p90 = durations[p90Index];

    // Modus (Mode)
    const counts = {};
    let maxCount = 0;
    let mode = null;
    durations.forEach((d) => {
        counts[d] = (counts[d] || 0) + 1;
        if (counts[d] > maxCount) {
            maxCount = counts[d];
            mode = d;
        }
    });

    // Get initiative data and count for each stat
    const getInitiativeData = (val) => {
        const matches = approvedRows.filter((r) => Number(r.process_month_value) === val);
        return {
            count: matches.length,
            items: matches.map((r) => ({
                no: r.no,
                status: r.latest_review_status,
            })),
        };
    };

    const minData = getInitiativeData(min);
    const maxData = getInitiativeData(max);
    const modeData = getInitiativeData(mode);

    return {
        totalApproved: approvedRows.length,
        totalNotApproved: notApprovedRows.length,
        notApprovedInitiatives: notApprovedRows.map((r) => ({
            no: r.no,
            status: r.latest_review_status,
        })),
        stats: [
            {
                label: "min",
                desc: "durasi aproval tercepat",
                bulan: min,
                jumlah: minData.count,
                initiatives: minData.items,
            },
            {
                label: "max",
                desc: "durasi aproval terlama",
                bulan: max,
                jumlah: maxData.count,
                initiatives: maxData.items,
            },
            {
                label: "median",
                desc: "rata rata",
                bulan: p50,
                jumlah: approvedRows.length,
                customText: `Dari total ${approvedRows.length} inisiatif yang telah approve`,
                initiatives: [],
            },
            {
                label: "modus",
                desc: "mayoritas durasi aproval",
                bulan: mode,
                jumlah: modeData.count,
                initiatives: modeData.items,
            },
        ],
    };
});

const leaderBreakdown = computed(() => {
    const breakdown = {};

    props.rows.forEach((row) => {
        const leader = row.project_leader || "Unknown Leader";
        if (!breakdown[leader]) {
            breakdown[leader] = {
                leader,
                approved: [],
                notApproved: [],
            };
        }

        const isApproved =
            row.process_month_value !== null &&
            row.process_month_value !== undefined &&
            row.process_month_value !== "";

        const data = {
            no: row.no,
            status: row.latest_review_status,
        };

        if (isApproved) {
            breakdown[leader].approved.push(data);
        } else {
            breakdown[leader].notApproved.push(data);
        }
    });

    return Object.values(breakdown).sort((a, b) => a.leader.localeCompare(b.leader));
});

const ownerBreakdown = computed(() => {
    const breakdown = {};

    props.rows.forEach((row) => {
        const owner = row.project_owner || "Unknown Owner";
        if (!breakdown[owner]) {
            breakdown[owner] = {
                owner,
                approved: [],
                notApproved: [],
            };
        }

        const isApproved =
            row.process_month_value !== null &&
            row.process_month_value !== undefined &&
            row.process_month_value !== "";

        const data = {
            no: row.no,
            status: row.latest_review_status,
        };

        if (isApproved) {
            breakdown[owner].approved.push(data);
        } else {
            breakdown[owner].notApproved.push(data);
        }
    });

    return Object.values(breakdown).sort((a, b) => a.owner.localeCompare(b.owner));
});

const getCircleColor = (status) => {
    const s = String(status ?? "").trim().toLowerCase();
    if (["on track", "on-track"].includes(s)) return "bg-emerald-500";
    if (["at risk", "at-risk"].includes(s)) return "bg-orange-500";
    if (["delayed", "delay"].includes(s)) return "bg-orange-600";
    if (["done", "finished"].includes(s)) return "bg-emerald-500";
    if (["on progress", "on-progress", "progress"].includes(s)) return "bg-sky-500";
    if (["on review", "on-review", "review"].includes(s)) return "bg-yellow-500";
    if (["not started", "not-started"].includes(s)) return "bg-blue-500";
    if (["not signed", "not-signed"].includes(s)) return "bg-rose-500";
    if (["belum ada status", ""].includes(s)) return "bg-slate-300";

    return "bg-slate-400";
};

const statusBreakdown = computed(() =>
    Array.isArray(props.summary?.statusBreakdown) ? props.summary.statusBreakdown : [],
);

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
