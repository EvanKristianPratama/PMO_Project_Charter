<template>
    <UserLayout title="Review Summary">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Summary</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Rangkuman statistik dan breakdown inisiatif</p>
                    </div>
                </div>
            </section>

            <!-- Duration Statistics Table -->
            <DurationStatsTable
                v-if="durationStats"
                :stats="durationStats.stats"
                :totalApproved="durationStats.totalApproved"
                :totalNotApproved="durationStats.totalNotApproved"
                :notApprovedInitiatives="durationStats.notApprovedInitiatives"
                :getCircleColor="getCircleColor"
            />

            <!-- Project Owner Breakdown Table -->
            <OwnerBreakdownTable
                :data="ownerBreakdown"
                :getCircleColor="getCircleColor"
            />

            <!-- Project Leader Breakdown Table -->
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
import { computed } from 'vue';
import DurationStatsTable from '@/Components/ProgramEvaluation/ReviewDashboard/DurationStatsTable.vue';
import OwnerBreakdownTable from '@/Components/ProgramEvaluation/ReviewDashboard/OwnerBreakdownTable.vue';
import LeaderBreakdownTable from '@/Components/ProgramEvaluation/ReviewDashboard/LeaderBreakdownTable.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
});

const durationStats = computed(() => {
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

    if (approvedRows.length === 0) return null;

    const values = approvedRows.map((r) => Number(r.process_month_value));
    const minVal = Math.min(...values);
    const maxVal = Math.max(...values);
    const sumVal = values.reduce((a, b) => a + b, 0);
    const avgVal = (sumVal / approvedRows.length).toFixed(1);

    const counts = {};
    values.forEach((v) => {
        counts[v] = (counts[v] || 0) + 1;
    });
    const maxFreq = Math.max(...Object.values(counts));
    const modusVals = Object.keys(counts)
        .filter((k) => counts[k] === maxFreq)
        .map(Number);
    const primaryModus = modusVals[0];

    const getInitsForVal = (val) =>
        approvedRows
            .filter((r) => Number(r.process_month_value) === val)
            .map((r) => ({ no: r.no, status: r.latest_review_status }));

    return {
        totalApproved: approvedRows.length,
        totalNotApproved: notApprovedRows.length,
        notApprovedInitiatives: notApprovedRows.map((r) => ({
            no: r.no,
            status: r.latest_review_status,
        })),
        stats: [
            {
                label: "Min",
                desc: "Inisiatif dengan durasi approval tercepat",
                bulan: minVal,
                jumlah: approvedRows.filter((r) => Number(r.process_month_value) === minVal).length,
                initiatives: getInitsForVal(minVal),
            },
            {
                label: "Median",
                desc: "Rata-rata durasi approval seluruh inisiatif",
                bulan: avgVal,
                jumlah: "-",
                initiatives: [],
                customText: `Dari total ${approvedRows.length} inisiatif yang telah approve`,
            },
            {
                label: "Max",
                desc: "Inisiatif dengan durasi approval terlama",
                bulan: maxVal,
                jumlah: approvedRows.filter((r) => Number(r.process_month_value) === maxVal).length,
                initiatives: getInitsForVal(maxVal),
            },
            {
                label: "Modus",
                desc: "Durasi approval yang paling sering muncul",
                bulan: primaryModus,
                jumlah: maxFreq,
                initiatives: getInitsForVal(primaryModus),
            },
        ],
    };
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

const getCircleColor = (status) => {
    const s = String(status ?? "").trim().toLowerCase();
    if (["on track", "on-track"].includes(s)) return "bg-emerald-500";
    if (["at risk", "at-risk"].includes(s)) return "bg-orange-500";
    if (["delayed"].includes(s)) return "bg-orange-600";
    if (["on progress", "on-progress", "on progres", "in progress"].includes(s)) return "bg-sky-500";
    if (["not started", "not-started"].includes(s)) return "bg-blue-500";
    if (["not signed", "not-signed"].includes(s)) return "bg-rose-500";
    return "bg-slate-400";
};
</script>
