<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    startYear: {
        type: Number,
        default: null,
    },
    endYear: {
        type: Number,
        default: null,
    },
    startYearRange: {
        type: Number,
        default: 2024,
    },
    endYearRange: {
        type: Number,
        default: 2029,
    },
});

const quarterNumbers = [1, 2, 3, 4];

const resolvedStartYear = computed(() => Number(props.startYear ?? props.startYearRange) || 2024);
const resolvedEndYear = computed(() => {
    const normalizedEnd = Number(props.endYear ?? props.endYearRange) || 2029;

    return normalizedEnd >= resolvedStartYear.value ? normalizedEnd : resolvedStartYear.value;
});

const years = computed(() =>
    Array.from({ length: resolvedEndYear.value - resolvedStartYear.value + 1 }, (_, index) => resolvedStartYear.value + index),
);

const quarterCells = computed(() =>
    years.value.flatMap((year) =>
        quarterNumbers.map((quarter) => ({
            year,
            quarter,
            label: `Q${quarter}`,
        })),
    ),
);

const totalCells = computed(() => quarterCells.value.length);

const normalizeQuarter = (value) => {
    const raw = String(value ?? '').trim().toUpperCase();
    const matched = raw.match(/Q?([1-4])/);

    return matched ? Number(matched[1]) : null;
};

const resolveInitiativeName = (item) => {
    const name = String(item?.initiative_name ?? item?.initiativeName ?? '').trim();
    if (name !== '') {
        return name;
    }

    const initiativeId = Number(item?.initiative_id ?? item?.initiativeId ?? 0);

    return initiativeId > 0 ? `Initiative #${initiativeId}` : 'Unknown Initiative';
};

const resolveOrganizationName = (item) => {
    const organizationName = String(item?.organization_name ?? item?.organizationName ?? '').trim();

    return organizationName !== '' ? organizationName : '-';
};

const normalizedItems = computed(() => {
    const items = Array.isArray(props.data) ? props.data : [];
    const maxQuarterIndex = totalCells.value - 1;
    const initiativeOrder = new Map();

    return items
        .map((item, index) => {
            const itemStartYear = Number(item?.startYear ?? item?.start_year ?? 0);
            const itemEndYear = Number(item?.endYear ?? item?.end_year ?? 0);
            const itemStartQuarter = normalizeQuarter(item?.startQ ?? item?.start_q ?? item?.startQuarter ?? item?.start_quarter);
            const itemEndQuarter = normalizeQuarter(item?.endQ ?? item?.end_q ?? item?.endQuarter ?? item?.end_quarter);

            if (!itemStartYear || !itemEndYear || !itemStartQuarter || !itemEndQuarter) {
                return null;
            }

            const rawStartIndex = ((itemStartYear - resolvedStartYear.value) * 4) + (itemStartQuarter - 1);
            const rawEndIndex = ((itemEndYear - resolvedStartYear.value) * 4) + (itemEndQuarter - 1);
            const startIndex = Math.min(rawStartIndex, rawEndIndex);
            const endIndex = Math.max(rawStartIndex, rawEndIndex);

            if (endIndex < 0 || startIndex > maxQuarterIndex) {
                return null;
            }

            const initiativeKey = String(item?.initiative_id ?? item?.initiativeId ?? resolveInitiativeName(item));

            if (!initiativeOrder.has(initiativeKey)) {
                initiativeOrder.set(initiativeKey, initiativeOrder.size);
            }

            return {
                key: String(item?.id ?? `roadmap-item-${index}`),
                initiativeKey,
                initiativeOrder: initiativeOrder.get(initiativeKey) ?? index,
                organizationName: resolveOrganizationName(item),
                initiativeName: resolveInitiativeName(item),
                activity: String(item?.activity ?? item?.title ?? '-').trim() || '-',
                startIndex: Math.max(0, startIndex),
                endIndex: Math.min(maxQuarterIndex, endIndex),
                sourceIndex: index,
            };
        })
        .filter(Boolean)
        .sort((left, right) => {
            if (left.initiativeOrder !== right.initiativeOrder) {
                return left.initiativeOrder - right.initiativeOrder;
            }

            if (left.startIndex !== right.startIndex) {
                return left.startIndex - right.startIndex;
            }

            if (left.endIndex !== right.endIndex) {
                return left.endIndex - right.endIndex;
            }

            const activityComparison = left.activity.localeCompare(right.activity, undefined, {
                numeric: true,
                sensitivity: 'base',
            });

            if (activityComparison !== 0) {
                return activityComparison;
            }

            return left.sourceIndex - right.sourceIndex;
        });
});

const allocateLanes = (items) => {
    const lanes = [];
    const laneEndIndexes = [];

    items.forEach((item) => {
        let laneIndex = laneEndIndexes.findIndex((laneEndIndex) => item.startIndex > laneEndIndex);

        if (laneIndex === -1) {
            laneIndex = lanes.length;
            lanes.push([]);
            laneEndIndexes.push(-1);
        }

        lanes[laneIndex].push(item);
        laneEndIndexes[laneIndex] = item.endIndex;
    });

    return lanes;
};

const groupedRoadmapRows = computed(() => {
    const grouped = new Map();

    normalizedItems.value.forEach((item) => {
        if (!grouped.has(item.initiativeKey)) {
            grouped.set(item.initiativeKey, {
                initiativeKey: item.initiativeKey,
                organizationName: item.organizationName,
                initiativeName: item.initiativeName,
                items: [],
            });
        }

        grouped.get(item.initiativeKey).items.push(item);
    });

    const groups = Array.from(grouped.values()).map((group) => ({
        ...group,
        lanes: allocateLanes(group.items).map((laneItems, laneIndex) => ({
            key: `${group.initiativeKey}-lane-${laneIndex}`,
            cells: buildLaneCells(group.initiativeKey, laneIndex, laneItems),
        })),
    }));

    let groupIndex = 0;

    while (groupIndex < groups.length) {
        const organizationName = groups[groupIndex].organizationName;
        let organizationRowSpan = 0;
        let endIndex = groupIndex;

        while (endIndex < groups.length && groups[endIndex].organizationName === organizationName) {
            organizationRowSpan += groups[endIndex].lanes.length;
            endIndex += 1;
        }

        groups[groupIndex].showOrganizationCell = true;
        groups[groupIndex].organizationRowSpan = organizationRowSpan;

        for (let hiddenIndex = groupIndex + 1; hiddenIndex < endIndex; hiddenIndex += 1) {
            groups[hiddenIndex].showOrganizationCell = false;
            groups[hiddenIndex].organizationRowSpan = 0;
        }

        groupIndex = endIndex;
    }

    return groups;
});

const isYearEndIndex = (index) => quarterCells.value[index]?.quarter === 4;

const buildLaneCells = (initiativeKey, laneIndex, items) => {
    const cells = [];
    let cursor = 0;

    items.forEach((item, itemIndex) => {
        while (cursor < item.startIndex) {
            cells.push({
                key: `${initiativeKey}-${laneIndex}-gap-${cursor}`,
                type: 'gap',
                span: 1,
                endsYear: isYearEndIndex(cursor),
            });
            cursor += 1;
        }

        cells.push({
            key: `${initiativeKey}-${laneIndex}-bar-${itemIndex}`,
            type: 'bar',
            span: (item.endIndex - item.startIndex) + 1,
            endsYear: isYearEndIndex(item.endIndex),
            label: item.activity,
            title: item.activity,
        });
        cursor = item.endIndex + 1;
    });

    while (cursor < totalCells.value) {
        cells.push({
            key: `${initiativeKey}-${laneIndex}-gap-${cursor}`,
            type: 'gap',
            span: 1,
            endsYear: isYearEndIndex(cursor),
        });
        cursor += 1;
    }

    return cells;
};
</script>

<template>
    <div class="roadmap-wrap">
        <div v-if="groupedRoadmapRows.length === 0" class="roadmap-empty">
            Belum ada data roadmap untuk ditampilkan.
        </div>

        <table v-else class="roadmap-table" :style="{ '--qcount': Math.max(totalCells, 1) }">
            <colgroup>
                <col class="col-organization">
                <col class="col-initiative">
                <col v-for="(_, index) in quarterCells" :key="`quarter-col-${index}`" class="col-quarter">
            </colgroup>

            <thead>
                <tr>
                    <th rowspan="2" class="th">
                        Organization
                    </th>
                    <th rowspan="2" class="th th-left">
                        Digital Initiative Roadmap {{ resolvedStartYear }}-{{ resolvedEndYear }}
                    </th>
                    <th
                        v-for="year in years"
                        :key="`year-${year}`"
                        colspan="4"
                        class="th th-year"
                    >
                        {{ year }}
                    </th>
                </tr>
                <tr>
                    <th
                        v-for="(cell, index) in quarterCells"
                        :key="`quarter-${cell.year}-${index}`"
                        class="th-q"
                        :class="{ 'border-r-blue': cell.quarter === 4 }"
                    >
                        {{ cell.label }}
                    </th>
                </tr>
            </thead>

            <tbody>
                <template v-for="group in groupedRoadmapRows" :key="group.initiativeKey">
                    <tr
                        v-for="(lane, laneIndex) in group.lanes"
                        :key="`${group.initiativeKey}-${lane.key}`"
                        class="row-data"
                    >
                        <td
                            v-if="laneIndex === 0 && group.showOrganizationCell"
                            :rowspan="group.organizationRowSpan"
                            class="cell-organization"
                        >
                            {{ group.organizationName }}
                        </td>

                        <td
                            v-if="laneIndex === 0"
                            :rowspan="group.lanes.length"
                            class="cell-project-name"
                        >
                            {{ group.initiativeName }}
                        </td>

                        <td
                            v-for="cell in lane.cells"
                            :key="cell.key"
                            :colspan="cell.span"
                            :class="[
                                cell.type === 'bar' ? 'cell-tl-bar' : 'cell-tl',
                                { 'border-r-blue': cell.endsYear },
                            ]"
                            :title="cell.title ?? ''"
                        >
                            <span v-if="cell.type === 'bar'" class="cell-tl-bar__text">
                                {{ cell.label }}
                            </span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.roadmap-wrap {
    --blue: #1c75bc;
    --blue-lt: #e2f0fb;
    --grid: #b9d1e8;
    --text: #0f172a;
    --text-sm: #111827;
    --bg: #ffffff;
    --bg-row: #f9fbff;
    --active: #111111;
    font-family: "Segoe UI", Arial, sans-serif;
}

.roadmap-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    border: 2px solid var(--blue);
    background: var(--bg);
}

.roadmap-table th,
.roadmap-table td {
    border: 1px solid var(--grid);
    overflow: hidden;
}

.col-organization {
    width: 9%;
}

.col-initiative {
    width: 23%;
}

.col-quarter {
    width: calc(68% / var(--qcount));
}

.th {
    background: var(--blue);
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 5px 6px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.2;
}

.th-left {
    text-align: left;
}

.th-q {
    background: var(--blue-lt);
    color: var(--blue);
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    padding: 2px 0;
    border-top: 1px solid var(--grid);
}

.border-r-blue {
    border-right: 2px solid var(--blue) !important;
}

.row-data {
    background: var(--bg-row);
}

.cell-organization {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    padding: 6px 8px;
    line-height: 1.3;
    text-align: center;
    vertical-align: middle;
    background: #f8fafc;
}

.cell-project-name {
    font-size: 12px;
    font-weight: 500;
    color: var(--text);
    padding: 6px 8px;
    line-height: 1.3;
    vertical-align: middle;
    background: var(--bg);
}

.cell-tl {
    height: 20px;
    padding: 0;
    background: var(--bg-row);
}

.roadmap-table td.cell-tl-bar {
    height: auto;
    padding: 6px 10px;
    background: #b7cd26;
    color: #2f3d0a;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.3;
    vertical-align: middle;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    border-top: 1px solid rgba(111, 130, 20, 0.45);
    border-bottom: 1px solid rgba(111, 130, 20, 0.45);
}

.cell-tl-bar__text {
    display: block;
    max-width: 100%;
    overflow: visible;
    text-overflow: clip;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.roadmap-empty {
    border: 1px dashed var(--grid);
    border-radius: 20px;
    background: #ffffff;
    padding: 32px 24px;
    text-align: center;
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 1024px) {
    .col-organization {
        width: 12%;
    }

    .col-initiative {
        width: 30%;
    }

    .col-quarter {
        width: calc(58% / var(--qcount));
    }

    .cell-organization,
    .cell-project-name {
        font-size: 11px;
    }

    .roadmap-table td.cell-tl-bar {
        padding: 6px;
        font-size: 9px;
    }
}
</style>
