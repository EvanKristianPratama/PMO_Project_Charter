<template>
    <UserLayout title="Project Status Summary">
        <div class="space-y-6 animate-fade-in-up">
            <!-- Duration Statistics Table -->
            <!-- <DurationStatsTable
                v-if="durationStats"
                :stats="durationStats.stats"
                :totalApproved="durationStats.totalApproved"
                :totalNotApproved="durationStats.totalNotApproved"
                :notApprovedInitiatives="durationStats.notApprovedInitiatives"
                :getCircleColor="getCircleColor"
            /> -->

            <!-- Project Owner Breakdown Table -->
            <!-- <OwnerBreakdownTable
                :originalData="ownerBreakdown.original"
                :restructureData="ownerBreakdown.restructure"
                :getCircleColor="getCircleColor"
            /> -->

            <!-- Project Leader Breakdown Table -->
            <!-- <LeaderBreakdownTable
                :originalData="leaderBreakdown.original"
                :restructureData="leaderBreakdown.restructure"
                :getCircleColor="getCircleColor"
            /> -->

            <!-- Status Matrix - Project Owner -->
            <OwnerStatusMatrix
                v-if="rows.length > 0"
                :rows="rows"
                groupBy="project_owner"
                label="Project Owner"
            />

            <!-- Status Matrix - Project Leader -->
            <LeaderStatusMatrix
                v-if="rows.length > 0"
                :rows="rows"
                groupBy="project_leader"
                label="Project Leader"
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
import OwnerStatusMatrix from '@/Components/ProgramEvaluation/ReviewDashboard/OwnerStatusMatrix.vue';
import LeaderStatusMatrix from '@/Components/ProgramEvaluation/ReviewDashboard/LeaderStatusMatrix.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
});

const getInitiativeTooltip = (row) => {
    const projectName = String(row.project_charter_name || row.initiative_name || '').trim();
    const status = String(row.latest_project_status || '').trim();

    if (projectName !== '' && status !== '') {
        return `${projectName} - ${status}`;
    }

    return projectName || status;
};

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
            .map((r) => ({
                no: r.no,
                status: r.latest_project_status,
                projectCharterName: getInitiativeTooltip(r),
            }));

    return {
        totalApproved: approvedRows.length,
        totalNotApproved: notApprovedRows.length,
        notApprovedInitiatives: notApprovedRows.map((r) => ({
            no: r.no,
            status: r.latest_project_status,
            projectCharterName: getInitiativeTooltip(r),
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
    const normalizeCode = (value) => String(value ?? '').trim();
    const compareSortCode = (leftCode, rightCode) => {
        const left = normalizeCode(leftCode);
        const right = normalizeCode(rightCode);

        if (left === '' && right === '') return 0;
        if (left === '') return 1;
        if (right === '') return -1;

        return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
    };

    const getBreakdown = (field) => {
        const breakdown = {};
        const sortField = `${field}_code`;
        props.rows.forEach((row) => {
            const owner = row[field] || "Unknown Owner";
            const ownerCode = normalizeCode(row[sortField]);
            if (!breakdown[owner]) {
                breakdown[owner] = {
                    owner,
                    sortCode: ownerCode,
                    totalCount: 0,
                    approved: [],
                    notApproved: [],
                };
            } else if (breakdown[owner].sortCode === '' && ownerCode !== '') {
                breakdown[owner].sortCode = ownerCode;
            }

            const isApproved =
                row.process_month_value !== null &&
                row.process_month_value !== undefined &&
                row.process_month_value !== "";

            const data = {
                no: row.no,
                status: row.latest_project_status,
                projectCharterName: getInitiativeTooltip(row),
            };

            breakdown[owner].totalCount += 1;

            if (isApproved) {
                breakdown[owner].approved.push(data);
            } else {
                breakdown[owner].notApproved.push(data);
            }
        });
        return Object.values(breakdown).sort((a, b) => {
            const codeCompare = compareSortCode(a.sortCode, b.sortCode);
            if (codeCompare !== 0) return codeCompare;
            return a.owner.localeCompare(b.owner);
        });
    };

    return {
        original: getBreakdown('project_owner'),
        restructure: getBreakdown('project_owner_restructure'),
    };
});

const leaderBreakdown = computed(() => {
    const normalizeCode = (value) => String(value ?? '').trim();
    const compareSortCode = (leftCode, rightCode) => {
        const left = normalizeCode(leftCode);
        const right = normalizeCode(rightCode);

        if (left === '' && right === '') return 0;
        if (left === '') return 1;
        if (right === '') return -1;

        return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
    };

    const getBreakdown = (field) => {
        const breakdown = {};
        const sortField = `${field}_code`;
        const parentField = `${field}_parent`;
        const parentCodeField = `${field}_parent_code`;
        props.rows.forEach((row) => {
            const leader = row[field] || "Unknown Leader";
            const leaderCode = normalizeCode(row[sortField]);
            const parentLabel = normalizeCode(row[parentField]);
            const parentCode = normalizeCode(row[parentCodeField]);
            const parentLevel2 = normalizeCode(row[`${field}_parent_level2`]);
            const parentLevel3 = normalizeCode(row[`${field}_parent_level3`]);
            const parentLevel4 = normalizeCode(row[`${field}_parent_level4`]);
            const parentLevel5 = normalizeCode(row[`${field}_parent_level5`]);
            const parentLevel6 = normalizeCode(row[`${field}_parent_level6`]);

            if (!breakdown[leader]) {
                breakdown[leader] = {
                    leader,
                    sortCode: leaderCode,
                    parent: parentLabel,
                    parentCode,
                    parentLevel2,
                    parentLevel3,
                    parentLevel4,
                    parentLevel5,
                    parentLevel6,
                    totalCount: 0,
                    approved: [],
                    notApproved: [],
                };
            } else {
                if (breakdown[leader].sortCode === '' && leaderCode !== '') {
                    breakdown[leader].sortCode = leaderCode;
                }
                if (breakdown[leader].parentCode === '' && parentCode !== '') {
                    breakdown[leader].parentCode = parentCode;
                }
                if ((breakdown[leader].parent === '' || breakdown[leader].parent === '-') && parentLabel !== '') {
                    breakdown[leader].parent = parentLabel;
                }
                if (breakdown[leader].parentLevel2 === '' && parentLevel2 !== '') {
                    breakdown[leader].parentLevel2 = parentLevel2;
                }
                if (breakdown[leader].parentLevel3 === '' && parentLevel3 !== '') {
                    breakdown[leader].parentLevel3 = parentLevel3;
                }
                if (breakdown[leader].parentLevel4 === '' && parentLevel4 !== '') {
                    breakdown[leader].parentLevel4 = parentLevel4;
                }
                if (breakdown[leader].parentLevel5 === '' && parentLevel5 !== '') {
                    breakdown[leader].parentLevel5 = parentLevel5;
                }
                if (breakdown[leader].parentLevel6 === '' && parentLevel6 !== '') {
                    breakdown[leader].parentLevel6 = parentLevel6;
                }
            }

            if (breakdown[leader].parent === '' && parentCode !== '') {
                breakdown[leader].parent = parentCode;
            }
            if (breakdown[leader].parentCode === '' && parentCode !== '') {
                breakdown[leader].parentCode = parentCode;
            }
            if (breakdown[leader].sortCode === '' && leaderCode !== '') {
                breakdown[leader].sortCode = leaderCode;
            }

            const isApproved =
                row.process_month_value !== null &&
                row.process_month_value !== undefined &&
                row.process_month_value !== "";

            const data = {
                no: row.no,
                status: row.latest_project_status,
                projectCharterName: getInitiativeTooltip(row),
            };

            breakdown[leader].totalCount += 1;

            if (isApproved) {
                breakdown[leader].approved.push(data);
            } else {
                breakdown[leader].notApproved.push(data);
            }
        });
        return Object.values(breakdown).sort((a, b) => {
            const codeCompare = compareSortCode(a.sortCode, b.sortCode);
            if (codeCompare !== 0) return codeCompare;
            return a.leader.localeCompare(b.leader);
        });
    };

    return {
        original: getBreakdown('project_leader'),
        restructure: getBreakdown('project_leader_restructure'),
    };
});

const getCircleColor = (status) => {
    const s = String(status ?? "").trim().toLowerCase();
    if (["on track", "on-track"].includes(s)) return "bg-emerald-500";
    if (["at risk", "at-risk"].includes(s)) return "bg-amber-500";
    if (["delayed"].includes(s)) return "bg-orange-600";
    if (["on progress", "on-progress", "on progres", "in progress"].includes(s)) return "bg-sky-500";
    if (["not started", "not-started"].includes(s)) return "bg-blue-500";
    if (["not signed", "not-signed"].includes(s)) return "bg-rose-500";
    return "bg-slate-400";
};
</script>
