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

    if (row.hasTimeline === false) {
        return [
            {
                type: "track",
                span: total,
                key: "track-full",
                endsYear: false,
            },
        ];
    }

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
            >
                <div class="cell-section-inner" :style="{ backgroundColor: sectionColor }">
                    <span class="cell-section__text">{{ sectionLabel }}</span>
                </div>
            </td>

            <!-- Count badge -->
            <td class="cell-label">
                <div class="label-wrap">
                    <span class="count-badge">{{ row.count }}</span>
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
                <div
                    v-else-if="cell.type === 'track'"
                    class="track-fill"
                >
                    <span class="track-label">{{ row.label }}</span>
                </div>
            </td>
        </tr>
    </template>
</template>

<style scoped>
.summary-row {
    border-bottom: 2px solid white;
}

/* ── Section label (vertical text, merged cell) ────── */
.cell-section {
    position: relative;
    width: 44px;
    min-width: 44px;
    max-width: 50px;
    padding: 0;
    vertical-align: middle;
    border-right: 6px solid white;
    border-bottom: 2px solid white;
    background: transparent;
}

.cell-section-inner {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px 0 0 6px;
    border: 2px solid #ed3d2f;
    box-sizing: border-box;
}

.cell-section__text {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.04em;
    color: #1a2e44;
    white-space: nowrap;
}

/* ── Label + badge cell ────────────────────────────── */
.cell-label {
    padding: 0;
    vertical-align: middle;
    border-right: 8px solid white;
    background: transparent;
    width: 56px;
    min-width: 56px;
}

.label-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 6px 8px;
}

.count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(180deg, #67a6ff 0%, #327cf5 100%);
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(20, 71, 158, 0.18);
}

/* ── Timeline gap / bar ─────────────────────────────── */
.cell-gap {
    height: 34px;
    padding: 0;
    background: #f0f0f0;
    border-bottom: 5px solid white;
}

.cell-bar {
    height: 34px;
    padding: 0;
    background: #f0f0f0;
    border-bottom: 5px solid white;
}

.bar-fill {
    width: 100%;
    height: 100%;
    min-height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
}

.track-fill {
    width: 100%;
    height: 100%;
    min-height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    background: linear-gradient(180deg, #fafafa 0%, #f1f1f1 100%);
    clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
}

.track-label {
    font-size: 10px;
    font-weight: 800;
    font-style: italic;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
}

.bar-label {
    font-size: 11px;
    font-weight: 800;
    font-style: italic;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
}

.year-sep {
    border-right: 4px solid white;
}

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 1280px) {
    .count-badge {
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
}

@media (max-width: 900px) {
    .cell-section__text {
        font-size: 11px;
    }
}
</style>
