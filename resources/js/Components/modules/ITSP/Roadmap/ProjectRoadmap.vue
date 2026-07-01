<script setup>
import { computed } from 'vue';

const props = defineProps({
    project:   { type: Object, required: true },
    form:      { type: Object, required: true },
    sequence:  { type: [Number, String], default: null },
    yearStart: { type: Number, default: 2025 },
    yearEnd:   { type: Number, default: 2029 },
    selectedRoadmapVersionId: { type: [Number, String], default: null },
    milestoneTypeOptions: { type: Array, default: () => [] },
});

/* ── Year / Quarter grid ─────────────────────────────── */

const yStart = computed(() => Number(props.yearStart) || 2025);
const yEnd   = computed(() => {
    const e = Number(props.yearEnd) || 2029;
    return e >= yStart.value ? e : yStart.value;
});

const years = computed(() =>
    Array.from({ length: yEnd.value - yStart.value + 1 }, (_, i) => yStart.value + i)
);

const quarterCells = computed(() =>
    years.value.flatMap((year) => [1, 2, 3, 4].map((quarter) => ({ year, quarter })))
);

const totalCells = computed(() => quarterCells.value.length);

const TYPE_BLOCK = 1;
const TYPE_DASHED = 2;

const normalizeMilestoneType = (value) => {
    const raw = Number(value);
    return raw === TYPE_DASHED || raw === 4 ? TYPE_DASHED : TYPE_BLOCK;
};

const timelineStyleByType = (typeCode) => {
    return normalizeMilestoneType(typeCode) === TYPE_DASHED ? 'dashed' : 'block';
};

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
    if (!meta || totalCells.value === 0) return null;
    const raw = (meta.year - yStart.value) * 4 + Math.floor(meta.monthIndex / 3);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
};

const toLineItems = (value) =>
    String(value || '').split(/\r?\n/).map((l) => l.trim()).filter(Boolean);

/* ── Milestones → rows ───────────────────────────────── */

const objectives = computed(() =>
    String(props.form?.objectives || '').split(/\r?\n/).map((l) => l.trim()).filter(Boolean)
);

const normalizeVersionKey = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === 'v') {
        return 'v1';
    }

    if (/^v\d+$/.test(lower)) {
        return `v${Math.max(Number(lower.slice(1)) || 1, 1)}`;
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return lower;
};

const toDisplayVersionLabel = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === 'v') {
        return 'v1';
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return raw;
};

const selectedRoadmapVersionKey = computed(() => {
    if (props.selectedRoadmapVersionId === null || props.selectedRoadmapVersionId === undefined || String(props.selectedRoadmapVersionId).trim() === '') {
        return null;
    }

    return normalizeVersionKey(props.selectedRoadmapVersionId);
});

const availableVersions = computed(() => {
    const labels = new Map();
    const registerVersion = (value, displayLabel = null) => {
        const key = normalizeVersionKey(value);
        const fallbackLabel = displayLabel || toDisplayVersionLabel(value);

        if (!labels.has(key)) {
            labels.set(key, fallbackLabel);
            return;
        }

        if (displayLabel) {
            labels.set(key, displayLabel);
        }
    };
    const milestones = props.project?.milestones ?? [];
    const projectCharters = Array.isArray(props.project?.charters) ? props.project.charters : [];

    projectCharters.forEach((charter) => {
        const statusLabel = charter?.resolved_status_label || charter?.status_label;
        registerVersion(charter?.version_label, statusLabel);
    });

    if (props.project?.charter?.version_label) {
        const charter = props.project.charter;
        const statusLabel = charter?.resolved_status_label || charter?.status_label;
        registerVersion(charter.version_label, statusLabel);
    }

    milestones.forEach((milestone) => {
        registerVersion(milestone?.version);
    });

    if (labels.size === 0) {
        registerVersion('v1');
    }

    return Array.from(labels.entries())
        .map(([key, label]) => ({ key, label }))
        .sort((a, b) => {
        const numA = Number(a.key.replace(/\D/g, '')) || 0;
        const numB = Number(b.key.replace(/\D/g, '')) || 0;
        return numA - numB; // Ascending (v1, v2)
    });
});

const versionsToRender = computed(() => {
    // If a specific version is selected, only render that one.
    // Otherwise, render all available versions.
    const selected = selectedRoadmapVersionKey.value;
    if (selected) {
        const selectedVersion = availableVersions.value.find((version) => version.key === selected);
        return selectedVersion ? [selectedVersion] : availableVersions.value;
    }
    return availableVersions.value;
});

const getMilestonesForVersion = (versionKey) => {
    return [...(props.project?.milestones ?? [])]
        .filter((item) => normalizeVersionKey(item.version) === versionKey)
        .sort((a, b) => {
            const od = (Number(a.order ?? 0)) - (Number(b.order ?? 0));
            return od !== 0 ? od : String(a.start_date ?? '').localeCompare(String(b.start_date ?? ''));
        });
};

const getObjectivesForVersion = (versionKey) => {
    const charters = Array.isArray(props.project?.charters) ? props.project.charters : [];
    const matchedCharter = charters.find(
        (charter) => normalizeVersionKey(charter?.version_label) === versionKey
    );

    if (matchedCharter) {
        return String(matchedCharter.objectives || '')
            .split(/\r?\n/)
            .map((line) => line.trim())
            .filter(Boolean);
    }

    if (normalizeVersionKey(props.project?.charter?.version_label) === versionKey) {
        return String(props.project?.charter?.objectives || '')
            .split(/\r?\n/)
            .map((line) => line.trim())
            .filter(Boolean);
    }

    return objectives.value;
};

const buildRowsFromMilestones = (milestones) => {
    const sections = new Map();
    for (const ms of milestones) {
        const sectionLabel = String(ms.type || 'Roadmap Activity').trim() || 'Roadmap Activity';
        const typeCode = normalizeMilestoneType(ms.milestone_type ?? ms.type);
        const si     = toQuarterIndex(ms.start_date ?? ms.end_date);
        const ei     = toQuarterIndex(ms.end_date   ?? ms.start_date);
        const hasTL  = Boolean(ms.start_date || ms.end_date) && si !== null && ei !== null;
        if (!sections.has(sectionLabel)) sections.set(sectionLabel, []);
        sections.get(sectionLabel).push({
            activity:    ms.title || '-',
            output:      toLineItems(ms.output),
            hasTimeline: hasTL,
            start:       hasTL ? Math.min(si, ei) : null,
            end:         hasTL ? Math.max(si, ei) : null,
            timelineStyle: timelineStyleByType(typeCode),
        });
    }
    return [...sections.entries()].map(([label, rows]) => ({ label, rows }));
};

const versionedRoadmaps = computed(() => {
    return versionsToRender.value
        .map((version) => {
            const milestones = getMilestonesForVersion(version.key);
            if (milestones.length === 0) {
                return null;
            }

            const sections = buildRowsFromMilestones(milestones);

            // Determine status color class for the badge
            let statusClass = 'bg-slate-100 text-slate-700 border-slate-300';
            const labelLower = String(version.label).toLowerCase();
            if (labelLower.includes('draft')) statusClass = 'bg-slate-100 text-slate-600 border-slate-300';
            else if (labelLower.includes('propose')) statusClass = 'bg-blue-100 text-blue-700 border-blue-300';
            else if (labelLower.includes('review')) statusClass = 'bg-amber-100 text-amber-700 border-amber-300';
            else if (labelLower.includes('approve')) statusClass = 'bg-emerald-100 text-emerald-700 border-emerald-300';
            else if (labelLower.includes('baseline')) statusClass = 'bg-purple-100 text-purple-700 border-purple-300';

            return {
                versionKey: version.key,
                versionLabel: version.label,
                statusClass,
                sections,
            };
        })
        .filter(Boolean);
});

const isActive = (row, idx) => row.hasTimeline && idx >= row.start && idx <= row.end;

const timelineCellClass = (row, quarterIndex, cell) => ({
    'cell-tl-active-block': isActive(row, quarterIndex) && row.timelineStyle !== 'dashed',
    'cell-tl-active-dashed': isActive(row, quarterIndex) && row.timelineStyle === 'dashed',
    'border-r-blue': cell.quarter === 4,
});
</script>

<template>
    <div v-if="versionedRoadmaps.length > 0" class="roadmap-wrap overflow-x-auto">
        <div v-for="roadmap in versionedRoadmaps" :key="`rm-v-${roadmap.versionKey}`" class="roadmap-version-block">
            <table class="roadmap-table" :style="{ '--qcount': Math.max(totalCells, 1) }">
                <colgroup>
                    <col class="col-no">
                    <col class="col-initiative">
                    <col v-for="(_, i) in quarterCells" :key="`cq-${roadmap.versionKey}-${i}`" class="col-quarter">
                    <col class="col-output">
                </colgroup>

                <!-- ── HEAD ── -->
                <thead>
                    <tr>
                        <th rowspan="2" class="th">No</th>
                        <th rowspan="2" class="th th-left">IT Initiative Roadmap {{ yStart }}–{{ yEnd }}</th>
                        <th v-for="year in years" :key="`y-${roadmap.versionKey}-${year}`" colspan="4" class="th th-year">{{ year }}</th>
                        <th rowspan="2" class="th">Output</th>
                    </tr>
                    <tr>
                        <th
                            v-for="(cell, i) in quarterCells"
                            :key="`qh-${roadmap.versionKey}-${i}`"
                            class="th-q"
                            :class="{ 'border-r-blue': cell.quarter === 4 }"
                        >Q{{ cell.quarter }}</th>
                    </tr>
                </thead>

                <!-- ── BODY ── -->
                <tbody>
                    <!-- Project name row -->
                    <tr class="row-project">
                        <td class="cell-no">{{ sequence ?? '' }}</td>
                        <td class="cell-project-name">
                            {{ project.name || '-' }} 
                            <span v-if="roadmap.versionLabel" class="version-badge" :class="roadmap.statusClass">
                                {{ roadmap.versionLabel }}
                            </span>
                        </td>
                        <td
                            v-for="(cell, i) in quarterCells" :key="`pg-${roadmap.versionKey}-${i}`"
                            class="cell-tl"
                            :class="{ 'border-r-blue': cell.quarter === 4 }"
                        ></td>
                        <td class="cell-output">–</td>
                    </tr>

                    <!-- Sections & activities -->
                    <template v-for="(section, si) in roadmap.sections" :key="`sec-${roadmap.versionKey}-${si}`">
                        <!-- Section header row -->
                        <tr class="row-section">
                            <td class="cell-no"></td>
                            <td class="cell-section">{{ section.label }}</td>
                            <td
                                v-for="(cell, i) in quarterCells" :key="`sg-${roadmap.versionKey}-${si}-${i}`"
                                class="cell-section-gap"
                                :class="{ 'border-r-blue': cell.quarter === 4 }"
                            ></td>
                            <td class="cell-section-gap"></td>
                        </tr>

                        <!-- Activity rows -->
                        <tr v-for="(row, ri) in section.rows" :key="`row-${roadmap.versionKey}-${si}-${ri}`" class="row-data">
                            <td class="cell-no"></td>
                            <td class="cell-activity">{{ row.activity }}</td>
                            <td
                                v-for="(cell, i) in quarterCells" :key="`tl-${roadmap.versionKey}-${si}-${ri}-${i}`"
                                class="cell-tl"
                                :class="timelineCellClass(row, i, cell)"
                            ></td>
                            <td class="cell-output">
                                <ul v-if="row.output.length" class="output-list">
                                    <li v-for="(item, oi) in row.output" :key="`out-${roadmap.versionKey}-${si}-${ri}-${oi}`">{{ item }}</li>
                                </ul>
                                <span v-else>–</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
/* ── Variables ─────────────────────────────────── */
.roadmap-wrap {
    --blue:   #1c75bc;
    --blue-lt: #e2f0fb;
    --grid:   #b9d1e8;
    --text:   #0f172a;
    --text-sm: #111827;
    --bg:     #ffffff;
    --bg-row: #f9fbff;
    --active: #000000;
    font-family: "Segoe UI", Arial, sans-serif;
}

.roadmap-version-block + .roadmap-version-block {
    margin-top: 24px;
}

/* ── Table shell ────────────────────────────────── */
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

/* ── Column widths ──────────────────────────────── */
.col-no         { width: 3%; }
.col-initiative { width: 28%; }
.col-quarter    { width: calc(44% / var(--qcount)); }
.col-output     { width: 25%; }

/* ── Header cells ───────────────────────────────── */
.th {
    background: var(--blue);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 5px 6px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.2;
}
.th-left { text-align: left; }
.th-year { text-align: center; }

.th-q {
    background: var(--blue-lt);
    color: var(--blue);
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    padding: 2px 0;
    border-top: 1px solid var(--grid);
}

/* ── Year-end divider ───────────────────────────── */
.border-r-blue { border-right: 2px solid var(--blue) !important; }

/* ── Row types ──────────────────────────────────── */
.row-project  { background: var(--bg); }
.row-section  { background: var(--blue-lt); }
.row-data     { background: var(--bg-row); }

/* ── Data cells ─────────────────────────────────── */
.cell-no {
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    color: var(--text-sm);
    padding: 3px 4px;
    vertical-align: top;
}

.cell-project-name {
    font-size: 12px;
    font-weight: 800;
    color: var(--text);
    padding: 5px 8px;
    vertical-align: middle;
    line-height: 1.3;
}

.version-badge {
    display: inline-flex;
    align-items: center;
    margin-left: 8px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
    letter-spacing: 0.01em;
    line-height: 1.15;
    vertical-align: middle;
}

.cell-section {
    font-size: 10px;
    font-weight: 700;
    color: #000000;
    padding: 3px 8px 3px 16px;
    border-left: 3px solid #000000;
    line-height: 1.2;
    vertical-align: middle;
}

.cell-activity {
    font-size: 11px;
    color: #000000;
    padding: 3px 8px 3px 24px;
    line-height: 1.3;
    vertical-align: top;
}

.cell-section-gap { height: 14px; padding: 0; }

/* ── Timeline cells ─────────────────────────────── */
.cell-tl {
    height: 20px;
    padding: 0;
    background: var(--bg-row);
}
.cell-tl-active-block { background: var(--active); }

.cell-tl-active-dashed {
    background-color: var(--bg-row);
    background-image: repeating-linear-gradient(
        90deg,
        #000000 0 6px,
        transparent 6px 10px
    );
    background-position: center;
    background-repeat: no-repeat;
    background-size: 100% 2px;
}

/* ── Output cell ────────────────────────────────── */
.cell-output {
    font-size: 10px;
    color: #000000;
    padding: 4px 6px;
    vertical-align: top;
    line-height: 1.3;
    background: var(--bg-row);
}

.output-list {
    margin: 0;
    padding-left: 14px;
}
.output-list li + li { margin-top: 2px; }
</style>
