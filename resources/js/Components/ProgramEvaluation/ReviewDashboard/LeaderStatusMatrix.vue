<template>
    <section v-if="displayRows.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-150">
        <div class="overflow-hidden rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
            <table class="w-full table-fixed border-collapse text-left text-[10px]">
                <thead>
                    <tr class="bg-slate-50 text-[9px] font-bold uppercase tracking-[0.12em] text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th rowspan="2" class="w-[8%] border-b border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                            Groub by
                        </th>
                        <th rowspan="2" class="w-[8%] border-b border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                            Groub by
                        </th>
                        <th rowspan="2" class="w-[8%] border-b border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                            Groub by
                        </th>
                        <th rowspan="2" class="w-[18%] border-b border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                            <div class="flex items-center justify-between gap-1">
                                <span class="whitespace-nowrap">{{ label }}</span>
                                <select
                                    v-model="viewMode"
                                    class="cursor-pointer rounded border border-slate-300 bg-white px-1 py-0.5 text-[7px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a]"
                                >
                                    <option value="original">Original</option>
                                    <option value="restructure">Refinement</option>
                                </select>
                            </div>
                        </th>
                        <th
                            v-for="status in statuses"
                            :key="status"
                            colspan="2"
                            class="border-b border-r border-slate-900 px-2 py-1.5 text-center dark:border-white/20"
                        >
                            {{ status }}
                        </th>
                        <th rowspan="2" class="w-[5%] border-b border-l border-slate-900 px-2 py-1.5 text-center dark:border-white/20">
                            Total All
                        </th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <template v-for="status in statuses" :key="'sub-' + status">
                            <th class="border-b border-r border-slate-900 px-2 py-1.5 text-center dark:border-white/20">
                                Total
                            </th>
                            <th class="border-b border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                                Inisiatif
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#171717]">
                    <tr
                        v-for="row in displayRows"
                        :key="row.leader"
                        class="border-b border-slate-200 transition-colors hover:bg-slate-50 dark:border-white/10 dark:hover:bg-white/5"
                    >
                    <td
                        v-if="row.showParentLevel2Cell"
                        :rowspan="row.parentLevel2RowSpan"
                            class="border-r border-slate-900 px-2 py-1.5 align-top font-black leading-tight text-slate-900 dark:border-white/20 dark:text-white break-words"
                        >
                            <span>{{ parseName(row.parentLevel2).title }}</span>
                            <span
                                v-if="parseName(row.parentLevel2).subtitle"
                                class="block font-normal italic text-slate-500 dark:text-slate-400"
                            >
                                - {{ parseName(row.parentLevel2).subtitle }}
                            </span>
                        </td>
                        <td
                            v-if="row.showParentLevel3Cell"
                            :rowspan="row.parentLevel3RowSpan"
                            class="border-r border-slate-900 px-2 py-1.5 align-top font-black leading-tight text-slate-900 dark:border-white/20 dark:text-white break-words"
                        >
                            <span>{{ parseName(row.parentLevel3).title }}</span>
                            <span
                                v-if="parseName(row.parentLevel3).subtitle"
                                class="block font-normal italic text-slate-500 dark:text-slate-400"
                            >
                                - {{ parseName(row.parentLevel3).subtitle }}
                            </span>
                        </td>
                        <td
                            v-if="row.showParentLevel4Cell"
                            :rowspan="row.parentLevel4RowSpan"
                            class="border-r border-slate-900 px-2 py-1.5 align-top font-black leading-tight text-slate-900 dark:border-white/20 dark:text-white break-words"
                        >
                            <span>{{ parseName(row.parentLevel4).title }}</span>
                            <span
                                v-if="parseName(row.parentLevel4).subtitle"
                                class="block font-normal italic text-slate-500 dark:text-slate-400"
                            >
                                - {{ parseName(row.parentLevel4).subtitle }}
                            </span>
                        </td>
                        <td class="border-r border-slate-900 px-2 py-1.5 font-black leading-tight text-slate-900 dark:border-white/20 dark:text-white break-words">
                            <span>{{ parseName(row.leader).title }}</span>
                            <span
                                v-if="parseName(row.leader).subtitle"
                                class="block font-normal italic text-slate-500 dark:text-slate-400"
                            >
                                - {{ parseName(row.leader).subtitle }}
                            </span>
                        </td>
                        <template v-for="status in statuses" :key="row.leader + status">
                            <td class="border-r border-slate-900 px-2 py-1.5 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                                {{ row.statusGroups[status]?.length || 0 }}
                            </td>
                            <td class="border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="init in row.statusGroups[status]"
                                        :key="`${row.leader}-${status}-${init.no}`"
                                        class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm"
                                        :class="getCircleColor(status)"
                                        :title="init.projectCharterName || init.status"
                                    >
                                        {{ init.no }}
                                    </span>
                                    <span v-if="!row.statusGroups[status]?.length" class="w-full text-center text-slate-400">-</span>
                                </div>
                            </td>
                        </template>
                        <td class="border-l border-slate-900 px-2 py-1.5 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.totalCount }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr
                        class="border-t border-slate-200 bg-slate-50 text-[9px] font-black uppercase tracking-[0.12em] text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-white"
                    >
                        <td colspan="3" class="border-t border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                            Grand Total
                        </td>
                        <td class="border-t border-r border-slate-900 px-2 py-1.5 dark:border-white/20 text-slate-700 dark:text-slate-300">
                            -
                        </td>
                        <template v-for="status in statuses" :key="'footer-total-' + status">
                            <td class="border-t border-r border-slate-900 px-2 py-1.5 text-center dark:border-white/20">
                                {{ columnTotals[status].count }}
                            </td>
                            <td class="border-t border-r border-slate-900 px-2 py-1.5 dark:border-white/20">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="init in columnTotals[status].items"
                                        :key="`footer-cap-${status}-${init.no}`"
                                        class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm"
                                        :class="getCircleColor(status)"
                                        :title="init.projectCharterName || init.status"
                                    >
                                        {{ init.no }}
                                    </span>
                                    <span v-if="!columnTotals[status].count" class="w-full text-center text-slate-400">-</span>
                                </div>
                            </td>
                        </template>
                        <td class="border-t border-l border-slate-900 px-2 py-1.5 text-center dark:border-white/20">
                            {{ grandTotalSum }}
                        </td>
                    </tr>
                    <tr class="border-t border-slate-900 bg-slate-100/80 text-[9px] font-black uppercase tracking-[0.12em] text-slate-900 dark:border-white/40 dark:bg-white/10 dark:text-white">
                        <td :colspan="4 + (statuses.length * 2)" class="border-r border-slate-900 px-2 py-1.5 text-right dark:border-white/20">
                            Total Keseluruhan Inisiatif ({{ statuses.join(' + ') }})
                        </td>
                        <td class="px-2 py-1.5 text-center text-[12px]">
                            {{ grandTotalSum }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
    groupBy: {
        type: String,
        default: 'project_leader',
    },
    label: {
        type: String,
        default: 'Project Leader',
    },
});

const viewMode = ref('original');

const statuses = ['On Track', 'At Risk', 'Not Signed', 'Not Started', 'Done'];

const parseName = (val) => {
    const str = String(val ?? '').trim();
    if (!str || str === '-') return { title: '-', subtitle: '' };

    const parts = str.split(' - ');
    if (parts.length > 1) {
        return {
            title: parts[0],
            subtitle: parts.slice(1).join(' - '),
        };
    }

    return { title: str, subtitle: '' };
};

const normalizeStatus = (value) => {
    const status = String(value ?? '').trim().toLowerCase();

    if (status === 'on-track') return 'on track';
    if (status === 'at-risk') return 'at risk';
    if (status === 'not-signed') return 'not signed';
    if (status === 'not-started' || status === 'not start') return 'not started';
    if (status === 'completed') return 'done';

    return status;
};

const getInitiativeTooltip = (row) => {
    const projectName = String(row.project_charter_name || row.initiative_name || '').trim();
    const status = String(row.latest_review_status || '').trim();
    const periodLabel = String(row.latest_review_period || '').trim();

    const nameParts = [];
    if (projectName !== '') nameParts.push(projectName);
    if (status !== '') nameParts.push(status);
    if (periodLabel !== '') nameParts.push(`(${periodLabel})`);

    return nameParts.join(' - ') || projectName || status || periodLabel;
};

const compareSortCode = (leftCode, rightCode) => {
    const left = String(leftCode ?? '').trim();
    const right = String(rightCode ?? '').trim();

    if (left === '' && right === '') return 0;
    if (left === '') return 1;
    if (right === '') return -1;

    return left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
};

const buildGroupedRows = (fieldKey) => {
    const breakdown = {};
    const sortFieldKey = `${fieldKey}_code`;
    const parentFieldKey = `${fieldKey}_parent`;
    const parentCodeFieldKey = `${fieldKey}_parent_code`;

    props.rows.forEach((row) => {
        const leader = row[fieldKey] || 'Unknown Leader';
        const leaderCode = String(row[sortFieldKey] ?? '').trim();
        const parent = String(row[parentFieldKey] ?? '').trim();
        const parentCode = String(row[parentCodeFieldKey] ?? '').trim();
        const parentLevel2 = String(row[`${fieldKey}_parent_level2`] ?? '').trim();
        const parentLevel3 = String(row[`${fieldKey}_parent_level3`] ?? '').trim();
        const parentLevel4 = String(row[`${fieldKey}_parent_level4`] ?? '').trim();

        if (!breakdown[leader]) {
            breakdown[leader] = {
                leader,
                sortCode: leaderCode,
                parent,
                parentCode,
                parentLevel2,
                parentLevel3,
                parentLevel4,
                statusGroups: {},
            };
            statuses.forEach((status) => {
                breakdown[leader].statusGroups[status] = [];
            });
        } else {
            if (breakdown[leader].sortCode === '' && leaderCode !== '') {
                breakdown[leader].sortCode = leaderCode;
            }
            if (breakdown[leader].parentCode === '' && parentCode !== '') {
                breakdown[leader].parentCode = parentCode;
            }
            if ((breakdown[leader].parent === '' || breakdown[leader].parent === '-') && parent !== '') {
                breakdown[leader].parent = parent;
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
        }

        const matchedStatus = statuses.find((status) => normalizeStatus(status) === normalizeStatus(row.latest_review_status));
        if (!matchedStatus) return;

        breakdown[leader].statusGroups[matchedStatus].push({
            no: row.no,
            status: row.latest_review_status,
            projectCharterName: getInitiativeTooltip(row),
        });
    });

    return Object.values(breakdown)
        .sort((a, b) => {
            const codeCompare = compareSortCode(a.sortCode, b.sortCode);
            if (codeCompare !== 0) return codeCompare;

            return String(a.leader).localeCompare(String(b.leader));
        })
        .map((row) => ({
            ...row,
            totalCount: statuses.reduce((sum, status) => sum + (row.statusGroups[status]?.length || 0), 0),
        }));
};

const displayRows = computed(() => {
    const fieldKey = viewMode.value === 'original' ? props.groupBy : `${props.groupBy}_restructure`;
    const rows = buildGroupedRows(fieldKey).map((row) => ({
        ...row,
        showParentLevel2Cell: false,
        showParentLevel3Cell: false,
        showParentLevel4Cell: false,
        parentLevel2RowSpan: 1,
        parentLevel3RowSpan: 1,
        parentLevel4RowSpan: 1,
    }));

    const computeRowSpansForLevel = (level) => {
        let groupStartIndex = 0;
        let currentGroupKey = null;

        const finalizeGroup = (endIndexExclusive) => {
            const span = endIndexExclusive - groupStartIndex;
            if (span > 0) {
                rows[groupStartIndex][`showParentLevel${level}Cell`] = true;
                rows[groupStartIndex][`parentLevel${level}RowSpan`] = span;
            }
        };

        rows.forEach((row, index) => {
            let groupKey = '';
            for (let currentLevel = 2; currentLevel <= level; currentLevel += 1) {
                groupKey += `||${row[`parentLevel${currentLevel}`]}`;
            }

            if (currentGroupKey === null) {
                currentGroupKey = groupKey;
                groupStartIndex = index;
                return;
            }

            if (groupKey !== currentGroupKey) {
                finalizeGroup(index);
                currentGroupKey = groupKey;
                groupStartIndex = index;
            }
        });

        finalizeGroup(rows.length);
    };

    for (let level = 2; level <= 4; level += 1) {
        computeRowSpansForLevel(level);
    }

    return rows;
});

const columnTotals = computed(() => {
    const results = {};
    statuses.forEach((status) => {
        results[status] = { count: 0, items: [] };
    });

    displayRows.value.forEach((row) => {
        statuses.forEach((status) => {
            const group = row.statusGroups[status] || [];
            results[status].count += group.length;
            results[status].items.push(...group);
        });
    });

    statuses.forEach((status) => {
        results[status].items.sort((a, b) => (Number(a.no) || 0) - (Number(b.no) || 0));
    });

    return results;
});

const grandTotalSum = computed(() => {
    return Object.values(columnTotals.value).reduce((sum, column) => sum + column.count, 0);
});

const getCircleColor = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase();
    if (normalized === 'on track') return 'bg-emerald-500';
    if (normalized === 'at risk') return 'bg-amber-500';
    if (normalized === 'not signed') return 'bg-rose-500';
    if (normalized === 'not started') return 'bg-blue-500';
    if (normalized === 'done') return 'bg-slate-500';
    return 'bg-slate-400';
};
</script>
