<template>
    <UserLayout title="Review Approval">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Review Approval Project Charter</h1>
                    </div>
                </div>
            </section>

            <InitiativesTimelineTable :items="sortedRows" />

            <!-- Statistics Summary Table -->
            <section v-if="durationStats" class="mt-12 overflow-hidden animate-fade-in-up delay-100">
                <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
                    <table class="w-full border-collapse text-left text-[11px]">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Category</th>
                                <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Total</th>
                                <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Statistik</th>
                                <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Deskripsi</th>
                                <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Bulan</th>
                                <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Jumlah</th>
                                <th class="border-b border-slate-900 px-4 py-3 dark:border-white/20">Inisiatif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#171717]">
                            <tr v-for="(stat, index) in durationStats.stats" :key="stat.label" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <!-- Rowspan for Total Approval Category and Total Count -->
                                <td v-if="index === 0" :rowspan="durationStats.stats.length" class="border-r border-slate-900 p-4 text-center font-black uppercase dark:border-white/20">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-slate-900 dark:text-white">Total Approval</span>
                                    </div>
                                </td>
                                <td v-if="index === 0" :rowspan="durationStats.stats.length" class="border-r border-slate-900 p-4 text-center text-3xl font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ durationStats.totalApproved }}
                                </td>

                                <!-- Stat Rows -->
                                <td class="border-r border-slate-900 px-4 py-2 font-bold uppercase text-slate-700 dark:border-white/20 dark:text-slate-300">
                                    {{ stat.label }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-2 italic text-slate-500 dark:border-white/20 dark:text-slate-400">
                                    {{ stat.desc }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-2 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ stat.bulan }} <span class="ml-0.5 text-[9px] font-normal text-slate-400">bulan</span>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-2 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ stat.jumlah }}
                                </td>
                                <td class="px-4 py-2 font-medium text-slate-600 dark:text-slate-400">
                                    <div v-if="stat.customText" class="italic text-slate-500 dark:text-slate-400">
                                        {{ stat.customText }}
                                    </div>
                                    <div v-else class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="init in stat.initiatives"
                                            :key="init.no"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                            :class="getCircleColor(init.status)"
                                            :title="init.status"
                                        >
                                            {{ init.no }}
                                        </span>
                                        <span v-if="stat.initiatives.length === 0" class="text-slate-400">-</span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Not Approved Row -->
                            <tr class="border-t border-slate-900 bg-slate-50/50 dark:border-white/20 dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="border-r border-slate-900 p-4 text-center font-black uppercase dark:border-white/20">
                                    Total Not Approval
                                </td>
                                <td class="border-r border-slate-900 p-4 text-center text-3xl font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ durationStats.totalNotApproved }}
                                </td>
                                <td colspan="4" class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Inisiatif:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="init in durationStats.notApprovedInitiatives"
                                                :key="init.no"
                                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                                :class="getCircleColor(init.status)"
                                                :title="init.status"
                                            >
                                                {{ init.no }}
                                            </span>
                                            <span v-if="durationStats.notApprovedInitiatives.length === 0" class="text-slate-400">-</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Project Owner Breakdown Table -->
            <section v-if="ownerBreakdown.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-125">
                <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
                    <table class="w-full border-collapse text-left text-[11px]">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Owner</th>
                                <th colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Aproval</th>
                                <th colspan="2" class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20">no aprove</th>
                            </tr>
                            <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                                <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                                <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                                <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                                <th class="border-b border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#171717]">
                            <tr v-for="row in ownerBreakdown" :key="row.owner" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="border-r border-slate-900 px-4 py-4 font-black uppercase text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.owner }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.approved.length }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="init in row.approved"
                                            :key="init.no"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                            :class="getCircleColor(init.status)"
                                            :title="init.status"
                                        >
                                            {{ init.no }}
                                        </span>
                                        <span v-if="row.approved.length === 0" class="text-slate-400">-</span>
                                    </div>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.notApproved.length }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="init in row.notApproved"
                                            :key="init.no"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                            :class="getCircleColor(init.status)"
                                            :title="init.status"
                                        >
                                            {{ init.no }}
                                        </span>
                                        <span v-if="row.notApproved.length === 0" class="text-slate-400">-</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Project Leader Breakdown Table -->
            <section v-if="leaderBreakdown.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-150">
                <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
                    <table class="w-full border-collapse text-left text-[11px]">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Leader</th>
                                <th colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Aproval</th>
                                <th colspan="2" class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20">no aprove</th>
                            </tr>
                            <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                                <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                                <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                                <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                                <th class="border-b border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#171717]">
                            <tr v-for="row in leaderBreakdown" :key="row.leader" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="border-r border-slate-900 px-4 py-4 font-black uppercase text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.leader }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.approved.length }}
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="init in row.approved"
                                            :key="init.no"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                            :class="getCircleColor(init.status)"
                                            :title="init.status"
                                        >
                                            {{ init.no }}
                                        </span>
                                        <span v-if="row.approved.length === 0" class="text-slate-400">-</span>
                                    </div>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                    {{ row.notApproved.length }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="init in row.notApproved"
                                            :key="init.no"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                            :class="getCircleColor(init.status)"
                                            :title="init.status"
                                        >
                                            {{ init.no }}
                                        </span>
                                        <span v-if="row.notApproved.length === 0" class="text-slate-400">-</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

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
