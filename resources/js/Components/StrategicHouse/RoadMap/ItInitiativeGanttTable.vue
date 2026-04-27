<script setup>
import { computed, ref, watch } from "vue";

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
const TYPE_DASHED = new Set([2, 4]);

const selectedProjectLeader = ref("");
const selectedProjectOwner = ref("");

function normalizePersonLabel(rawValue) {
    return String(rawValue ?? "").trim();
}

function getProjectPersonnelEntries(initiative) {
    const projects = Array.isArray(initiative?.projects) ? initiative.projects : [];

    const fromProjects = projects
        .map((project) => ({
            owner: normalizePersonLabel(project?.owner ?? project?.charter?.owner),
            leader: normalizePersonLabel(project?.leader ?? project?.charter?.leader),
        }))
        .filter((item) => item.owner !== "" || item.leader !== "");

    if (fromProjects.length > 0) {
        return fromProjects;
    }

    const fallbackOwner = normalizePersonLabel(
        initiative?.owner ?? initiative?.charter?.owner,
    );
    const fallbackLeader = normalizePersonLabel(
        initiative?.leader ?? initiative?.charter?.leader,
    );

    return fallbackOwner || fallbackLeader
        ? [{ owner: fallbackOwner, leader: fallbackLeader }]
        : [];
}

const allInitiatives = computed(() =>
    (Array.isArray(props.groups) ? props.groups : []).flatMap((group) =>
        Array.isArray(group?.initiatives) ? group.initiatives : [],
    ),
);

const availableProjectLeaders = computed(() => {
    const leaders = new Set();

    allInitiatives.value.forEach((initiative) => {
        getProjectPersonnelEntries(initiative).forEach((entry) => {
            if (entry.leader !== "") {
                leaders.add(entry.leader);
            }
        });
    });

    return Array.from(leaders).sort((left, right) => left.localeCompare(right));
});

const availableProjectOwners = computed(() => {
    const owners = new Set();

    allInitiatives.value.forEach((initiative) => {
        getProjectPersonnelEntries(initiative).forEach((entry) => {
            if (entry.owner !== "") {
                owners.add(entry.owner);
            }
        });
    });

    return Array.from(owners).sort((left, right) => left.localeCompare(right));
});

watch(
    availableProjectLeaders,
    (leaders) => {
        if (!Array.isArray(leaders) || leaders.length === 0) {
            selectedProjectLeader.value = "";
            return;
        }

        if (!leaders.includes(selectedProjectLeader.value)) {
            selectedProjectLeader.value = "";
        }
    },
    { immediate: true },
);

watch(
    availableProjectOwners,
    (owners) => {
        if (!Array.isArray(owners) || owners.length === 0) {
            selectedProjectOwner.value = "";
            return;
        }

        if (!owners.includes(selectedProjectOwner.value)) {
            selectedProjectOwner.value = "";
        }
    },
    { immediate: true },
);

const filteredGroups = computed(() =>
    (Array.isArray(props.groups) ? props.groups : [])
        .map((group) => {
            const initiatives = (Array.isArray(group?.initiatives) ? group.initiatives : []).filter((initiative) => {
                if (!selectedProjectLeader.value && !selectedProjectOwner.value) {
                    return true;
                }

                return getProjectPersonnelEntries(initiative).some((entry) => {
                    const matchesLeader =
                        !selectedProjectLeader.value ||
                        entry.leader === selectedProjectLeader.value;
                    const matchesOwner =
                        !selectedProjectOwner.value ||
                        entry.owner === selectedProjectOwner.value;

                    return matchesLeader && matchesOwner;
                });
            });

            return initiatives.length > 0
                ? {
                    ...group,
                    initiatives,
                }
                : null;
        })
        .filter(Boolean),
);

function parseDateMeta(value) {
    if (!value) return null;

    const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (match) {
        return {
            year: Number(match[1]),
            monthIndex: Number(match[2]) - 1,
        };
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return null;
    }

    return {
        year: parsed.getUTCFullYear(),
        monthIndex: parsed.getUTCMonth(),
    };
}

function toQuarterIndexFromDate(value) {
    const meta = parseDateMeta(value);
    if (!meta) return null;

    const raw = (meta.year - props.startYear) * 4 + Math.floor(meta.monthIndex / 3);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

function toLegacyQuarterIndex(yearValue, quarterValue) {
    const year = Number(yearValue);
    const quarter = parseInt(String(quarterValue ?? "").replace(/[Qq]/, ""), 10);

    if (!Number.isFinite(year) || !Number.isFinite(quarter) || quarter < 1 || quarter > 4) {
        return null;
    }

    const raw = (year - props.startYear) * 4 + (quarter - 1);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

function normalizeTimelineStyle(milestoneType) {
    return TYPE_DASHED.has(Number(milestoneType)) ? "dashed" : "block";
}

function buildTimelineCells(initiative) {
    const cells = Array.from({ length: totalCells.value }, (_, index) => ({
        key: `cell-${initiative?.id ?? "initiative"}-${index}`,
        hasBlock: false,
        hasDashed: false,
        endsYear: (index + 1) % 4 === 0,
    }));

    const projects = Array.isArray(initiative?.projects) ? initiative.projects : [];
    const milestones = projects.flatMap((project) =>
        Array.isArray(project?.milestones) ? project.milestones : [],
    );

    if (milestones.length > 0) {
        milestones.forEach((milestone) => {
            const startIndex = toQuarterIndexFromDate(
                milestone?.start_date ?? milestone?.end_date,
            );
            const endIndex = toQuarterIndexFromDate(
                milestone?.end_date ?? milestone?.start_date,
            );

            if (startIndex === null && endIndex === null) {
                return;
            }

            const safeStart = Math.min(startIndex ?? endIndex, endIndex ?? startIndex);
            const safeEnd = Math.max(startIndex ?? endIndex, endIndex ?? startIndex);
            const timelineStyle = normalizeTimelineStyle(
                milestone?.milestone_type ?? milestone?.type,
            );

            for (let index = safeStart; index <= safeEnd; index += 1) {
                if (timelineStyle === "dashed") {
                    cells[index].hasDashed = true;
                } else {
                    cells[index].hasBlock = true;
                }
            }
        });

        return cells;
    }

    const legacyStart = toLegacyQuarterIndex(initiative?.startYear, initiative?.startQ);
    const legacyEnd = toLegacyQuarterIndex(initiative?.endYear, initiative?.endQ);

    if (legacyStart === null && legacyEnd === null) {
        return cells;
    }

    const safeStart = Math.min(legacyStart ?? legacyEnd, legacyEnd ?? legacyStart);
    const safeEnd = Math.max(legacyStart ?? legacyEnd, legacyEnd ?? legacyStart);

    for (let index = safeStart; index <= safeEnd; index += 1) {
        cells[index].hasBlock = true;
    }

    return cells;
}
</script>

<template>
    <div class="gantt-wrap">
        <div class="gantt-filters">
            <label class="gantt-filter-field">
                <span class="gantt-filter-label">Project Leader</span>
                <select v-model="selectedProjectLeader" class="gantt-filter-select">
                    <option value="">Semua Project Leader</option>
                    <option
                        v-for="leader in availableProjectLeaders"
                        :key="`filter-project-leader-${leader}`"
                        :value="leader"
                    >
                        {{ leader }}
                    </option>
                </select>
            </label>

            <label class="gantt-filter-field">
                <span class="gantt-filter-label">Project Owner</span>
                <select v-model="selectedProjectOwner" class="gantt-filter-select">
                    <option value="">Semua Project Owner</option>
                    <option
                        v-for="owner in availableProjectOwners"
                        :key="`filter-project-owner-${owner}`"
                        :value="owner"
                    >
                        {{ owner }}
                    </option>
                </select>
            </label>
        </div>

        <!-- Empty state -->
        <div v-if="!filteredGroups || filteredGroups.length === 0" class="gantt-empty">
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
                    v-for="group in filteredGroups"
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

                        <!-- Quarter cells -->
                        <td
                            v-for="cell in buildTimelineCells(initiative)"
                            :key="cell.key"
                            :class="[
                                'cell-quarter-timeline',
                                cell.hasBlock ? 'cell-bar' : 'cell-gap',
                                cell.hasDashed ? 'cell-dashed' : '',
                                { 'border-r-navy': cell.endsYear },
                            ]"
                        >
                            <span
                                v-if="cell.hasDashed"
                                class="cell-dashed-line"
                            />
                        </td>
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

.gantt-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 14px;
}

.gantt-filter-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 180px;
}

.gantt-filter-label {
    color: var(--text-muted);
    font-size: 11px;
    font-weight: 600;
}

.gantt-filter-select {
    border: 1px solid var(--grid);
    border-radius: 8px;
    background: var(--bg);
    color: var(--text);
    font-size: 12px;
    padding: 8px 10px;
    min-height: 34px;
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
.cell-gap,
.cell-quarter-timeline {
    height: 26px;
    padding: 0;
    background: inherit;
    position: relative;
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

.gantt-table td.cell-dashed {
    background: var(--bg);
}

.cell-dashed-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    border-top: 2px dashed var(--navy-mid);
    transform: translateY(-50%);
    z-index: 2;
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
