<script setup>
import { computed } from "vue";

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    startYear: {
        type: Number,
        default: 2025,
    },
    endYear: {
        type: Number,
        default: 2029,
    },
    totalCount: {
        type: Number,
        default: 0,
    },
});

/* ---------- Year / Quarter helpers ---------- */

const years = computed(() =>
    Array.from(
        { length: props.endYear - props.startYear + 1 },
        (_, i) => props.startYear + i,
    ),
);

const quarterCells = computed(() =>
    years.value.flatMap((y) =>
        [1, 2, 3, 4].map((q) => ({ year: y, quarter: q, label: `Q${q}` })),
    ),
);

const totalCells = computed(() => quarterCells.value.length);

/* ---------- Cell builder ---------- */
/**
 * Returns an array of cell descriptors for a single initiative row.
 * Each descriptor: { type: 'gap'|'bar', span: number, key: string|number, endsYear: boolean }
 */
function buildCells(initiative) {
    const total = totalCells.value;

    // No milestone data -> render all-gap row (no bar)
    if (
        !initiative.startYear ||
        !initiative.startQ ||
        !initiative.endYear ||
        !initiative.endQ
    ) {
        return Array.from({ length: total }, (_, i) => ({
            type: "gap",
            span: 1,
            key: `empty-${i}`,
            endsYear: (i + 1) % 4 === 0,
        }));
    }

    const sq = parseInt(String(initiative.startQ).replace(/[Qq]/, ""), 10);
    const eq = parseInt(String(initiative.endQ).replace(/[Qq]/, ""), 10);
    const startIdx = (initiative.startYear - props.startYear) * 4 + (sq - 1);
    const endIdx = (initiative.endYear - props.startYear) * 4 + (eq - 1);

    // Clamp to valid range
    const safeStart = Math.max(0, Math.min(startIdx, total - 1));
    const safeEnd = Math.max(safeStart, Math.min(endIdx, total - 1));

    const cells = [];
    let cursor = 0;

    // Gap before bar
    while (cursor < safeStart) {
        cells.push({
            type: "gap",
            span: 1,
            key: cursor,
            endsYear: (cursor + 1) % 4 === 0,
        });
        cursor++;
    }

    // Bar
    cells.push({
        type: "bar",
        span: safeEnd - safeStart + 1,
        key: `bar-${safeStart}`,
        endsYear: (safeEnd + 1) % 4 === 0,
    });
    cursor = safeEnd + 1;

    // Gap after bar
    while (cursor < total) {
        cells.push({
            type: "gap",
            span: 1,
            key: cursor,
            endsYear: (cursor + 1) % 4 === 0,
        });
        cursor++;
    }

    return cells;
}
</script>

<template>
    <div class="gantt-wrap">
        <!-- Empty state -->
        <div v-if="!groups || groups.length === 0" class="gantt-empty">
            Belum ada data roadmap untuk ditampilkan.
        </div>

        <table
            v-else
            class="gantt-table"
            :style="{ '--qcount': Math.max(totalCells, 1) }"
        >
            <colgroup>
                <col class="col-coe" />
                <col class="col-initiative" />
                <col
                    v-for="(_, i) in quarterCells"
                    :key="`qcol-${i}`"
                    class="col-quarter"
                />
            </colgroup>

            <!-- Two-row header -->
            <thead>
                <!-- Row 1: labels + year groups -->
                <tr>
                    <th rowspan="2" class="th th-block">
                        IT Architecture
                        <br />
                        Building Block
                    </th>
                    <th rowspan="2" class="th th-left">
                        List of ITSP Initiatives {{ startYear }}-{{ endYear }}
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

                <!-- Row 2: Q1-Q4 per year -->
                <tr>
                    <th
                        v-for="(cell, i) in quarterCells"
                        :key="`q-${cell.year}-Q${cell.quarter}-${i}`"
                        class="th-q"
                        :class="{ 'border-r-navy': cell.quarter === 4 }"
                    >
                        {{ cell.label }}
                    </th>
                </tr>
            </thead>

            <!-- Body -->
            <tbody>
                <template
                    v-for="group in groups"
                    :key="`group-${group.coe_name}`"
                >
                    <tr
                        v-for="(initiative, idx) in group.initiatives"
                        :key="`${group.coe_name}-${initiative.no}`"
                        class="row-data"
                    >
                        <!-- CoE cell: only on first row of the group, spans all initiative rows -->
                        <td
                            v-if="idx === 0"
                            :rowspan="group.initiatives.length"
                            class="cell-coe"
                        >
                            {{ group.coe_name }}
                        </td>

                        <!-- Initiative name with numbered badge -->
                        <td class="cell-initiative">
                            <div class="initiative-row">
                                <span class="badge">{{ initiative.no }}</span>
                                <span class="initiative-name">{{
                                    initiative.name
                                }}</span>
                            </div>
                        </td>

                        <!-- Quarter cells (gap / bar) -->
                        <td
                            v-for="cell in buildCells(initiative)"
                            :key="cell.key"
                            :colspan="cell.span"
                            :class="[
                                cell.type === 'bar' ? 'cell-bar' : 'cell-gap',
                                { 'border-r-navy': cell.endsYear },
                            ]"
                        />
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
/* --- Design tokens --- */
.gantt-wrap {
    --navy: #102a4c;
    --navy-mid: #1a4b8c;
    --navy-lt: #dbeafe;
    --navy-text: #1e40af;
    --bar: #1a4b8c;
    --bar-border: #0d3069;
    --bar-hover: #1d4ed8;
    --grid: #cbd5e0;
    --grid-year: #6b8fb0;
    --text: #0f172a;
    --text-muted: #475569;
    --bg: #ffffff;
    --bg-row: #f8fafd;
    --bg-alt: #f1f5f9;
    font-family: "Segoe UI", Arial, sans-serif;
}

/* --- Outer wrapper --- */
.gantt-wrap {
    width: 100%;
    overflow-x: auto;
}

/* --- Table skeleton --- */
.gantt-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    border: 2px solid var(--navy);
    background: var(--bg);
    min-width: 900px;
}

.gantt-table th,
.gantt-table td {
    border: 1px solid var(--grid);
    overflow: hidden;
}

/* --- Column widths --- */
.col-coe {
    width: 10%;
}

.col-initiative {
    width: 22%;
}

.col-quarter {
    width: calc(68% / var(--qcount));
}

/* --- Header cells row 1 --- */
.th {
    background: var(--navy);
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 6px 7px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.3;
    white-space: nowrap;
}

.th-block {
    white-space: normal;
    line-height: 1.4;
}

.th-left {
    text-align: left;
    white-space: normal;
}

.th-year {
    border-left: 2px solid var(--grid-year) !important;
    border-right: 2px solid var(--grid-year) !important;
}

/* --- Header cells row 2 quarters --- */
.th-q {
    background: var(--navy-lt);
    color: var(--navy-text);
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    padding: 3px 0;
    border-top: 1px solid var(--grid);
}

/* Year-end right border */
.border-r-navy {
    border-right: 2px solid var(--navy-mid) !important;
}

/* --- Body rows --- */
.row-data {
    background: var(--bg-row);
}

.row-data:nth-child(even) {
    background: var(--bg-alt);
}

/* --- CoE Building Block cell --- */
.cell-coe {
    font-size: 10px;
    font-weight: 700;
    color: var(--navy);
    padding: 6px 8px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.35;
    background: #e8f0fb;
    border-right: 2px solid var(--navy-mid) !important;
    word-break: break-word;
}

/* --- Initiative name cell --- */
.cell-initiative {
    padding: 5px 7px;
    vertical-align: middle;
    background: var(--bg);
}

.initiative-row {
    display: flex;
    align-items: flex-start;
    gap: 5px;
}

/* Numbered badge */
.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--navy);
    color: #ffffff;
    font-size: 9px;
    font-weight: 700;
    line-height: 1;
    margin-top: 1px;
    letter-spacing: 0;
}

.initiative-name {
    font-size: 11px;
    font-weight: 500;
    color: var(--text);
    line-height: 1.35;
    word-break: break-word;
}

/* --- Gap cell no bar --- */
.cell-gap {
    height: 26px;
    padding: 0;
    background: inherit;
}

/* --- Bar cell --- */
.gantt-table td.cell-bar {
    height: 26px;
    padding: 0;
    background: var(--bar);
    border-top: 1px solid var(--bar-border);
    border-bottom: 1px solid var(--bar-border);
    transition: background 0.15s ease;
}

.gantt-table td.cell-bar:hover {
    background: var(--bar-hover);
}

/* --- Empty state --- */
.gantt-empty {
    border: 1px dashed var(--grid);
    border-radius: 16px;
    background: #ffffff;
    padding: 32px 24px;
    text-align: center;
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
}

/* --- Responsive --- */
@media (max-width: 1280px) {
    .col-coe {
        width: 12%;
    }
    .col-initiative {
        width: 28%;
    }
    .col-quarter {
        width: calc(60% / var(--qcount));
    }
    .cell-coe {
        font-size: 9px;
    }
    .initiative-name {
        font-size: 10px;
    }
    .badge {
        width: 14px;
        height: 14px;
        font-size: 8px;
    }
}

@media (max-width: 768px) {
    .col-coe {
        width: 14%;
    }
    .col-initiative {
        width: 34%;
    }
    .col-quarter {
        width: calc(52% / var(--qcount));
    }
}
</style>
