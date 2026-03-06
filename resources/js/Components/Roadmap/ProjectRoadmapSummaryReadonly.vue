<script setup>
import { computed } from 'vue';

const props = defineProps({
    project: { type: Object, required: true },
    sequence: { type: [Number, String], default: null },
    yearStart: { type: Number, default: 2025 },
    yearEnd: { type: Number, default: 2029 },
    showHeader: { type: Boolean, default: false },
});

/* ── Year / Quarter grid ─────────────────────────────── */

const yStart = computed(() => Number(props.yearStart) || 2025);
const yEnd = computed(() => {
    const e = Number(props.yearEnd) || 2029;
    return e >= yStart.value ? e : yStart.value;
});

const years = computed(() =>
    Array.from({ length: yEnd.value - yStart.value + 1 }, (_, i) => yStart.value + i)
);

const totalQuarters = computed(() => years.value.length * 4);

/* ── Date helpers ────────────────────────────────────── */

const parseDateMeta = (value) => {
    if (!value) return null;
    const m = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (m) return { year: +m[1], monthIndex: +m[2] - 1 };
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return null;
    return { year: d.getUTCFullYear(), monthIndex: d.getUTCMonth() };
};

const toQuarterIndex = (dateValue) => {
    const meta = parseDateMeta(dateValue);
    if (!meta || totalQuarters.value === 0) return null;
    const raw = (meta.year - yStart.value) * 4 + Math.floor(meta.monthIndex / 3);
    return Math.max(0, Math.min(raw, totalQuarters.value - 1));
};

const TYPE_BLOCK = 1;
const TYPE_DASHED = 2;

const normalizeMilestoneType = (value) => {
    const raw = Number(value);
    return raw === TYPE_DASHED || raw === 4 ? TYPE_DASHED : TYPE_BLOCK;
};

/* ── Compute combined milestone range ────────────────── */

const milestoneRange = computed(() => {
    const milestones = props.project?.milestones ?? [];
    if (milestones.length === 0) return { solid: null, dashed: null };

    let sMin = totalQuarters.value, sMax = -1;
    let dMin = totalQuarters.value, dMax = -1;

    for (const ms of milestones) {
        const type = normalizeMilestoneType(ms.milestone_type ?? ms.type);
        const si = toQuarterIndex(ms.start_date ?? ms.end_date);
        const ei = toQuarterIndex(ms.end_date ?? ms.start_date);

        if (si === null && ei === null) continue;

        const startIdx = si ?? ei;
        const endIdx = ei ?? si;

        if (type === TYPE_BLOCK) {
            sMin = Math.min(sMin, startIdx);
            sMax = Math.max(sMax, endIdx);
        } else {
            dMin = Math.min(dMin, startIdx);
            dMax = Math.max(dMax, endIdx);
        }
    }

    return {
        solid: sMin <= sMax ? { start: sMin, end: sMax } : null,
        dashed: dMin <= dMax ? { start: dMin, end: dMax } : null,
    };
});

/* ── Bar as percentage ──────────────────────────────── */

const barStyle = computed(() => {
    const range = milestoneRange.value.solid;
    if (!range || totalQuarters.value === 0) return null;
    const left = (range.start / totalQuarters.value) * 100;
    const width = ((range.end - range.start + 1) / totalQuarters.value) * 100;
    return { left: `${left}%`, width: `${width}%` };
});

const dashedBarStyle = computed(() => {
    const range = milestoneRange.value.dashed;
    if (!range || totalQuarters.value === 0) return null;
    const left = (range.start / totalQuarters.value) * 100;
    const width = ((range.end - range.start + 1) / totalQuarters.value) * 100;
    return { left: `${left}%`, width: `${width}%` };
});
</script>

<template>
    <div class="summary-wrapper">
        <!-- Header Row (Optional) -->
        <div v-if="showHeader" class="summary-header">
            <div class="col-no header-cell">Code</div>
            <div class="col-name header-cell">Initiative</div>
            <div class="col-timeline header-cell" style="justify-content: center;">Project Charter Time Line</div>
        </div>

        <div class="summary-row summary-row--readonly" :class="{ 'has-header': showHeader }">
            <!-- No -->
            <div class="col-no">
                <span class="seq-badge">{{ sequence ?? '' }}</span>
            </div>

            <!-- Name -->
            <div class="col-name">
                <span class="project-name">{{ project.name || '-' }}</span>
            </div>

            <!-- Timeline grid -->
            <div class="col-timeline col-timeline-readonly">
                <!-- Year headers -->
                <div class="year-row">
                    <div v-for="year in years" :key="`y-${year}`" class="year-cell">{{ year }}</div>
                </div>

                <!-- Bar track -->
                <div class="bar-track">
                    <!-- Year divider lines -->
                    <span v-for="(year, yi) in years" :key="`div-${year}`" class="year-divider"
                        :style="{ left: `${((yi + 1) / years.length) * 100}%` }"></span>

                    <!-- The dashed bar (painted first, under the solid block if overlapping) -->
                    <div v-if="dashedBarStyle" class="bar-dashed" :style="dashedBarStyle"></div>

                    <!-- The solid bar -->
                    <div v-if="barStyle" class="bar-fill" :style="barStyle"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.summary-wrapper {
    display: flex;
    flex-direction: column;
}

.summary-header {
    display: flex;
    align-items: stretch;
    background: #f1f5f9;
    border: 1px solid #d0dce8;
    border-bottom: none;
    border-radius: 6px 6px 0 0;
    min-height: 28px;
}

.header-cell {
    display: flex;
    align-items: center;
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    padding: 4px 6px;
}

.header-cell.col-timeline {
    border-right: none;
}

.summary-row {
    display: flex;
    align-items: stretch;
    border: 1px solid #d0dce8;
    background: #ffffff;
    border-radius: 6px;
    min-height: 38px;
    font-family: "Segoe UI", Arial, sans-serif;
}

.summary-row.has-header {
    border-radius: 0 0 6px 6px;
}

/* ── No column ─── */
.col-no {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    flex-shrink: 0;
    border-right: 1px solid #d0dce8;
}

.seq-badge {
    font-size: 10px;
    font-weight: 700;
    color: #1c75bc;
}

/* ── Name column ─── */
.col-name {
    display: flex;
    align-items: center;
    width: 28%;
    flex-shrink: 0;
    padding: 0 12px;
    border-right: 1px solid #d0dce8;
    min-width: 0;
}

.project-name {
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

/* ── Timeline column ─── */
.col-timeline {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 4px 6px 4px 0;
    min-width: 0;
    overflow: hidden;
}

.col-timeline-readonly {
    border-radius: 0 6px 6px 0;
}

.year-row {
    display: flex;
    padding: 0 2px;
}

.year-cell {
    flex: 1;
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    line-height: 1;
    padding-bottom: 3px;
}

.bar-track {
    position: relative;
    height: 12px;
    margin: 0 2px;
    background: #f1f5f9;
    border-radius: 3px;
}

.year-divider {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 1px;
    background: #cbd5e1;
    pointer-events: none;
}

.year-divider:last-of-type {
    display: none;
}

.bar-fill {
    position: absolute;
    top: 1px;
    bottom: 1px;
    border-radius: 2px;
    background: linear-gradient(90deg, #1c75bc, #2b87cc);
    transition: width 0.25s ease, left 0.25s ease;
    z-index: 2;
}

.bar-dashed {
    position: absolute;
    top: 50%;
    height: 0;
    border-top: 2px dashed #1c75bc;
    opacity: 0.8;
    transform: translateY(-50%);
    transition: width 0.25s ease, left 0.25s ease;
    z-index: 1;
}
</style>
