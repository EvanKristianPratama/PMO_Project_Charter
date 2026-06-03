<template>
    <section v-if="matrixData.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-150">
        <!-- Bar Chart Statistik -->
        <OwnerStatistikChart 
            v-if="showChart"
            :matrixData="matrixData" 
            :periodChartData="periodChartData"
            :selectedOwnerLabel="selectedOwnerLabel"
        />

        <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20 mt-6">
            <div class="border-b border-slate-900 px-2 py-2 dark:border-white/20">
                <div class="flex flex-wrap items-center justify-start gap-2">
                    <button
                        type="button"
                        class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                        :class="showInitiativeColumns
                            ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                            : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                        @click="showInitiativeColumns = !showInitiativeColumns"
                    >
                        {{ showInitiativeColumns ? 'Hide Inisiatif' : 'Show Inisiatif' }}
                    </button>
                    <button
                        type="button"
                        class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                        :class="showChart
                            ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                            : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                        @click="showChart = !showChart"
                    >
                        {{ showChart ? 'Hide Chart' : 'Show Chart' }}
                    </button>
                    <label for="owner-filter" class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                        Project Owner
                    </label>
                    <select
                        id="owner-filter"
                        v-model="selectedOwner"
                        class="max-w-[150px] truncate cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                    >
                        <option value="all">All Project Owner</option>
                        <option
                            v-for="owner in ownerOptions"
                            :key="`${viewMode}-owner-${owner.value}`"
                            :value="owner.value"
                        >
                            {{ owner.label }}
                        </option>
                    </select>
                    <label for="charter-version-filter" class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                        Charter Version
                    </label>
                    <select
                        id="charter-version-filter"
                        v-model="selectedCharterVersion"
                        class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                    >
                        <option value="all">All Version</option>
                        <option value="approved">Approved</option>
                        <option value="baseline">Baseline</option>
                    </select>
                    <label for="period-filter" class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                        Periode
                    </label>
                    <select
                        id="period-filter"
                        v-model="selectedPeriod"
                        class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                    >
                        <option value="all">All (Latest)</option>
                        <option
                            v-for="period in periodOptions"
                            :key="`${viewMode}-period-${period.value}`"
                            :value="period.value"
                        >
                            {{ period.label }}
                        </option>
                    </select>
                </div>
            </div>
            <table class="w-full border-collapse text-left text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            <div class="flex items-center justify-between gap-4">
                                <span>{{ label }}</span>
                                <select 
                                    v-model="viewMode"
                                    class="bg-white dark:bg-[#1a1a1a] border border-slate-300 dark:border-white/10 rounded px-2 py-0.5 text-[9px] font-bold outline-none focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                                >
                                    <option value="original">Original</option>
                                    <option value="restructure">Refinement</option>
                                </select>
                            </div>
                        </th>
                        <th v-for="status in statuses" :key="status" :colspan="statusColspan" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">
                            {{ status }}
                        </th>
                        <th rowspan="2" class="border-b border-l border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Total All
                        </th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <template v-for="status in statuses" :key="'sub-' + status">
                            <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                            <th v-if="showInitiativeColumns" class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        </template>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#171717]">
                    <tr v-for="row in matrixData" :key="row.name" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 px-4 py-4 font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.name }}
                        </td>
                        <template v-for="status in statuses" :key="row.name + status">
                            <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                {{ row.statusGroups[status]?.length || 0 }}
                            </td>
                            <td v-if="showInitiativeColumns" class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="init in row.statusGroups[status]"
                                        :key="init.no"
                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                        :class="getCircleColor(status)"
                                        :title="init.name"
                                    >
                                        {{ init.no }}
                                    </span>
                                    <span v-if="!row.statusGroups[status]?.length" class="text-slate-400 text-center w-full">-</span>
                                </div>
                            </td>
                        </template>
                        <td class="border-l border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.totalCount }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px]">
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">Grand Total</td>
                        <template v-for="status in statuses" :key="'footer-total-' + status">
                            <td class="border-t border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                                {{ columnTotals[status].count }}
                            </td>
                            <td v-if="showInitiativeColumns" class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="init in columnTotals[status].items"
                                        :key="'footer-cap-' + status + init.no"
                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                        :class="getCircleColor(status)"
                                        :title="init.name"
                                    >
                                        {{ init.no }}
                                    </span>
                                    <span v-if="!columnTotals[status].count" class="text-slate-400 text-center w-full">-</span>
                                </div>
                            </td>
                        </template>
                        <td class="border-t border-l border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            {{ grandTotalSum }}
                        </td>
                    </tr>
                    <tr class="bg-slate-100/80 text-slate-900 dark:bg-white/10 dark:text-white font-black uppercase text-[11px] border-t border-slate-900 dark:border-white/40">
                        <td :colspan="footerColspan" class="px-4 py-2.5 text-right border-r border-slate-900 dark:border-white/20">
                            Total Keseluruhan Inisiatif ({{ statuses.join(' + ') }})
                        </td>
                        <td class="px-4 py-2.5 text-center text-[13px]">
                            {{ grandTotalSum }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import OwnerStatistikChart from './OwnerStatistikChart.vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true
    },
    groupBy: {
        type: String,
        default: 'project_owner'
    },
    label: {
        type: String,
        default: 'Project Owner'
    }
});

const viewMode = ref('original'); // 'original' or 'restructure'
const showInitiativeColumns = ref(true);
const showChart = ref(true);
const selectedOwner = ref('all');
const selectedCharterVersion = ref('all');
const selectedPeriod = ref('all');

// Status list based on the user's image request order
const statuses = [
    'On Track',
    'At Risk',
    'Not Signed',
    'Not Started',
    'Done'
];

const selectedOwnerLabel = computed(() => {
    if (selectedOwner.value === 'all') return 'All Project Owner';
    return selectedOwner.value;
});

const statusColspan = computed(() => (showInitiativeColumns.value ? 2 : 1));
const footerColspan = computed(() => 1 + (statuses.length * statusColspan.value));

const ownerFieldKey = computed(() => {
    if (viewMode.value === 'original') {
        if (selectedCharterVersion.value === 'all') {
            return 'project_owner';
        }
        return `project_owner_${selectedCharterVersion.value}`;
    }

    return `${props.groupBy}_restructure`;
});

const ownerSortFieldKey = computed(() => {
    if (viewMode.value === 'original') {
        if (selectedCharterVersion.value === 'all') {
            return 'project_owner_code';
        }
        return `project_owner_${selectedCharterVersion.value}_code`;
    }

    return `${ownerFieldKey.value}_code`;
});

const charterNameKey = computed(() => {
    if (viewMode.value === 'original') {
        if (selectedCharterVersion.value === 'all') {
            return 'project_charter_name';
        }
        return `project_charter_name_${selectedCharterVersion.value}`;
    }

    return 'project_charter_name';
});

const ownerOptions = computed(() => {
    const map = new Map();

    props.rows.forEach((row) => {
        if (selectedCharterVersion.value !== 'all') {
            if (selectedCharterVersion.value === 'approved' && !row.has_approved) {
                return;
            }
            if (selectedCharterVersion.value === 'baseline' && !row.has_baseline) {
                return;
            }
        }

        const value = String(row[ownerFieldKey.value] ?? '').trim();
        if (!value || value === '-') return;

        const code = String(row[ownerSortFieldKey.value] ?? '').trim();

        if (!map.has(value)) {
            map.set(value, { value, label: value, code });
            return;
        }

        const existing = map.get(value);
        if ((existing.code ?? '') === '' && code !== '') {
            existing.code = code;
        }
    });

    return Array.from(map.values()).sort((a, b) => {
        const padCode = (val) => {
            let str = String(val ?? '').trim();
            if (!str) return '';
            while (str.length < 7) {
                str += '0';
            }
            return str;
        };
        const left = padCode(a.code);
        const right = padCode(b.code);

        if (left === '' && right === '') return a.label.localeCompare(b.label);
        if (left === '') return 1;
        if (right === '') return -1;

        return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
    });
});

const periodOrderValue = (periodLabel) => {
    const normalized = String(periodLabel ?? '').trim();
    if (!normalized) return 0;

    const parts = normalized.split(/\s+/);
    const year = Number(parts[parts.length - 1]);
    const monthName = parts.slice(0, -1).join(' ').trim();

    const monthOrderMap = {
        Januari: 1,
        Februari: 2,
        Maret: 3,
        April: 4,
        Mei: 5,
        Juni: 6,
        Juli: 7,
        Agustus: 8,
        September: 9,
        Oktober: 10,
        November: 11,
        Desember: 12,
    };

    const monthOrder = monthOrderMap[monthName] ?? 0;

    if (!Number.isFinite(year)) {
        return monthOrder;
    }

    return (year * 100) + monthOrder;
};

const periodOptions = computed(() => {
    const map = new Map();

    props.rows.forEach((row) => {
        const periods = Array.isArray(row.project_status_periods) && row.project_status_periods.length > 0
            ? row.project_status_periods
            : (String(row.latest_project_status_period ?? '').trim() ? [row.latest_project_status_period] : []);

        periods.forEach((period) => {
            const value = String(period ?? '').trim();
            if (!value) return;

            if (!map.has(value)) {
                map.set(value, { value, label: value });
            }
        });
    });

    return Array.from(map.values()).sort((a, b) => periodOrderValue(b.value) - periodOrderValue(a.value));
});

const getProjectStatusEntry = (row, period) => {
    const logs = Array.isArray(row.project_status_logs) ? row.project_status_logs : [];

    if (period === 'all') {
        if (logs.length > 0) {
            // Find the chronologically latest log using periodOrderValue
            let latestLog = logs[0];
            let maxOrder = periodOrderValue(latestLog.period);

            for (let i = 1; i < logs.length; i++) {
                const currentOrder = periodOrderValue(logs[i].period);
                if (currentOrder > maxOrder) {
                    maxOrder = currentOrder;
                    latestLog = logs[i];
                }
            }
            return latestLog;
        }

        return {
            status: String(row.latest_project_status ?? '').trim(),
            period: String(row.latest_project_status_period ?? '').trim(),
        };
    }

    for (let index = logs.length - 1; index >= 0; index -= 1) {
        const log = logs[index];
        if (String(log?.period ?? '').trim() === period) {
            return log;
        }
    }

    return null;
};

const getInitiativeTooltip = (row, statusEntry = null) => {
    const projectName = String(row[charterNameKey.value] || row.project_charter_name || row.initiative_name || '').trim();
    const status = String(statusEntry?.status ?? row.latest_project_status ?? '').trim();
    const periodLabel = String(statusEntry?.period ?? row.latest_project_status_period ?? '').trim();

    const nameParts = [];
    if (projectName !== '') nameParts.push(projectName);
    if (status !== '') nameParts.push(status);
    if (periodLabel !== '') nameParts.push(`(${periodLabel})`);

    return nameParts.join(' - ') || projectName || status || periodLabel;
};

const matrixData = computed(() => {
    const breakdown = {};
    const fieldKey = ownerFieldKey.value;
    const sortFieldKey = ownerSortFieldKey.value;

    const normalizeCode = (value) => String(value ?? '').trim();
    const compareSortCode = (leftCode, rightCode) => {
        const padCode = (val) => {
            let str = normalizeCode(val);
            if (!str) return '';
            while (str.length < 7) {
                str += '0';
            }
            return str;
        };
        const left = padCode(leftCode);
        const right = padCode(rightCode);

        if (left === '' && right === '') return 0;
        if (left === '') return 1;
        if (right === '') return -1;

        return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
    };

    props.rows.forEach((row) => {
        if (selectedCharterVersion.value !== 'all') {
            if (selectedCharterVersion.value === 'approved' && !row.has_approved) {
                return;
            }
            if (selectedCharterVersion.value === 'baseline' && !row.has_baseline) {
                return;
            }
        }

        const ownerValue = String(row[fieldKey] ?? '').trim();
        if (selectedOwner.value !== 'all' && ownerValue !== selectedOwner.value) {
            return;
        }

        const statusEntry = getProjectStatusEntry(row, selectedPeriod.value);
        if (!statusEntry || !String(statusEntry.status ?? '').trim()) {
            return;
        }

        const key = row[fieldKey] || "Unknown";
        const sortCode = normalizeCode(row[sortFieldKey]);
        // Normalize status to match our array
        let status = String(statusEntry.status || "").trim().toLowerCase();
        
        if (status === 'on-track') status = 'on track';
        if (status === 'at-risk') status = 'at risk';
        if (status === 'not-signed') status = 'not signed';
        if (status === 'not-started' || status === 'not start') status = 'not started';
        if (status === 'completed') status = 'done';

        if (!breakdown[key]) {
            breakdown[key] = {
                name: key,
                sortCode,
                statusGroups: {}
            };
            statuses.forEach(s => breakdown[key].statusGroups[s] = []);
        } else if (breakdown[key].sortCode === '' && sortCode !== '') {
            breakdown[key].sortCode = sortCode;
        }

        // Find match in statuses array (case-insensitive)
        const matchedStatus = statuses.find(s => s.toLowerCase() === status);
        
        if (matchedStatus && breakdown[key].statusGroups[matchedStatus]) {
            breakdown[key].statusGroups[matchedStatus].push({
                no: row.no,
                name: getInitiativeTooltip(row, statusEntry),
                projectName: String(row[charterNameKey.value] || row.project_charter_name || row.initiative_name || '').trim(),
                period: String(statusEntry?.period ?? row.latest_project_status_period ?? '').trim()
            });
        }
    });

    return Object.values(breakdown).sort((a, b) => {
        const codeCompare = compareSortCode(a.sortCode, b.sortCode);
        if (codeCompare !== 0) return codeCompare;

        return a.name.localeCompare(b.name);
    }).map((row) => ({
        ...row,
        totalCount: statuses.reduce((sum, status) => sum + (row.statusGroups[status]?.length || 0), 0),
    }));
});

const periodChartData = computed(() => {
    const map = new Map();
    props.rows.forEach((row) => {
        const periods = Array.isArray(row.project_status_periods) && row.project_status_periods.length > 0
            ? row.project_status_periods
            : (String(row.latest_project_status_period ?? '').trim() ? [row.latest_project_status_period] : []);

        periods.forEach((period) => {
            const value = String(period ?? '').trim();
            if (value) map.set(value, true);
        });
    });

    const sortedPeriods = Array.from(map.keys()).sort((a, b) => periodOrderValue(a) - periodOrderValue(b));

    const fieldKey = ownerFieldKey.value;

    return sortedPeriods.map(period => {
        const counts = {
            period,
            'On Track': 0,
            'At Risk': 0,
            'Not Signed': 0,
            'Not Started': 0,
            'Done': 0
        };

        props.rows.forEach(row => {
            if (selectedCharterVersion.value !== 'all') {
                if (selectedCharterVersion.value === 'approved' && !row.has_approved) return;
                if (selectedCharterVersion.value === 'baseline' && !row.has_baseline) return;
            }

            const ownerValue = String(row[fieldKey] ?? '').trim();
            if (selectedOwner.value !== 'all' && ownerValue !== selectedOwner.value) {
                return;
            }

            const statusEntry = getProjectStatusEntry(row, period);
            if (!statusEntry || !String(statusEntry.status ?? '').trim()) {
                return;
            }

            let status = String(statusEntry.status || "").trim().toLowerCase();
            if (status === 'on-track') status = 'on track';
            if (status === 'at-risk') status = 'at risk';
            if (status === 'not-signed') status = 'not signed';
            if (status === 'not-started' || status === 'not start') status = 'not started';
            if (status === 'completed') status = 'done';

            const matchedStatus = statuses.find(s => s.toLowerCase() === status);
            if (matchedStatus && counts[matchedStatus] !== undefined) {
                counts[matchedStatus]++;
            }
        });

        return counts;
    });
});

const columnTotals = computed(() => {
    const results = {};
    statuses.forEach(s => {
        results[s] = { count: 0, items: [] };
    });

    matrixData.value.forEach(row => {
        statuses.forEach(s => {
            const group = row.statusGroups[s] || [];
            results[s].count += group.length;
            results[s].items.push(...group);
        });
    });

    // Sort items by no for consistency
    statuses.forEach(s => {
        results[s].items.sort((a, b) => (Number(a.no) || 0) - (Number(b.no) || 0));
    });

    return results;
});

const grandTotalSum = computed(() => {
    return Object.values(columnTotals.value).reduce((sum, col) => sum + col.count, 0);
});

const getCircleColor = (status) => {
    const s = String(status ?? "").trim().toLowerCase();
    if (s === "on track") return "bg-emerald-500";
    if (s === "at risk") return "bg-amber-500";
    if (s === "not signed") return "bg-rose-500";
    if (s === "not started") return "bg-blue-500";
    if (s === "done") return "bg-slate-500";
    return "bg-slate-400";
};

watch(viewMode, () => {
    selectedOwner.value = 'all';
    selectedPeriod.value = 'all';
});

watch(selectedCharterVersion, () => {
    selectedOwner.value = 'all';
});
</script>
