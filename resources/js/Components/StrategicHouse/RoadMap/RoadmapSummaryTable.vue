<script setup>
import { computed } from "vue";

const props = defineProps({
    /** Label for the left merged cell, e.g. "Holding", "IT Initiative" */
    sectionLabel: { type: String, required: true },
    /** Array of { label, count, startYear, startQuarter, endYear, endQuarter } */
    rows: { type: Array, default: () => [] },
    /** First year of the timeline grid */
    startYear: { type: Number, required: true },
    /** Last year of the timeline grid */
    endYear: { type: Number, required: true },
    /** CSS color for the bar */
    barColor: { type: String, default: "#dc2626" },
    /** CSS color for the section label background */
    sectionColor: { type: String, default: "#b7cd26" },
});

const years = computed(() =>
    Array.from(
        { length: props.endYear - props.startYear + 1 },
        (_, i) => props.startYear + i,
    ),
);

const totalCells = computed(() => years.value.length * 4);

/** Convert year + quarter to a 0-based cell index. */
function toCellIndex(year, quarter) {
    const raw = (year - props.startYear) * 4 + (quarter - 1);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

/**
 * Build an array of cell descriptors (gap | bar) for a single row.
 */
function buildCells(row) {
    const total = totalCells.value;
    const startIdx = toCellIndex(row.startYear, row.startQuarter);
    const endIdx = toCellIndex(row.endYear, row.endQuarter);
    const safeStart = Math.min(startIdx, endIdx);
    const safeEnd = Math.max(startIdx, endIdx);
    const cells = [];
    let c = 0;

    while (c < safeStart) {
        cells.push({
            type: "gap",
            span: 1,
            key: `g-${c}`,
            endsYear: (c + 1) % 4 === 0,
        });
        c++;
    }

    cells.push({
        type: "bar",
        span: safeEnd - safeStart + 1,
        key: `bar-${safeStart}`,
        endsYear: (safeEnd + 1) % 4 === 0,
    });
    c = safeEnd + 1;

    while (c < total) {
        cells.push({
            type: "gap",
            span: 1,
            key: `g-${c}`,
            endsYear: (c + 1) % 4 === 0,
        });
        c++;
    }

    return cells;
}

const normalizedRows = computed(() =>
    props.rows.map((row, idx) => ({
        ...row,
        cells: buildCells(row),
        rowKey: `${props.sectionLabel}-${idx}`,
    })),
);
</script>

<template>
    <template v-if="normalizedRows.length > 0">
        <tr
            v-for="(row, rowIdx) in normalizedRows"
            :key="row.rowKey"
            class="summary-row"
        >
            <!-- Section label cell — merged across all rows -->
            <td
                v-if="rowIdx === 0"
                :rowspan="normalizedRows.length"
                class="cell-section"
                :style="{ backgroundColor: sectionColor }"
            >
                <span class="cell-section__text">{{ sectionLabel }}</span>
            </td>

            <!-- Count badge -->
            <td class="cell-label">
                <div class="label-wrap">
                    <span class="count-badge">{{ row.count }}</span>
                    <span class="label-text">{{ row.label }}</span>
                </div>
            </td>

            <!-- Timeline cells -->
            <td
                v-for="cell in row.cells"
                :key="`${row.rowKey}-${cell.key}`"
                :colspan="cell.span"
                :class="[
                    cell.type === 'bar' ? 'cell-bar' : 'cell-gap',
                    cell.endsYear ? 'year-sep' : '',
                ]"
            >
                <div
                    v-if="cell.type === 'bar'"
                    class="bar-fill"
                    :style="{ backgroundColor: barColor }"
                >
                    <span class="bar-label">{{ row.label }}</span>
                </div>
            </td>
        </tr>
    </template>
</template>

<style scoped>
.summary-row {
    border-bottom: 1px solid #d9e4ef;
}

.summary-row:last-child {
    border-bottom: 2px solid #8ca9c7;
}

/* ── Section label (vertical text, merged cell) ────── */
.cell-section {
    width: 40px;
    min-width: 40px;
    max-width: 50px;
    padding: 8px 4px;
    text-align: center;
    vertical-align: middle;
    border-right: 2px solid #8ca9c7;
    border-bottom: 2px solid #8ca9c7;
}

.cell-section__text {
    display: block;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.04em;
    color: #1a2e44;
    white-space: nowrap;
}

/* ── Label + badge cell ────────────────────────────── */
.cell-label {
    padding: 6px 10px;
    vertical-align: middle;
    border-right: 1px solid #d9e4ef;
    background: #ffffff;
    min-width: 180px;
}

.label-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}

.count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    line-height: 1;
}

.label-text {
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
}

/* ── Timeline gap / bar ─────────────────────────────── */
.cell-gap {
    height: 36px;
    padding: 0;
    background: #ffffff;
    border-bottom: 1px solid #eef2f7;
}

.cell-bar {
    height: 36px;
    padding: 3px 0;
    background: transparent;
    border-bottom: 1px solid #eef2f7;
}

.bar-fill {
    width: 100%;
    height: 100%;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 0 10px;
    min-height: 28px;
}

.bar-label {
    font-size: 10px;
    font-weight: 700;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.year-sep {
    border-right: 1px dashed #cbd5e0;
}

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 1280px) {
    .cell-label {
        min-width: 150px;
    }
    .label-text {
        font-size: 10px;
    }
    .count-badge {
        width: 20px;
        height: 20px;
        font-size: 9px;
    }
}

@media (max-width: 900px) {
    .cell-label {
        min-width: 120px;
    }
    .cell-section__text {
        font-size: 10px;
    }
}
</style>
