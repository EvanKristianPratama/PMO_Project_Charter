<template>
    <section v-if="matrixData.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-150">
        <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
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
                        <th v-for="status in statuses" :key="status" colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">
                            {{ status }}
                        </th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <template v-for="status in statuses" :key="'sub-' + status">
                            <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
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
                            <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
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
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px]">
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">Grand Total</td>
                        <template v-for="status in statuses" :key="'footer-total-' + status">
                            <td class="border-t border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                                {{ columnTotals[status].count }}
                            </td>
                            <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
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
                    </tr>
                    <tr class="bg-slate-100/80 text-slate-900 dark:bg-white/10 dark:text-white font-black uppercase text-[11px] border-t border-slate-900 dark:border-white/40">
                        <td :colspan="1 + (statuses.length * 2) - 1" class="px-4 py-2.5 text-right border-r border-slate-900 dark:border-white/20">
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
import { ref, computed } from 'vue';

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

// Status list based on the user's image request order
const statuses = [
    'On Track',
    'At Risk',
    'Not Signed',
    'Not Started',
    'Done'
];

const matrixData = computed(() => {
    const breakdown = {};
    const fieldKey = viewMode.value === 'original' ? props.groupBy : `${props.groupBy}_restructure`;
    const sortFieldKey = `${fieldKey}_code`;

    const normalizeCode = (value) => String(value ?? '').trim();
    const compareSortCode = (leftCode, rightCode) => {
        const left = normalizeCode(leftCode);
        const right = normalizeCode(rightCode);

        if (left === '' && right === '') return 0;
        if (left === '') return 1;
        if (right === '') return -1;

        return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
    };

    props.rows.forEach((row) => {
        const key = row[fieldKey] || "Unknown";
        const sortCode = normalizeCode(row[sortFieldKey]);
        // Normalize status to match our array
        let status = String(row.latest_review_status || "").trim().toLowerCase();
        
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
                name: row.initiative_name
            });
        }
    });

    return Object.values(breakdown).sort((a, b) => {
        const codeCompare = compareSortCode(a.sortCode, b.sortCode);
        if (codeCompare !== 0) return codeCompare;

        return a.name.localeCompare(b.name);
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
</script>
