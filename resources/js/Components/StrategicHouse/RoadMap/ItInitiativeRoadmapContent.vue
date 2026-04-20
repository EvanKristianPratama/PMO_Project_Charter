<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
    groups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2025 },
    endYear: { type: Number, default: 2029 },
    totalCount: { type: Number, default: 0 },
    milestoneTypeOptions: { type: Array, default: () => [] },
});

/* ── Toggle ──────────────────────────────────────────── */
const showMode = ref("all");
const toggleBtns = [
    { key: "all", label: "All" },
    { key: "baseline", label: "Baseline" },
    { key: "approved", label: "Approved" },
];
const legendItems = [
    { label: "On Track", status: "On Track" },
    { label: "At Risk", status: "At Risk" },
    { label: "Done", status: "Done" },
    { label: "Not Started", status: "Not Started" },
    { label: "Not Signed", status: "Not Signed" },
];
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

/* ── Compute bar range ───────────────────────────────── */
function getRange(projects, statusFilter) {
    if (!Array.isArray(projects) || projects.length === 0) return null;

    let pool = projects;
    
    // Filter by status if specified
    if (statusFilter) {
        pool = projects.filter((p) => {
            const status = Number(p.status ?? 0);
            
            if (statusFilter === 'baseline') {
                // Status 5 = Baseline
                return status === 5;
            }
            
            if (statusFilter === 'approved') {
                // Status 4 = Approved
                return status === 4;
            }
            
            return true;
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

function barRange(projects) {
    if (showMode.value === "baseline") return getRange(projects, "baseline");
    if (showMode.value === "approved") return getRange(projects, "approved");
    return getRange(projects, null);
}

/* ── Build cell descriptors ──────────────────────────── */
function buildCells(range) {
    const total = totalCells.value;

    if (!range) {
        return Array.from({ length: total }, (_, i) => ({
            type: "gap",
            span: 1,
            key: `g${i}`,
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
            key: `g${c}`,
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
            key: `g${c}`,
            endsYear: (c + 1) % 4 === 0,
        });
        c++;
    }

    return cells;
}

/* ── CoE rowspan ─────────────────────────────────────── */
function coeRowspan(group) {
    return (group.initiatives ?? []).length;
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

const displayGroups = computed(() =>
    (Array.isArray(props.groups) ? props.groups : []).map((group) => ({
        ...group,
        initiatives: (Array.isArray(group?.initiatives) ? group.initiatives : []).map((initiative) => {
            const periodState = resolveInitiativeStatusByPeriod(
                initiative,
                selectedPeriod.value,
            );

            return {
                ...initiative,
                display_status: periodState.status,
                display_period: periodState.period,
            };
        }),
    })),
);

const selectedPeriodLabel = computed(() => {
    return (
        availablePeriods.value.find((period) => period.value === selectedPeriod.value)?.label
        ?? "-"
    );
});

const legendItemsWithCounts = computed(() => {
    const initiatives = displayGroups.value.flatMap((group) =>
        Array.isArray(group?.initiatives) ? group.initiatives : [],
    );

    const statusItems = legendItems.map((item) => ({
        ...item,
        count: initiatives.filter((initiative) => {
            return normalizeStatusLabel(initiative?.display_status) === item.status;
        }).length,
    }));

    return [
        ...statusItems,
        {
            label: "Total",
            status: "Total",
            count: initiatives.length,
        },
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
        <!-- ── Header + Toggle ─────────────────────────── -->
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
        >
            <!-- Toggle -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <div
                    class="inline-flex rounded-lg overflow-hidden border border-slate-200 dark:border-white/10 shadow-sm"
                >
                    <button
                        v-for="btn in toggleBtns"
                        :key="btn.key"
                        type="button"
                        class="px-3 py-1.5 text-[11px] font-semibold transition-colors"
                        :class="
                            showMode === btn.key
                                ? 'bg-[#1a4b8c] text-white'
                                : 'bg-white dark:bg-[#1c1c1c] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'
                        "
                        @click="showMode = btn.key"
                    >
                        {{ btn.label }}
                    </button>
                </div>

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
                <div class="legend-panel__title">Legend Status</div>
                <div class="legend-panel__period">{{ selectedPeriodLabel }}</div>
            </div>
            <div class="legend-list">
                <div
                    v-for="item in legendItemsWithCounts"
                    :key="`legend-${item.status}`"
                    class="legend-item"
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
                        <tr
                            v-for="(initiative, idx) in group.initiatives"
                            :key="`ini-${initiative.id}`"
                            class="row-data"
                        >
                            <!-- CoE cell -->
                            <td
                                v-if="idx === 0"
                                :rowspan="coeRowspan(group)"
                                class="cell-coe"
                            >
                                {{ group.coe_name }}
                            </td>

                            <!-- Initiative name -->
                            <td class="cell-initiative">
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
                                v-for="cell in buildCells(
                                    barRange(initiative.projects),
                                )"
                                :key="cell.key"
                                :colspan="cell.span"
                                :class="[
                                    cell.type === 'bar'
                                        ? 'cell-bar'
                                        : 'cell-gap',
                                    cell.endsYear ? 'year-sep' : '',
                                ]"
                            />
                        </tr>
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
    --timeline-thickness: 8px;
    --group-separator: #8ca9c7;
    --group-soft: #edf4fb;
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
    border-bottom: 3px solid var(--group-separator) !important;
}

/* ── Initiative cell ─────────────────────────────────── */
.cell-initiative {
    padding: 7px 10px;
    vertical-align: middle;
    border-right: 1px solid #e2e8f0 !important;
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

.legend-swatch {
    display: inline-flex;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
}

.legend-label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
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
    height: auto;
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
    height: var(--timeline-thickness);
    transform: translateY(-50%);
    background: #1a4b8c;
    border-radius: 999px;
}

/* Year boundary — subtle dashed separator */
.year-sep {
    border-right: 1px dashed #cbd5e0 !important;
}

.row-data {
    transition: background-color 0.18s ease;
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
