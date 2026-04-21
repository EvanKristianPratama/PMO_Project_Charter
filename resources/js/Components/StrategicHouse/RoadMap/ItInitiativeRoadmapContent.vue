<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
    groups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2025 },
    endYear: { type: Number, default: 2029 },
    totalCount: { type: Number, default: 0 },
    milestoneTypeOptions: { type: Array, default: () => [] },
});

const roadmapLegendItems = [
    { key: "baseline", label: "Baseline" },
    { key: "approved", label: "Approved" },
];
const statusLegendOrder = [
    "On Track",
    "At Risk",
    "Delayed",
    "Done",
    "Not Started",
    "Not Signed",
    "On Review",
];
const visibleRoadmapLayers = ref(["baseline", "approved"]);
const selectedReviewStatus = ref("Total");
const selectedPeriod = ref("");
const monthsOrder = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];

/* ── Year / Quarter grid ─────────────────────────────── */
const years = computed(() =>
    Array.from(
        { length: props.endYear - props.startYear + 1 },
        (_, i) => props.startYear + i,
    ),
);

const quarterCells = computed(() =>
    years.value.flatMap((y) =>
        [1, 2, 3, 4].map((q) => ({ year: y, quarter: q })),
    ),
);

const totalCells = computed(() => quarterCells.value.length);

/* ── Date → quarter index ────────────────────────────── */
function toQIdx(value) {
    if (!value) return null;
    const m = String(value).match(/^(\d{4})-(\d{2})/);
    if (!m) return null;
    const year = parseInt(m[1], 10);
    const month = parseInt(m[2], 10) - 1;
    const raw = (year - props.startYear) * 4 + Math.floor(month / 3);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

function normalizeRoadmapStatus(rawStatus) {
    const normalized = String(rawStatus ?? "").trim().toLowerCase();

    if (!normalized) return "";
    if (normalized.includes("baseline")) return "baseline";
    if (["approve", "approved", "aproved"].includes(normalized)) {
        return "approved";
    }
    if (normalized.includes("approve")) return "approved";

    return normalized;
}

function isRoadmapLayerVisible(layerKey) {
    return visibleRoadmapLayers.value.includes(layerKey);
}

function toggleRoadmapLayer(layerKey) {
    if (visibleRoadmapLayers.value.includes(layerKey)) {
        if (visibleRoadmapLayers.value.length === 1) {
            return;
        }

        visibleRoadmapLayers.value = visibleRoadmapLayers.value.filter(
            (item) => item !== layerKey,
        );
        return;
    }

    visibleRoadmapLayers.value = roadmapLegendItems
        .map((item) => item.key)
        .filter(
            (itemKey) =>
                itemKey === layerKey
                || visibleRoadmapLayers.value.includes(itemKey),
        );
}

function isSelectedReviewStatus(status) {
    return selectedReviewStatus.value === status;
}

function toggleReviewStatus(status) {
    if (status === "Total" || selectedReviewStatus.value === status) {
        selectedReviewStatus.value = "Total";
        return;
    }

    selectedReviewStatus.value = status;
}

/* ── Compute bar range ───────────────────────────────── */
function getRange(projects, statusFilter) {
    if (!Array.isArray(projects) || projects.length === 0) return null;

    let pool = projects;

    if (statusFilter) {
        pool = projects.filter((project) => {
            const statusKey = normalizeRoadmapStatus(
                project?.status_ref?.name ?? project?.status,
            );

            return statusKey === statusFilter;
        });
    }

    if (pool.length === 0) return null;

    const milestones = pool.flatMap((p) =>
        Array.isArray(p.milestones) ? p.milestones : [],
    );
    if (milestones.length === 0) return null;

    let minS = null;
    let maxE = null;

    for (const ms of milestones) {
        const si = toQIdx(ms.start_date ?? ms.end_date);
        const ei = toQIdx(ms.end_date ?? ms.start_date);
        if (si !== null && (minS === null || si < minS)) minS = si;
        if (ei !== null && (maxE === null || ei > maxE)) maxE = ei;
    }

    if (minS === null && maxE === null) return null;
    const s = minS ?? maxE;
    const e = maxE ?? minS;
    return { start: Math.min(s, e), end: Math.max(s, e) };
}

/* ── Build cell descriptors ──────────────────────────── */
function buildCells(range, keyPrefix = "default") {
    const total = totalCells.value;

    if (!range) {
        return Array.from({ length: total }, (_, i) => ({
            type: "gap",
            span: 1,
            key: `${keyPrefix}-g${i}`,
            endsYear: (i + 1) % 4 === 0,
        }));
    }

    const { start, end } = range;
    const safeStart = Math.max(0, Math.min(start, total - 1));
    const safeEnd = Math.max(safeStart, Math.min(end, total - 1));
    const cells = [];
    let c = 0;

    while (c < safeStart) {
        cells.push({
            type: "gap",
            span: 1,
            key: `${keyPrefix}-g${c}`,
            endsYear: (c + 1) % 4 === 0,
        });
        c++;
    }

    cells.push({
        type: "bar",
        span: safeEnd - safeStart + 1,
        key: `${keyPrefix}-bar-${safeStart}`,
        endsYear: (safeEnd + 1) % 4 === 0,
    });
    c = safeEnd + 1;

    while (c < total) {
        cells.push({
            type: "gap",
            span: 1,
            key: `${keyPrefix}-g${c}`,
            endsYear: (c + 1) % 4 === 0,
        });
        c++;
    }

    return cells;
}

function buildInitiativeTimelineRows(initiative) {
    const initiativeId = initiative?.id ?? "initiative";
    const projects = Array.isArray(initiative?.projects) ? initiative.projects : [];

    const rows = roadmapLegendItems
        .filter((item) => isRoadmapLayerVisible(item.key))
        .map((item) => {
            const range = getRange(projects, item.key);

            if (!range) {
                return null;
            }

            return {
                key: `${initiativeId}-${item.key}`,
                layerKey: item.key,
                label: item.label,
                isPlaceholder: false,
                cells: buildCells(range, `${initiativeId}-${item.key}`),
            };
        })
        .filter(Boolean);

    if (rows.length > 0) {
        return rows;
    }

    return [
        {
            key: `${initiativeId}-empty`,
            layerKey: "empty",
            label: "",
            isPlaceholder: true,
            cells: buildCells(null, `${initiativeId}-empty`),
        },
    ];
}

const allInitiatives = computed(() =>
    (Array.isArray(props.groups) ? props.groups : []).flatMap((group) =>
        Array.isArray(group?.initiatives) ? group.initiatives : [],
    ),
);

const availablePeriods = computed(() => {
    const periods = new Map();

    allInitiatives.value.forEach((initiative) => {
        const reviewStatuses = Array.isArray(initiative?.review_statuses)
            ? initiative.review_statuses
            : [];

        reviewStatuses.forEach((statusItem) => {
            const value = String(statusItem?.period_key ?? "").trim();
            const label = String(statusItem?.periode_label ?? "").trim();
            const year = Number(statusItem?.year ?? 0);
            const startMonth = String(statusItem?.start ?? "").trim();
            const endMonth = String(statusItem?.end ?? "").trim();

            if (!value || !label || periods.has(value)) {
                return;
            }

            periods.set(value, {
                value,
                label,
                year,
                startMonth,
                endMonth,
            });
        });
    });

    const sortedPeriods = Array.from(periods.values()).sort((left, right) => {
        if (left.year !== right.year) {
            return right.year - left.year;
        }

        const leftStartIndex = monthsOrder.indexOf(left.startMonth);
        const rightStartIndex = monthsOrder.indexOf(right.startMonth);
        if (leftStartIndex !== rightStartIndex) {
            return rightStartIndex - leftStartIndex;
        }

        const leftEndIndex = monthsOrder.indexOf(left.endMonth);
        const rightEndIndex = monthsOrder.indexOf(right.endMonth);
        return rightEndIndex - leftEndIndex;
    });

    return sortedPeriods;
});

watch(
    availablePeriods,
    (periods) => {
        if (!Array.isArray(periods) || periods.length === 0) {
            selectedPeriod.value = "";
            return;
        }

        const hasSelectedPeriod = periods.some(
            (period) => period.value === selectedPeriod.value,
        );

        if (!hasSelectedPeriod) {
            selectedPeriod.value = periods[0].value;
        }
    },
    { immediate: true },
);

function resolveInitiativeStatusByPeriod(initiative, periodValue) {
    const reviewStatuses = Array.isArray(initiative?.review_statuses)
        ? initiative.review_statuses
        : [];

    if (reviewStatuses.length === 0) {
        return {
            status: initiative?.implementation_status ?? null,
            period: null,
        };
    }

    let selected = null;
    for (let index = reviewStatuses.length - 1; index >= 0; index -= 1) {
        const statusItem = reviewStatuses[index];
        if (String(statusItem?.period_key ?? "") === String(periodValue)) {
            selected = statusItem;
            break;
        }
    }

    return {
        status: selected?.review_status ?? initiative?.implementation_status ?? null,
        period: selected?.periode_label ?? null,
    };
}

const baseDisplayGroups = computed(() =>
    (Array.isArray(props.groups) ? props.groups : []).map((group) => {
        const initiatives = (Array.isArray(group?.initiatives) ? group.initiatives : []).map((initiative) => {
            const periodState = resolveInitiativeStatusByPeriod(
                initiative,
                selectedPeriod.value,
            );
            const timelineRows = buildInitiativeTimelineRows(initiative);

            return {
                ...initiative,
                display_status: periodState.status,
                display_period: periodState.period,
                timeline_rows: timelineRows,
                timeline_rowspan: timelineRows.length,
            };
        });

        return {
            ...group,
            initiatives,
            timeline_rowspan: initiatives.reduce(
                (total, initiative) => total + (initiative.timeline_rowspan || 1),
                0,
            ),
        };
    }),
);

const baseDisplayInitiatives = computed(() =>
    baseDisplayGroups.value.flatMap((group) =>
        Array.isArray(group?.initiatives) ? group.initiatives : [],
    ),
);

const displayGroups = computed(() =>
    baseDisplayGroups.value
        .map((group) => {
            const initiatives = (Array.isArray(group?.initiatives) ? group.initiatives : [])
                .filter((initiative) => {
                    if (selectedReviewStatus.value === "Total") {
                        return true;
                    }

                    return normalizeStatusLabel(initiative?.display_status) === selectedReviewStatus.value;
                });

            if (initiatives.length === 0) {
                return null;
            }

            return {
                ...group,
                initiatives,
                timeline_rowspan: initiatives.reduce(
                    (total, initiative) => total + (initiative.timeline_rowspan || 1),
                    0,
                ),
            };
        })
        .filter(Boolean),
);

const hasDisplayGroups = computed(() => displayGroups.value.length > 0);

const selectedPeriodLabel = computed(() => {
    return (
        availablePeriods.value.find((period) => period.value === selectedPeriod.value)?.label
        ?? "-"
    );
});

const reviewStatusLegendItems = computed(() => {
    const counts = baseDisplayInitiatives.value.reduce((carry, initiative) => {
        const status = normalizeStatusLabel(initiative?.display_status);

        if (!status) {
            return carry;
        }

        carry.set(status, (carry.get(status) ?? 0) + 1);

        return carry;
    }, new Map());

    return [
        {
            label: "Total",
            status: "Total",
            count: baseDisplayInitiatives.value.length,
        },
        ...statusLegendOrder
            .map((status) => ({
                label: status,
                status,
                count: counts.get(status) ?? 0,
            }))
            .filter((item) => item.count > 0),
    ];
});

function normalizeStatusLabel(rawStatus) {
    const value = String(rawStatus ?? "").trim();
    if (!value) return "";

    const normalized = value.toLowerCase();

    if (normalized === "on track") return "On Track";
    if (normalized === "at risk") return "At Risk";
    if (normalized === "delayed") return "Delayed";
    if (normalized === "not started") return "Not Started";
    if (normalized === "not signed") return "Not Signed";
    if (normalized === "done" || normalized === "completed") return "Done";
    if (normalized === "on review") return "On Review";

    return value;
}

function badgeClass(status) {
    const normalized = normalizeStatusLabel(status);

    if (normalized === "On Track") return "badge--on-track";
    if (normalized === "At Risk") return "badge--at-risk";
    if (normalized === "Delayed") return "badge--delayed";
    if (normalized === "Not Started") return "badge--not-started";
    if (normalized === "Not Signed") return "badge--not-signed";
    if (normalized === "Done") return "badge--done";
    if (normalized === "On Review") return "badge--on-review";

    return "badge--default";
}
</script>

<template>
    <div class="space-y-3">
        <!-- ── Header ─────────────────────────────────── -->
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <label class="period-filter">
                    <span class="period-filter__label">Status Review Implementation Period</span>
                    <select v-model="selectedPeriod" class="period-filter__select">
                        <option
                            v-if="availablePeriods.length === 0"
                            value=""
                            disabled
                        >
                            Belum ada periode
                        </option>
                        <option
                            v-for="period in availablePeriods"
                            :key="`status-period-${period.value}`"
                            :value="period.value"
                        >
                            {{ period.label }}
                        </option>
                    </select>
                </label>
            </div>
        </div>

        <div
            v-if="groups && groups.length"
            class="legend-panel"
        >
            <div class="legend-panel__header">
                <div class="legend-panel__title">Legend</div>
                <div class="legend-panel__period">{{ selectedPeriodLabel }}</div>
            </div>

            <div class="legend-panel__section">
                <div class="legend-panel__subtitle">Roadmap</div>
                <div class="legend-list">
                    <button
                        v-for="item in roadmapLegendItems"
                        :key="`roadmap-legend-${item.key}`"
                        type="button"
                        :aria-pressed="isRoadmapLayerVisible(item.key)"
                        :class="[
                            'legend-item',
                            'legend-item--button',
                            !isRoadmapLayerVisible(item.key) ? 'legend-item--muted' : '',
                        ]"
                        @click="toggleRoadmapLayer(item.key)"
                    >
                        <span
                            :class="[
                                'legend-swatch',
                                `timeline-swatch--${item.key}`,
                            ]"
                        />
                        <span class="legend-label">{{ item.label }}</span>
                        <span class="legend-toggle">
                            {{ isRoadmapLayerVisible(item.key) ? "Shown" : "Hidden" }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="legend-panel__section">
                <div class="legend-panel__subtitle">Status Review</div>
                <div class="legend-list">
                    <button
                        v-for="item in reviewStatusLegendItems"
                        :key="`legend-${item.status}`"
                        type="button"
                        :aria-pressed="isSelectedReviewStatus(item.status)"
                        :class="[
                            'legend-item',
                            'legend-item--button',
                            isSelectedReviewStatus(item.status) ? 'legend-item--active' : '',
                        ]"
                        @click="toggleReviewStatus(item.status)"
                    >
                        <span
                            v-if="item.status !== 'Total'"
                            :class="[
                                'legend-swatch',
                                badgeClass(item.status),
                            ]"
                        />
                        <span class="legend-label">
                            {{ item.label }} <span class="legend-count">({{ item.count }})</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Empty state ─────────────────────────────── -->
        <div
            v-if="!groups || groups.length === 0"
            class="rounded-xl border border-dashed border-slate-200 dark:border-white/10 bg-white dark:bg-[#171717] p-10 text-center"
        >
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Belum ada data roadmap IT Strategic Initiative.
            </p>
        </div>

        <div
            v-else-if="!hasDisplayGroups"
            class="rounded-xl border border-dashed border-slate-200 dark:border-white/10 bg-white dark:bg-[#171717] p-10 text-center"
        >
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Tidak ada initiative yang cocok dengan filter status review yang dipilih.
            </p>
        </div>

        <!-- ── Gantt Table ─────────────────────────────── -->
        <div
            v-else
            class="overflow-x-auto border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171717]"
            style="min-width: 0"
        >
            <table
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

                <!-- Single header row — year labels only -->
                <thead>
                    <tr class="border-b border-[#c9d2dd]">
                        <th
                            class="th-cell bg-[#326eb2] text-white border-r border-white/30"
                        >
                            IT Building Blocks
                        </th>
                        <th
                            class="th-cell th-left bg-[#326eb2] text-white border-r border-white/30"
                        >
                            IT Initiatives
                        </th>
                        <th
                            v-for="year in years"
                            :key="`yr-${year}`"
                            colspan="4"
                            class="th-year bg-[#326eb2] text-white border-l border-white/30"
                        >
                            {{ year }}
                        </th>
                    </tr>
                </thead>

                <!-- Body -->
                <tbody>
                    <template
                        v-for="group in displayGroups"
                        :key="`group-${group.coe_name}`"
                    >
                        <template
                            v-for="(initiative, idx) in group.initiatives"
                            :key="`initiative-${initiative.id}`"
                        >
                            <tr
                                v-for="(timelineRow, rowIdx) in initiative.timeline_rows"
                                :key="`ini-${initiative.id}-${timelineRow.key}`"
                                :class="[
                                    'row-data',
                                    idx === group.initiatives.length - 1
                                    && rowIdx === initiative.timeline_rows.length - 1
                                        ? 'group-end-row'
                                        : '',
                                ]"
                            >
                                <!-- CoE cell -->
                                <td
                                    v-if="idx === 0 && rowIdx === 0"
                                    :rowspan="group.timeline_rowspan"
                                    class="cell-coe"
                                >
                                    {{ group.coe_name }}
                                </td>

                                <!-- Initiative name -->
                                <td
                                    v-if="rowIdx === 0"
                                    :rowspan="initiative.timeline_rowspan"
                                    class="cell-initiative"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            :class="[
                                                'badge',
                                                badgeClass(initiative.display_status),
                                            ]"
                                        >
                                            {{ initiative.no }}
                                        </span>
                                        <span
                                            class="ini-name text-slate-700 dark:text-slate-200"
                                        >
                                            {{ initiative.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Timeline cells -->
                                <td
                                    v-for="cell in timelineRow.cells"
                                    :key="cell.key"
                                    :colspan="cell.span"
                                    :title="cell.type === 'bar' ? timelineRow.label : ''"
                                    :class="[
                                        cell.type === 'bar' ? 'cell-bar' : 'cell-gap',
                                        cell.type === 'bar' ? `cell-bar--${timelineRow.layerKey}` : '',
                                        timelineRow.isPlaceholder ? 'cell-gap--placeholder' : '',
                                        cell.endsYear ? 'year-sep' : '',
                                    ]"
                                />
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
/* ── Layout only — colors handled by Tailwind classes ── */

.gantt-table {
    width: 100%;
    min-width: 820px;
    table-layout: fixed;
    border-collapse: collapse;
    --timeline-thickness: 10px;
    --group-separator: #8ca9c7;
    --group-soft: #edf4fb;
    --group-separator-width: 1px;
}

.gantt-table th,
.gantt-table td {
    border: none;
    padding: 0;
    overflow: hidden;
}

/* ── Column widths ───────────────────────────────────── */
.col-coe {
    width: 11%;
}
.col-initiative {
    width: 23%;
}
.col-quarter {
    width: calc(40% / var(--qcount));
}

/* ── Header cells ────────────────────────────────────── */
.th-cell {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 18px 12px;
    text-align: left;
    vertical-align: middle;
    white-space: nowrap;
    line-height: 1.35;
}

.th-left {
    text-align: left;
}

.th-year {
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    padding: 18px 6px;
    vertical-align: middle;
    line-height: 1.2;
}

/* ── CoE cell ────────────────────────────────────────── */
.cell-coe {
    font-size: 10.5px;
    font-weight: 600;
    padding: 10px 12px;
    vertical-align: middle;
    line-height: 1.4;
    word-break: break-word;
    color: #334155;
    background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
    border-bottom: var(--group-separator-width) solid var(--group-separator) !important;
}

/* ── Initiative cell ─────────────────────────────────── */
.cell-initiative {
    padding: 7px 10px;
    vertical-align: middle;
    border-right: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #eef2f7 !important;
}

.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 17px;
    height: 17px;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
    line-height: 1;
    color: #ffffff;
}

.badge--default {
    background: #2d8fe2;
}

.badge--on-track {
    background: #8fcfff;
    color: #214f87;
}

.badge--at-risk {
    background: #ffea00;
    color: #7b5d00;
}

.badge--delayed {
    background: #f97316;
}

.badge--not-started {
    background: #2d8fe2;
}

.badge--not-signed {
    background: #ff1d1d;
}

.badge--done {
    background: #1fb34a;
}

.badge--on-review {
    background: #f59e0b;
}

.legend-panel {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 14px 16px;
    border: 1px solid #d9e4ef;
    border-radius: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.legend-panel__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 8px 12px;
}

.legend-panel__title {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #325b8f;
}

.legend-panel__period {
    font-size: 11px;
    font-weight: 600;
    color: #5b728d;
}

.legend-panel__section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.legend-panel__subtitle {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #64809f;
}

.legend-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 18px;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.legend-item--button {
    border: 1px solid #d9e4ef;
    border-radius: 999px;
    padding: 6px 10px;
    background: #ffffff;
    cursor: pointer;
    transition:
        opacity 0.18s ease,
        border-color 0.18s ease,
        background-color 0.18s ease,
        transform 0.18s ease;
}

.legend-item--button:hover {
    border-color: #8ca9c7;
    background: #f8fbff;
}

.legend-item--button:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(93, 140, 192, 0.18);
}

.legend-item--active {
    border-color: #5d8cc0;
    background: #eef6ff;
}

.legend-item--muted {
    opacity: 0.5;
}

.legend-swatch {
    display: inline-flex;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
}

.timeline-swatch--baseline {
    background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
}

.timeline-swatch--approved {
    background: linear-gradient(90deg, #34d399 0%, #16a34a 100%);
}

.legend-label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
}

.legend-toggle {
    font-size: 10px;
    font-weight: 700;
    color: #6b7280;
}

.legend-count {
    color: #64748b;
    font-weight: 700;
}

.period-filter {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border: 1px solid #d6e2ee;
    border-radius: 12px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.period-filter__label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: #59769a;
    white-space: nowrap;
}

.period-filter__select {
    min-width: 190px;
    border: 1px solid #c8d6e4;
    border-radius: 10px;
    background: #ffffff;
    padding: 7px 28px 7px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2359749a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 12px;
}

.period-filter__select:focus {
    outline: none;
    border-color: #5d8cc0;
    box-shadow: 0 0 0 3px rgba(93, 140, 192, 0.14);
}

.ini-name {
    font-size: 11px;
    font-weight: 500;
    line-height: 1.35;
    word-break: break-word;
}

/* ── Timeline cells ──────────────────────────────────── */
.cell-gap,
.gantt-table td.cell-bar {
    position: relative;
    height: 22px;
    vertical-align: middle;
    background: #ffffff;
    border-bottom: 1px solid #eef2f7 !important;
}

.gantt-table td.cell-bar {
    background: transparent;
}

.gantt-table td.cell-bar::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: calc(var(--timeline-thickness) - 2px);
    transform: translateY(-50%);
    background: #1a4b8c;
    border-radius: 999px;
}

.gantt-table td.cell-bar--baseline::after {
    background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
}

.gantt-table td.cell-bar--approved::after {
    background: linear-gradient(90deg, #34d399 0%, #16a34a 100%);
}

.cell-gap--placeholder {
    background: #fafcff;
}

/* Year boundary — subtle dashed separator */
.year-sep {
    border-right: 1px dashed #cbd5e0 !important;
}

.row-data {
    border-bottom: 1px solid #eef2f7;
    transition: background-color 0.18s ease;
}

.group-end-row > td {
    border-bottom: 1px solid var(--group-separator) !important;
}

.row-data:hover {
    background: #f8fbff;
}

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 1280px) {
    .col-coe {
        width: 13%;
    }
    .col-initiative {
        width: 27%;
    }
    .col-quarter {
        width: calc(60% / var(--qcount));
    }
    .ini-name {
        font-size: 10px;
    }
    .badge {
        width: 15px;
        height: 15px;
        font-size: 8px;
    }
    .legend-list {
        gap: 8px 14px;
    }
    .period-filter__select {
        min-width: 170px;
    }
}

@media (max-width: 900px) {
    .col-coe {
        width: 15%;
    }
    .col-initiative {
        width: 32%;
    }
    .col-quarter {
        width: calc(53% / var(--qcount));
    }
    .legend-panel {
        padding: 12px 14px;
    }
    .legend-item--button {
        width: 100%;
        justify-content: space-between;
    }
    .legend-label {
        font-size: 10px;
    }
    .period-filter {
        width: 100%;
        justify-content: space-between;
    }
    .period-filter__select {
        min-width: 0;
        width: 100%;
    }
}
</style>
