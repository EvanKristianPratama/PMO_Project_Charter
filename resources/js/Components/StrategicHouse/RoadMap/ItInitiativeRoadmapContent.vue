<script setup>
import { Link } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    groups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2025 },
    endYear: { type: Number, default: 2029 },
    totalCount: { type: Number, default: 0 },
    milestoneTypeOptions: { type: Array, default: () => [] },
    groupHeaderLabel: { type: String, default: "IT Building Blocks" },
    initiativeHeaderLabel: { type: String, default: "IT Initiatives" },
    sectionTitle: { type: String, default: "" },
    filterMode: { type: String, default: "period" },
    showOrganizationFilter: { type: Boolean, default: false },
    showControls: { type: Boolean, default: true },
    showFilters: { type: Boolean, default: true },
    controlsPlacement: { type: String, default: "top" },
    showLegend: { type: Boolean, default: true },
    showRoadmapLegend: { type: Boolean, default: true },
    showTableHeader: { type: Boolean, default: true },
    emptyText: {
        type: String,
        default: "Belum ada data roadmap IT Strategic Initiative.",
    },
});

/* ── Constants ───────────────────────────────────────── */
const roadmapLegendItems = [
    { key: "baseline", label: "Baseline" },
    { key: "approved", label: "Approved" },
];

const PERIOD_FILTER_LATEST = "__latest__";
const PERIOD_FILTER_ALL = "__all__";

const statusLegendOrder = [
    "On Progress",
    "On Track",
    "At Risk",
    "Delayed",
    "Done",
    "Not Started",
    "Not Signed",
    "On Review",
];

const monthsOrder = [
    "Januari",   "Februari", "Maret",    "April",
    "Mei",       "Juni",     "Juli",     "Agustus",
    "September", "Oktober",  "November", "Desember",
];

const MONTH_ALIAS_TO_INDEX = {
    januari: 0,
    jan: 0,
    january: 0,
    februari: 1,
    feb: 1,
    february: 1,
    maret: 2,
    mar: 2,
    march: 2,
    april: 3,
    apr: 3,
    mei: 4,
    may: 4,
    juni: 5,
    jun: 5,
    june: 5,
    juli: 6,
    jul: 6,
    july: 6,
    agustus: 7,
    agu: 7,
    ags: 7,
    aug: 7,
    august: 7,
    september: 8,
    sep: 8,
    sept: 8,
    oktober: 9,
    okt: 9,
    oct: 9,
    october: 9,
    november: 10,
    nov: 10,
    desember: 11,
    des: 11,
    dec: 11,
    december: 11,
};

/**
 * Single source of truth for status label → display label and badge CSS modifier.
 * Keyed by the lowercase version of the raw status string.
 */
const STATUS_MAP = {
    "on progress": { label: "On Progress", badge: "badge--on-progress" },
    "in progress": { label: "On Progress", badge: "badge--on-progress" },
    "on track":    { label: "On Track",    badge: "badge--on-track"    },
    "at risk":     { label: "At Risk",     badge: "badge--at-risk"     },
    "delayed":     { label: "Delayed",     badge: "badge--delayed"     },
    "not started": { label: "Not Started", badge: "badge--not-started" },
    "not signed":  { label: "Not Signed",  badge: "badge--not-signed"  },
    "done":        { label: "Done",        badge: "badge--done"        },
    "completed":   { label: "Done",        badge: "badge--done"        }, // alias
    "on review":   { label: "On Review",   badge: "badge--on-review"   },
};

/* ── Reactive state ──────────────────────────────────── */
const visibleRoadmapLayers = ref(["baseline", "approved"]);
const selectedReviewStatus = ref("Total");
const selectedPeriod = ref(PERIOD_FILTER_LATEST);
const selectedOrganization = ref("");
const selectedProjectLeader = ref("");
const selectedProjectOwner = ref("");

/* ── Year / Month grid ─────────────────────────────── */
const years = computed(() =>
    Array.from(
        { length: props.endYear - props.startYear + 1 },
        (_, i) => props.startYear + i,
    ),
);

const monthCells = computed(() =>
    years.value.flatMap((y) =>
        Array.from({ length: 12 }, (_, month) => ({ year: y, month: month + 1 })),
    ),
);

const totalCells = computed(() => monthCells.value.length);

const organizationFilterMode = computed(() => props.filterMode === "organization");
const hasOrganizationFilter = computed(
    () => organizationFilterMode.value || props.showOrganizationFilter,
);

function resolveInitiativeDuration(initiative) {
    if (!Array.isArray(initiative?.projects)) {
        return "";
    }

    return (
        initiative.projects
            .map((project) => String(project?.duration ?? "").trim())
            .find((value) => value !== "") ?? ""
    );
}

/* ── Helpers ─────────────────────────────────────────── */

/** Convert a date string (YYYY-MM-...) to a month index relative to startYear. */
function toMonthIdx(value) {
    if (!value) return null;
    const m = String(value).match(/^(\d{4})-(\d{2})/);
    if (!m) return null;
    const year  = parseInt(m[1], 10);
    const month = parseInt(m[2], 10) - 1;
    const raw   = (year - props.startYear) * 12 + month;
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

function monthToIndex(monthValue) {
    const rawValue = String(monthValue ?? "").trim();

    if (!rawValue) {
        return -1;
    }

    const numericValue = Number(rawValue);

    if (Number.isInteger(numericValue) && numericValue >= 1 && numericValue <= 12) {
        return numericValue - 1;
    }

    const normalizedValue = rawValue.toLowerCase();

    if (Object.prototype.hasOwnProperty.call(MONTH_ALIAS_TO_INDEX, normalizedValue)) {
        return MONTH_ALIAS_TO_INDEX[normalizedValue];
    }

    const byLabel = monthsOrder.findIndex(
        (monthLabel) => monthLabel.toLowerCase() === normalizedValue,
    );

    return byLabel;
}

/**
 * Normalise a raw roadmap status to "baseline" | "approved" | <raw>.
 * Handles common typos (e.g. "aproved").
 */
function normalizeRoadmapStatus(rawStatus) {
    const normalized = String(rawStatus ?? "").trim().toLowerCase();
    if (!normalized) return "";
    if (normalized.includes("baseline")) return "baseline";
    if (normalized.includes("approve")) return "approved"; // covers "approved", "approve", "aproved"
    return normalized;
}

/** Return the canonical display label for an implementation status string. */
function normalizeStatusLabel(rawStatus) {
    const value = String(rawStatus ?? "").trim();
    if (!value) return "";
    return STATUS_MAP[value.toLowerCase()]?.label ?? value;
}

function normalizeOrganizationLabel(rawValue) {
    const value = String(rawValue ?? "").trim();

    if (!value || value === "-") {
        return "Belum ada organisasi";
    }

    return value;
}

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

/** Return the BEM modifier class for a status badge. */
function badgeClass(status) {
    const value = String(status ?? "").trim();
    if (!value) return "badge--default";
    return STATUS_MAP[value.toLowerCase()]?.badge ?? "badge--default";
}

function statusMarkerColor(status) {
    const value = String(status ?? "").trim().toLowerCase();

    return (
        {
            "on progress": "#2d8fe2",
            "in progress": "#2d8fe2",
            "on track": "#8fcfff",
            "at risk": "#ffea00",
            "delayed": "#f97316",
            "not started": "#2d8fe2",
            "not signed": "#ff1d1d",
            "done": "#1fb34a",
            "completed": "#1fb34a",
            "on review": "#f59e0b",
        }[value] ?? "#0b2a8a"
    );
}

function firstNonEmptyValue(...values) {
    for (const value of values) {
        const normalized = String(value ?? "").trim();
        if (normalized !== "") {
            return normalized;
        }
    }

    return "";
}

function buildReviewMarkerFromLog(log) {
    if (!log) {
        return null;
    }

    const monthValue = firstNonEmptyValue(log.end, log.month, log.start);
    const year = Number(log.year ?? 0);
    const monthIndex = monthToIndex(monthValue);

    if (!Number.isFinite(year) || monthIndex < 0) {
        return null;
    }

    const anchorIndex = Math.max(
        0,
        Math.min((year - props.startYear) * 12 + monthIndex, totalCells.value - 1),
    );

    return {
        anchorIndex,
        color: statusMarkerColor(log.status ?? log.review_status),
        label: log.periode_label ?? [monthValue, year].filter(Boolean).join(" "),
        statusLabel: normalizeStatusLabel(log.status ?? log.review_status),
    };
}

function resolveReviewMarkerOffset(reviewMarker, cell) {
    const anchorIndex = Number(reviewMarker?.anchorIndex);
    const cellStart = Number(cell?.startIndex);
    const cellEnd = Number(cell?.endIndex);

    if (!Number.isFinite(anchorIndex) || !Number.isFinite(cellStart) || !Number.isFinite(cellEnd)) {
        return 50;
    }

    const span = Math.max(1, cellEnd - cellStart + 1);
    const relativeIndex = Math.max(0, Math.min(anchorIndex - cellStart, span - 1));

    return ((relativeIndex + 0.5) / span) * 100;
}

function buildPeriodKeyFromLog(log) {
    const start = String(log?.start ?? "").trim();
    const end = String(log?.end ?? "").trim();
    const month = String(log?.month ?? "").trim();
    const year = String(log?.year ?? "").trim();
    const primaryMonth = start || month;

    if (!primaryMonth || !year) {
        return "";
    }

    return [primaryMonth, end, year].join("|");
}

function resolveLogMonthIndex(log) {
    return monthToIndex(
        firstNonEmptyValue(log?.end, log?.month, log?.start),
    );
}

function resolveLogTimestamp(log) {
    const updatedAt = Date.parse(String(log?.updated_at ?? ""));
    if (Number.isFinite(updatedAt)) {
        return updatedAt;
    }

    const createdAt = Date.parse(String(log?.created_at ?? ""));
    return Number.isFinite(createdAt) ? createdAt : 0;
}

function compareTimelineLogs(left, right) {
    const leftYear = Number(left?.year ?? 0);
    const rightYear = Number(right?.year ?? 0);

    if (leftYear !== rightYear) {
        return leftYear - rightYear;
    }

    const leftMonthIndex = resolveLogMonthIndex(left);
    const rightMonthIndex = resolveLogMonthIndex(right);

    if (leftMonthIndex !== rightMonthIndex) {
        return leftMonthIndex - rightMonthIndex;
    }

    const leftTimestamp = resolveLogTimestamp(left);
    const rightTimestamp = resolveLogTimestamp(right);

    if (leftTimestamp !== rightTimestamp) {
        return leftTimestamp - rightTimestamp;
    }

    return Number(left?.id ?? 0) - Number(right?.id ?? 0);
}

function sortTimelineLogs(logs) {
    return [...logs].sort(compareTimelineLogs);
}

function resolveLatestTimelineLog(reviewLogs, implementationLogs) {
    const logs = [...reviewLogs, ...implementationLogs].filter(Boolean);

    if (logs.length === 0) {
        return null;
    }

    return sortTimelineLogs(logs).at(-1) ?? null;
}

function resolveAllTimelineLogs(reviewLogs, implementationLogs) {
    const sourceLogs = [...reviewLogs, ...implementationLogs];
    const dedupedLogs = new Map();

    sortTimelineLogs(sourceLogs).forEach((log) => {
        const key = String(
            log?.period_key
            ?? [log?.year ?? "", resolveLogMonthIndex(log), log?.periode_label ?? ""].join("|"),
        ).trim();

        if (!key) return;
        dedupedLogs.set(key, log);
    });

    return Array.from(dedupedLogs.values());
}

function resolveReviewMarkersForSelection(reviewLogs, implementationLogs, selectedPeriodValue) {
    if (selectedPeriodValue === PERIOD_FILTER_ALL) {
        return resolveAllTimelineLogs(reviewLogs, implementationLogs)
            .map((log) => buildReviewMarkerFromLog(log))
            .filter(Boolean);
    }

    let matchedLog = null;

    if (
        selectedPeriodValue &&
        selectedPeriodValue !== PERIOD_FILTER_LATEST &&
        selectedPeriodValue !== PERIOD_FILTER_ALL
    ) {
        matchedLog = reviewLogs.findLast(
            (item) => String(item?.period_key ?? "") === String(selectedPeriodValue),
        ) ?? null;

        if (!matchedLog) {
            matchedLog = implementationLogs.findLast(
                (item) => String(item?.period_key ?? buildPeriodKeyFromLog(item)) === String(selectedPeriodValue),
            ) ?? null;
        }
    }

    if (!matchedLog) {
        matchedLog = resolveLatestTimelineLog(reviewLogs, implementationLogs);
    }

    const marker = buildReviewMarkerFromLog(matchedLog);
    return marker ? [marker] : [];
}

function resolveReviewMarkersForCell(reviewMarkers, cell) {
    const markers = (Array.isArray(reviewMarkers) ? reviewMarkers : []).filter(
        (marker) =>
            cell.startIndex <= marker.anchorIndex && marker.anchorIndex <= cell.endIndex,
    );

    return markers.map((marker, index) => ({
        ...marker,
        topOffset: 50 + ((index - ((markers.length - 1) / 2)) * 14),
    }));
}

/* ── Roadmap layer visibility ────────────────────────── */
function isRoadmapLayerVisible(layerKey) {
    return visibleRoadmapLayers.value.includes(layerKey);
}

function toggleRoadmapLayer(layerKey) {
    const isVisible = visibleRoadmapLayers.value.includes(layerKey);

    if (isVisible) {
        // Prevent deselecting the last remaining layer.
        if (visibleRoadmapLayers.value.length === 1) return;
        visibleRoadmapLayers.value = visibleRoadmapLayers.value.filter(
            (k) => k !== layerKey,
        );
        return;
    }

    // Re-add layerKey while preserving the canonical order from roadmapLegendItems.
    visibleRoadmapLayers.value = roadmapLegendItems
        .map((item) => item.key)
        .filter((k) => k === layerKey || visibleRoadmapLayers.value.includes(k));
}

/* ── Review status filter ────────────────────────────── */
function isSelectedReviewStatus(status) {
    return selectedReviewStatus.value === status;
}

function toggleReviewStatus(status) {
    selectedReviewStatus.value =
        status === "Total" || selectedReviewStatus.value === status ? "Total" : status;
}

const availableOrganizations = computed(() => {
    const organizations = new Map();

    allInitiatives.value.forEach((initiative) => {
        const value = normalizeOrganizationLabel(initiative?.organization_name ?? "");

        if (!value || organizations.has(value)) return;
        organizations.set(value, { value, label: value });
    });

    return Array.from(organizations.values()).sort((left, right) =>
        left.label.localeCompare(right.label),
    );
});

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

/* ── Range computation ───────────────────────────────── */

/**
 * Compute the single-cell month index from the latest review_statuses entry.
 * The bar is pinned to the `end` month (or `start` if no `end`) of the review period.
 * All initiatives with the same end month+year will be perfectly aligned.
 */
function getRangeFromReviewStatuses(reviewStatuses) {
    if (!Array.isArray(reviewStatuses) || reviewStatuses.length === 0) return null;

    // Use the last (latest) review status entry
    const latest = reviewStatuses[reviewStatuses.length - 1];
    if (!latest) return null;

    const endMonthName  = firstNonEmptyValue(latest.end, latest.start);
    const startMonthName = firstNonEmptyValue(latest.start, latest.end);
    const year          = parseInt(String(latest.year ?? ""), 10);

    if (!Number.isFinite(year)) return null;

    // Resolve end month index (0-based within year)
    const endMonthIdx   = endMonthName   ? monthsOrder.indexOf(endMonthName)   : -1;
    const startMonthIdx = startMonthName ? monthsOrder.indexOf(startMonthName) : -1;

    const resolvedEndIdx   = endMonthIdx   >= 0 ? endMonthIdx   : (startMonthIdx >= 0 ? startMonthIdx : -1);
    const resolvedStartIdx = startMonthIdx >= 0 ? startMonthIdx : resolvedEndIdx;

    if (resolvedEndIdx < 0) return null;

    const cellEnd   = Math.max(0, Math.min((year - props.startYear) * 12 + resolvedEndIdx,   totalCells.value - 1));
    const cellStart = Math.max(0, Math.min((year - props.startYear) * 12 + resolvedStartIdx, totalCells.value - 1));

    return { start: Math.min(cellStart, cellEnd), end: Math.max(cellStart, cellEnd) };
}

/**
 * Compute the month-index range that covers all milestones in `projects`
 * for a given roadmap status layer (baseline | approved).
 * This is used to determine whether a Gantt row SHOULD EXIST,
 * keeping the original chart structure intact.
 */
function getMilestoneRange(projects, statusFilter) {
    if (!Array.isArray(projects) || projects.length === 0) return null;

    const pool = statusFilter
        ? projects.filter((project) => {
              const statusKey = normalizeRoadmapStatus(
                  project?.status_ref?.name ?? project?.status,
              );
              return statusKey === statusFilter;
          })
        : projects;

    if (pool.length === 0) return null;

    const milestones = pool.flatMap((p) =>
        Array.isArray(p.milestones) ? p.milestones : [],
    );
    if (milestones.length === 0) return null;

    let minS = null;
    let maxE = null;

    for (const ms of milestones) {
        const si = toMonthIdx(ms.start_date ?? ms.end_date);
        const ei = toMonthIdx(ms.end_date ?? ms.start_date);
        if (si !== null && (minS === null || si < minS)) minS = si;
        if (ei !== null && (maxE === null || ei > maxE)) maxE = ei;
    }

    if (minS === null && maxE === null) return null;
    const s = minS ?? maxE;
    const e = maxE ?? minS;
    return { start: Math.min(s, e), end: Math.max(s, e) };
}

/* ── Cell builder ────────────────────────────────────── */

/** Build an array of cell descriptors (gap | bar) for a given month range. */
function buildCells(range, keyPrefix = "default") {
    const total = totalCells.value;

    if (!range) {
        return Array.from({ length: total }, (_, i) => ({
            type: "gap",
            span: 1,
            key: `${keyPrefix}-g${i}`,
            startIndex: i,
            endIndex: i,
            endsYear: (i + 1) % 12 === 0,
        }));
    }

    const { start, end } = range;
    const safeStart = Math.max(0, Math.min(start, total - 1));
    const safeEnd   = Math.max(safeStart, Math.min(end, total - 1));
    const cells     = [];
    let   c         = 0;

    while (c < safeStart) {
        cells.push({ type: "gap", span: 1, key: `${keyPrefix}-g${c}`, startIndex: c, endIndex: c, endsYear: (c + 1) % 12 === 0 });
        c++;
    }

    cells.push({
        type: "bar",
        span: safeEnd - safeStart + 1,
        key: `${keyPrefix}-bar-${safeStart}`,
        startIndex: safeStart,
        endIndex: safeEnd,
        endsYear: (safeEnd + 1) % 12 === 0,
    });
    c = safeEnd + 1;

    while (c < total) {
        cells.push({ type: "gap", span: 1, key: `${keyPrefix}-g${c}`, startIndex: c, endIndex: c, endsYear: (c + 1) % 12 === 0 });
        c++;
    }

    return cells;
}

/**
 * Build the set of timeline rows for a single initiative.
 * Returns one row per visible roadmap layer that has data,
 * or a single placeholder row when no layer has data.
 *
 * Gantt bars are positioned based on Milestone start_date/end_date (original behavior).
 * The review marker (plot segi4) is positioned based on the `end` month
 * of the review_statuses entry matching the selected period, so all
 * markers align at the same column when the same period is selected.
 */
function buildInitiativeTimelineRows(initiative, selectedPeriodValue) {
    const initiativeId = initiative?.id ?? "initiative";
    const projects     = Array.isArray(initiative?.projects) ? initiative.projects : [];
    const rows         = [];

    const roadmapRows = roadmapLegendItems
        .filter((item) => isRoadmapLayerVisible(item.key))
        .map((item) => {
            const range = getMilestoneRange(projects, item.key);
            if (!range) return null;

            return {
                key:           `${initiativeId}-${item.key}`,
                layerKey:      item.key,
                label:         item.label,
                isPlaceholder: false,
                cells:         buildCells(range, `${initiativeId}-${item.key}`),
            };
        })
        .filter(Boolean);

    // Find the review log matching the selected period (not just the latest)
    const reviewLogs = Array.isArray(initiative?.review_statuses) ? initiative.review_statuses : [];
    const implementationLogs = Array.isArray(initiative?.implementation_statuses)
        ? initiative.implementation_statuses
        : [];

    const reviewMarkers = resolveReviewMarkersForSelection(
        reviewLogs,
        implementationLogs,
        selectedPeriodValue,
    );

    if (reviewMarkers.length > 0 && roadmapRows.length > 0) {
        roadmapRows.forEach((row) => {
            row.reviewMarkers = reviewMarkers;
        });
    }

    if (roadmapRows.length > 0) return roadmapRows;

    // Fallback: render a single empty row so the Gantt table stays aligned.
    const fallbackRows = [
        {
            key:           `${initiativeId}-empty`,
            layerKey:      "empty",
            label:         "",
            isPlaceholder: true,
            cells:         buildCells(null, `${initiativeId}-empty`),
        },
    ];

    if (reviewMarkers.length > 0) {
        fallbackRows[0].reviewMarkers = reviewMarkers;
    }

    return fallbackRows;
}

/* ── Derived data ────────────────────────────────────── */
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
        const implementationStatuses = Array.isArray(initiative?.implementation_statuses)
            ? initiative.implementation_statuses
            : [];

        reviewStatuses.forEach((statusItem) => {
            const value      = String(statusItem?.period_key    ?? "").trim();
            const label      = String(statusItem?.periode_label ?? "").trim();
            const year       = Number(statusItem?.year          ?? 0);
            const startMonth = String(statusItem?.start         ?? "").trim();
            const endMonth   = String(statusItem?.end           ?? "").trim();

            if (!value || !label || periods.has(value)) return;

            periods.set(value, { value, label, year, startMonth, endMonth });
        });

        implementationStatuses.forEach((statusItem) => {
            const value = String(
                statusItem?.period_key ?? buildPeriodKeyFromLog(statusItem),
            ).trim();
            const label = String(statusItem?.periode_label ?? "").trim();
            const year = Number(statusItem?.year ?? 0);
            const startMonth = String(statusItem?.start ?? statusItem?.month ?? "").trim();
            const endMonth = String(statusItem?.end ?? "").trim();

            if (!value || !label || periods.has(value)) return;

            periods.set(value, { value, label, year, startMonth, endMonth });
        });
    });

    return Array.from(periods.values()).sort((left, right) => {
        if (left.year !== right.year) return right.year - left.year;

        const li = monthToIndex(left.startMonth);
        const ri = monthToIndex(right.startMonth);
        if (li !== ri) return ri - li;

        return monthToIndex(right.endMonth) - monthToIndex(left.endMonth);
    });
});

const periodFilterOptions = computed(() => [
    { value: PERIOD_FILTER_LATEST, label: "Latest (Terbaru)" },
    { value: PERIOD_FILTER_ALL, label: "All (Semua Periode)" },
    ...availablePeriods.value,
]);

// Keep special filters stable; fall back to Latest if a concrete period disappears.
watch(
    availablePeriods,
    (periods) => {
        if (
            selectedPeriod.value === PERIOD_FILTER_LATEST ||
            selectedPeriod.value === PERIOD_FILTER_ALL
        ) {
            return;
        }

        if (!Array.isArray(periods) || periods.length === 0) {
            selectedPeriod.value = PERIOD_FILTER_LATEST;
            return;
        }

        const stillValid = periods.some((p) => p.value === selectedPeriod.value);
        if (!stillValid) selectedPeriod.value = PERIOD_FILTER_LATEST;
    },
    { immediate: true },
);

watch(
    availableOrganizations,
    (organizations) => {
        if (!Array.isArray(organizations) || organizations.length === 0) {
            selectedOrganization.value = "";
            return;
        }

        const stillValid = organizations.some(
            (item) => item.value === selectedOrganization.value,
        );

        if (!stillValid) {
            selectedOrganization.value = "";
        }
    },
    { immediate: true },
);

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

/**
 * Return the implementation status and period label for an initiative
 * at a specific review period. Falls back to `implementation_status` when
 * no period-specific record is found.
 */
function resolveInitiativeStatusByPeriod(initiative, periodValue) {
    const reviewStatuses = Array.isArray(initiative?.review_statuses)
        ? initiative.review_statuses
        : [];
    const implementationStatuses = Array.isArray(initiative?.implementation_statuses)
        ? initiative.implementation_statuses
        : [];

    if (
        periodValue === PERIOD_FILTER_LATEST ||
        periodValue === PERIOD_FILTER_ALL ||
        !periodValue
    ) {
        const latestLog = resolveLatestTimelineLog(reviewStatuses, implementationStatuses);

        return {
            status:
                latestLog?.review_status
                ?? latestLog?.status
                ?? initiative?.implementation_status
                ?? null,
            period: latestLog?.periode_label ?? null,
        };
    }

    const selected = reviewStatuses.findLast(
        (item) => String(item?.period_key ?? "") === String(periodValue),
    );

    if (!selected) {
        const selectedImplementation = implementationStatuses.findLast(
            (item) => String(item?.period_key ?? buildPeriodKeyFromLog(item)) === String(periodValue),
        );

        return {
            status: selectedImplementation?.review_status ?? selectedImplementation?.status ?? initiative?.implementation_status ?? null,
            period: selectedImplementation?.periode_label ?? null,
        };
    }

    return {
        status: selected?.review_status ?? initiative?.implementation_status ?? null,
        period: selected?.periode_label ?? null,
    };
}

const selectedFilterLabel = computed(() => {
    const leaderLabel = selectedProjectLeader.value || "Semua Project Leader";
    const ownerLabel = selectedProjectOwner.value || "Semua Project Owner";

    if (organizationFilterMode.value) {
        const organizationLabel = !selectedOrganization.value
            ? "Semua Organisasi"
            : (
                availableOrganizations.value.find(
                (item) => item.value === selectedOrganization.value,
            )?.label ?? "-"
            );

        return [organizationLabel, leaderLabel, ownerLabel].join(" · ");
    }

    const selectedPeriodLabel =
        selectedPeriod.value === PERIOD_FILTER_LATEST
            ? "Latest (Terbaru)"
            : selectedPeriod.value === PERIOD_FILTER_ALL
                ? "All (Semua Periode)"
                : availablePeriods.value.find((p) => p.value === selectedPeriod.value)?.label ?? "-";

    if (hasOrganizationFilter.value) {
        const organizationLabel = !selectedOrganization.value
            ? "Semua Organisasi"
            : (
                availableOrganizations.value.find(
                    (item) => item.value === selectedOrganization.value,
                )?.label ?? "-"
            );

        return [
            selectedPeriodLabel,
            organizationLabel,
            leaderLabel,
            ownerLabel,
        ].join(" · ");
    }

    return [
        selectedPeriodLabel,
        leaderLabel,
        ownerLabel,
    ].join(" · ");
});

const baseDisplayGroups = computed(() =>
    (Array.isArray(props.groups) ? props.groups : []).map((group) => {
        const initiatives = (
            Array.isArray(group?.initiatives) ? group.initiatives : []
        ).map((initiative) => {
            const organizationName = normalizeOrganizationLabel(
                initiative?.organization_name ?? "",
            );
            const periodState  = resolveInitiativeStatusByPeriod(initiative, selectedPeriod.value);
            const timelineRows = buildInitiativeTimelineRows(initiative, selectedPeriod.value);

            return {
                ...initiative,
                display_organization: organizationName,
                display_status:   periodState.status,
                display_period:   periodState.period,
                timeline_rows:    timelineRows,
                timeline_rowspan: timelineRows.length,
            };
        });

        return {
            ...group,
            initiatives,
            timeline_rowspan: initiatives.reduce(
                (sum, ini) => sum + (ini.timeline_rowspan || 1),
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

const legendInitiatives = computed(() => {
    if (!hasOrganizationFilter.value || !selectedOrganization.value) {
        return baseDisplayInitiatives.value;
    }

    return baseDisplayInitiatives.value.filter(
        (initiative) => (initiative?.display_organization ?? "") === selectedOrganization.value,
    );
});

const displayGroups = computed(() =>
    baseDisplayGroups.value
        .map((group) => {
            const initiatives = (
            Array.isArray(group?.initiatives) ? group.initiatives : []
        ).filter((initiative) => {
                const personnelEntries = getProjectPersonnelEntries(initiative);
                const matchesPersonnel =
                    !selectedProjectLeader.value && !selectedProjectOwner.value
                        ? true
                        : personnelEntries.some((entry) => {
                            const matchesLeader =
                                !selectedProjectLeader.value ||
                                entry.leader === selectedProjectLeader.value;
                            const matchesOwner =
                                !selectedProjectOwner.value ||
                                entry.owner === selectedProjectOwner.value;

                            return matchesLeader && matchesOwner;
                        });

                if (!matchesPersonnel) {
                    return false;
                }

                if (hasOrganizationFilter.value && selectedOrganization.value) {
                    if ((initiative?.display_organization ?? "") !== selectedOrganization.value) {
                        return false;
                    }
                }

                if (selectedReviewStatus.value === "Total") return true;
                return normalizeStatusLabel(initiative?.display_status) === selectedReviewStatus.value;
            });

            if (initiatives.length === 0) return null;

            return {
                ...group,
                initiatives,
                timeline_rowspan: initiatives.reduce(
                    (sum, ini) => sum + (ini.timeline_rowspan || 1),
                    0,
                ),
            };
        })
        .filter(Boolean),
);

const hasDisplayGroups = computed(() => displayGroups.value.length > 0);
const controlsAtBottom = computed(() => props.controlsPlacement === "bottom");

const reviewStatusLegendItems = computed(() => {
    const counts = legendInitiatives.value.reduce((carry, initiative) => {
        const status = normalizeStatusLabel(initiative?.display_status);
        if (!status) return carry;
        carry.set(status, (carry.get(status) ?? 0) + 1);
        return carry;
    }, new Map());

    return [
        { label: "Total", status: "Total", count: legendInitiatives.value.length },
        ...statusLegendOrder
            .map((status) => ({ label: status, status, count: counts.get(status) ?? 0 }))
            .filter((item) => item.count > 0),
    ];
});
</script>

<template>
    <div class="space-y-3">
        <!-- ── Header ─────────────────────────────────── -->
        <div v-if="showControls && !controlsAtBottom" class="controls-stack">
            <div v-if="sectionTitle" class="roadmap-section-title">
                {{ sectionTitle }}
            </div>

            <template v-if="showFilters">
                <div class="content-header">
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <label v-if="!organizationFilterMode" class="period-filter">
                            <span class="period-filter__label">
                                Status Review Implementation Period
                            </span>
                            <select v-model="selectedPeriod" class="period-filter__select">
                                <option
                                    v-for="period in periodFilterOptions"
                                    :key="`status-period-${period.value}`"
                                    :value="period.value"
                                >
                                    {{ period.label }}
                                </option>
                            </select>
                        </label>

                        <label v-if="hasOrganizationFilter" class="period-filter">
                            <span class="period-filter__label">
                                Organization
                            </span>
                            <select
                                v-model="selectedOrganization"
                                class="period-filter__select"
                            >
                                <option value="">Semua Organisasi</option>
                                <option
                                    v-for="organization in availableOrganizations"
                                    :key="`organization-${organization.value}`"
                                    :value="organization.value"
                                >
                                    {{ organization.label }}
                                </option>
                            </select>
                        </label>

                        <label class="period-filter">
                            <span class="period-filter__label">
                                Project Leader
                            </span>
                            <select
                                v-model="selectedProjectLeader"
                                class="period-filter__select"
                            >
                                <option value="">Semua Project Leader</option>
                                <option
                                    v-for="leader in availableProjectLeaders"
                                    :key="`project-leader-${leader}`"
                                    :value="leader"
                                >
                                    {{ leader }}
                                </option>
                            </select>
                        </label>

                        <label class="period-filter">
                            <span class="period-filter__label">
                                Project Owner
                            </span>
                            <select
                                v-model="selectedProjectOwner"
                                class="period-filter__select"
                            >
                                <option value="">Semua Project Owner</option>
                                <option
                                    v-for="owner in availableProjectOwners"
                                    :key="`project-owner-${owner}`"
                                    :value="owner"
                                >
                                    {{ owner }}
                                </option>
                            </select>
                        </label>
                    </div>
                </div>

                <div v-if="showLegend && groups && groups.length" class="legend-panel">
                    <div class="legend-panel__header">
                        <div class="legend-panel__title">Legend</div>
                        <div class="legend-panel__period">{{ selectedFilterLabel }}</div>
                    </div>

                    <div v-if="showRoadmapLegend" class="legend-panel__section">
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
                                <span :class="['legend-swatch', `timeline-swatch--${item.key}`]" />
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
                                    :class="['legend-swatch', 'legend-swatch--diamond', badgeClass(item.status)]"
                                />
                                <span class="legend-label">
                                    {{ item.label }}
                                    <span class="legend-count">({{ item.count }})</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- ── Empty states ──────────────────────────── -->
        <div
            v-if="!groups || groups.length === 0"
            class="empty-state rounded-xl border-dashed dark:border-white/10 dark:bg-[#171717]"
        >
            <p class="empty-state__text dark:text-slate-400">
                {{ emptyText }}
            </p>
        </div>

        <div
            v-else-if="!hasDisplayGroups"
            class="empty-state rounded-xl border-dashed dark:border-white/10 dark:bg-[#171717]"
        >
            <p class="empty-state__text dark:text-slate-400">
                Tidak ada initiative yang cocok dengan filter status review yang dipilih.
            </p>
        </div>

        <!-- ── Gantt table ────────────────────────────── -->
        <div
            v-else
            class="gantt-wrapper dark:border-white/10 dark:bg-[#171717]"
        >
            <table
                class="gantt-table"
                :style="{ '--cellcount': Math.max(totalCells, 1) }"
            >
                <colgroup>
                    <col class="col-coe" />
                    <col class="col-initiative" />
                    <col class="col-duration" />
                    <col
                        v-for="(_, i) in monthCells"
                        :key="`qcol-${i}`"
                        class="col-month"
                    />
                </colgroup>

                <!-- Year header -->
                <thead v-if="showTableHeader">
                    <tr class="gantt-header-row">
                        <th class="th-cell th-header border-r border-white/30">
                            {{ groupHeaderLabel }}
                        </th>
                        <th class="th-cell th-left th-header border-r border-white/30">
                            {{ initiativeHeaderLabel }}
                        </th>
                        <th class="th-cell th-left th-header border-r border-white/30">
                            Duration
                        </th>
                        <th
                            v-for="year in years"
                            :key="`yr-${year}`"
                            colspan="12"
                            class="th-year th-header border-l border-white/30"
                        >
                            {{ year }}
                        </th>
                    </tr>
                </thead>

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
                                    idx === group.initiatives.length - 1 &&
                                    rowIdx === initiative.timeline_rows.length - 1
                                        ? 'group-end-row'
                                        : '',
                                ]"
                            >
                                <!-- CoE label — spans all rows in this group -->
                                <td
                                    v-if="idx === 0 && rowIdx === 0"
                                    :rowspan="group.timeline_rowspan"
                                    class="cell-coe"
                                >
                                    {{ group.coe_name }}
                                </td>

                                <!-- Initiative label — spans all timeline rows for this initiative -->
                                <td
                                    v-if="rowIdx === 0"
                                    :rowspan="initiative.timeline_rowspan"
                                    class="cell-initiative"
                                >
                                    <div class="initiative-label">
                                        <span :class="['badge', badgeClass(initiative.display_status)]">
                                            {{ initiative.no }}
                                        </span>
                                        <Link
                                            v-if="initiative.projects?.[0]?.project_id"
                                            :href="route('it-initiatives.show', { project: initiative.projects[0].project_id, tab: 'detail' })"
                                            class="ini-name dark:text-slate-200 hover:text-blue-600 hover:underline transition-colors duration-200"
                                        >
                                            {{ initiative.name }}
                                        </Link>
                                        <span v-else class="ini-name dark:text-slate-200">
                                            {{ initiative.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Duration column — spans all timeline rows for this initiative -->
                                <td
                                    v-if="rowIdx === 0"
                                    :rowspan="initiative.timeline_rowspan"
                                    class="cell-duration"
                                >
                                    {{ resolveInitiativeDuration(initiative) || '-' }}
                                </td>

                                <!-- Timeline bar / gap cells -->
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
                                >
                                    <div
                                        v-if="timelineRow.reviewMarkers?.length"
                                        class="review-marker"
                                    >
                                        <span
                                            v-for="(marker, markerIdx) in resolveReviewMarkersForCell(timelineRow.reviewMarkers, cell)"
                                            :key="`${timelineRow.key}-marker-${marker.label}-${markerIdx}`"
                                            class="review-marker__diamond"
                                            :title="`${marker.label} · ${marker.statusLabel || '-'}`"
                                            :style="{
                                                backgroundColor: marker.color,
                                                boxShadow: `0 0 0 2px #ffffff, 0 0 0 3px ${marker.color}26`,
                                                left: `${resolveReviewMarkerOffset(marker, cell)}%`,
                                                top: `${marker.topOffset}%`,
                                            }"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- ── Bottom controls ───────────────────────── -->
        <div v-if="showControls && controlsAtBottom" class="controls-stack">
            <div v-if="sectionTitle" class="roadmap-section-title">
                {{ sectionTitle }}
            </div>

            <template v-if="showFilters">
                <div class="content-header">
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <label v-if="!organizationFilterMode" class="period-filter">
                            <span class="period-filter__label">
                                Status Review Implementation Period
                            </span>
                            <select v-model="selectedPeriod" class="period-filter__select">
                                <option
                                    v-for="period in periodFilterOptions"
                                    :key="`status-period-${period.value}`"
                                    :value="period.value"
                                >
                                    {{ period.label }}
                                </option>
                            </select>
                        </label>

                        <label v-if="hasOrganizationFilter" class="period-filter">
                            <span class="period-filter__label">
                                Organization
                            </span>
                            <select
                                v-model="selectedOrganization"
                                class="period-filter__select"
                            >
                                <option value="">Semua Organisasi</option>
                                <option
                                    v-for="organization in availableOrganizations"
                                    :key="`organization-${organization.value}`"
                                    :value="organization.value"
                                >
                                    {{ organization.label }}
                                </option>
                            </select>
                        </label>

                        <label class="period-filter">
                            <span class="period-filter__label">
                                Project Leader
                            </span>
                            <select
                                v-model="selectedProjectLeader"
                                class="period-filter__select"
                            >
                                <option value="">Semua Project Leader</option>
                                <option
                                    v-for="leader in availableProjectLeaders"
                                    :key="`project-leader-bottom-${leader}`"
                                    :value="leader"
                                >
                                    {{ leader }}
                                </option>
                            </select>
                        </label>

                        <label class="period-filter">
                            <span class="period-filter__label">
                                Project Owner
                            </span>
                            <select
                                v-model="selectedProjectOwner"
                                class="period-filter__select"
                            >
                                <option value="">Semua Project Owner</option>
                                <option
                                    v-for="owner in availableProjectOwners"
                                    :key="`project-owner-bottom-${owner}`"
                                    :value="owner"
                                >
                                    {{ owner }}
                                </option>
                            </select>
                        </label>
                    </div>
                </div>

                <div v-if="showLegend && groups && groups.length" class="legend-panel">
                    <div class="legend-panel__header">
                        <div class="legend-panel__title">Legend</div>
                        <div class="legend-panel__period">{{ selectedFilterLabel }}</div>
                    </div>

                    <div v-if="showRoadmapLegend" class="legend-panel__section">
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
                                <span :class="['legend-swatch', `timeline-swatch--${item.key}`]" />
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
                                    :class="['legend-swatch', 'legend-swatch--diamond', badgeClass(item.status)]"
                                />
                                <span class="legend-label">
                                    {{ item.label }}
                                    <span class="legend-count">({{ item.count }})</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<style scoped>
/* ─────────────────────────────────────────────────────
   Design tokens — scoped to the Gantt table root
   so they don't leak to the global stylesheet.
   ───────────────────────────────────────────────────── */
.gantt-table {
    --timeline-thickness:  10px;
    --group-sep-color:     #8ca9c7;
    --group-sep-width:     1px;
    --row-border-color:    #eef2f7;
    --cell-border-color:   #e2e8f0;
    --badge-size:          17px;
    --badge-font:          9px;
}

/* ─────────────────────────────────────────────────────
   Layout
   ───────────────────────────────────────────────────── */
.content-header {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.controls-stack {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.roadmap-section-title {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #1e3a5f;
    text-transform: uppercase;
}

@media (min-width: 640px) {
    .content-header {
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
    }
}

.gantt-wrapper {
    overflow-x: auto;
    border: 1px solid #d9e4ef;
    background: #ffffff;
    min-width: 0;
}

/* ─────────────────────────────────────────────────────
   Gantt table shell
   ───────────────────────────────────────────────────── */
.gantt-table {
    width: 100%;
    min-width: 820px;
    table-layout: fixed;
    border-collapse: collapse;
}

/* Reset all borders; specific selectors below re-add only what's needed. */
.gantt-table th,
.gantt-table td {
    border: none;
    padding: 0;
    overflow: hidden;
}

/* ─────────────────────────────────────────────────────
    Column widths
    CoE, initiative, and duration columns are fixed;
    timeline columns share the remaining horizontal space.
   ───────────────────────────────────────────────────── */
.col-coe        { width: 11%; }
.col-initiative { width: 23%; }
.col-duration   { width: 10%; }
.col-month      { width: calc(40% / var(--cellcount)); }

/* ─────────────────────────────────────────────────────
   Header
   ───────────────────────────────────────────────────── */
.gantt-header-row { border-bottom: 1px solid #c9d2dd; }

.th-header {
    background-color: #326eb2;
    color: #ffffff;
}

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

.th-left { text-align: left; }

.th-year {
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    padding: 18px 6px;
    vertical-align: middle;
    line-height: 1.2;
}

/* ─────────────────────────────────────────────────────
   Body rows
   ───────────────────────────────────────────────────── */
.row-data {
    border-bottom: 1px solid var(--row-border-color);
    transition: background-color 0.18s ease;
}

.row-data:hover { background: #f8fbff; }

/* Group separator — higher specificity than the .gantt-table td reset above */
.gantt-table tr.group-end-row > td {
    border-bottom: var(--group-sep-width) solid var(--group-sep-color);
}

/* ─────────────────────────────────────────────────────
   CoE cell
   ───────────────────────────────────────────────────── */
.gantt-table td.cell-coe {
    font-size: 10.5px;
    font-weight: 600;
    padding: 10px 12px;
    vertical-align: middle;
    line-height: 1.4;
    word-break: break-word;
    color: #334155;
    background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
    border-bottom: var(--group-sep-width) solid var(--group-sep-color);
}

/* ─────────────────────────────────────────────────────
   Initiative cell
   ───────────────────────────────────────────────────── */
.gantt-table td.cell-initiative {
    padding: 7px 10px;
    vertical-align: middle;
    border-right: 1px solid var(--cell-border-color);
    border-bottom: 1px solid var(--row-border-color);
}

.initiative-label {
    display: flex;
    align-items: center;
    gap: 6px;
}

.ini-name {
    font-size: 11px;
    font-weight: 500;
    line-height: 1.35;
    word-break: break-word;
    color: #475569;
}

.gantt-table td.cell-duration {
    padding: 7px 10px;
    vertical-align: middle;
    text-align: center;
    border-right: 1px solid var(--cell-border-color);
    border-bottom: 1px solid var(--row-border-color);
    color: #1d4ed8;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.35;
    word-break: break-word;
}

/* ─────────────────────────────────────────────────────
   Badge (status dot)
   ───────────────────────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width:       var(--badge-size);
    height:      var(--badge-size);
    border-radius: 50%;
    font-size:   var(--badge-font);
    font-weight: 700;
    line-height: 1;
    color: #ffffff;
}

.badge--default     { background: #2d8fe2; }
.badge--on-progress { background: #2d8fe2; }
.badge--on-track    { background: #8fcfff; color: #214f87; }
.badge--at-risk     { background: #ffea00; color: #7b5d00; }
.badge--delayed     { background: #f97316; }
.badge--not-started { background: #2d8fe2; }
.badge--not-signed  { background: #ff1d1d; }
.badge--done        { background: #1fb34a; }
.badge--on-review   { background: #f59e0b; }

/* ─────────────────────────────────────────────────────
   Timeline cells
   ───────────────────────────────────────────────────── */
.gantt-table td.cell-gap,
.gantt-table td.cell-bar {
    position: relative;
    height: 22px;
    vertical-align: middle;
    background: #ffffff;
    border-bottom: 1px solid var(--row-border-color);
    overflow: visible;
}

.gantt-table td.cell-bar { background: transparent; }

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
    background: #0b2a8a;
}

.gantt-table td.cell-bar--approved::after {
    background: linear-gradient(90deg, #34d399 0%, #16a34a 100%);
}

.gantt-table td.cell-gap--placeholder { background: #fafcff; }

.review-marker {
    position: absolute;
    inset: 0;
    z-index: 3;
    pointer-events: none;
}

.review-marker__diamond {
    position: absolute;
    top: 50%;
    width: 9px;
    height: 9px;
    border-radius: 1px;
    transform: translate(-50%, -50%) rotate(45deg);
    box-shadow: 0 0 0 2px #ffffff, 0 0 0 3px rgba(11, 42, 138, 0.12);
}

/* Year boundary — dashed vertical separator */
.gantt-table td.year-sep { border-right: 1px dashed #cbd5e0; }

/* ─────────────────────────────────────────────────────
   Empty state
   ───────────────────────────────────────────────────── */
.empty-state {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 40px;
    text-align: center;
}

.empty-state__text {
    font-size: 13px;
    color: #64748b;
}

/* ─────────────────────────────────────────────────────
   Legend panel
   ───────────────────────────────────────────────────── */
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

.legend-item--active { border-color: #5d8cc0; background: #eef6ff; }
.legend-item--muted  { opacity: 0.5; }

.legend-swatch {
    display: inline-flex;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
}

.timeline-swatch--baseline {
    background: #0b2a8a;
}

.timeline-swatch--approved {
    background: linear-gradient(90deg, #34d399 0%, #16a34a 100%);
}

.legend-swatch--diamond {
    width: 10px;
    height: 10px;
    border-radius: 2px;
    transform: rotate(45deg);
}

.legend-label  { font-size: 11px; font-weight: 600; color: #475569; }
.legend-toggle { font-size: 10px; font-weight: 700; color: #6b7280; }
.legend-count  { color: #64748b;  font-weight: 700; }

/* ─────────────────────────────────────────────────────
   Period filter
   ───────────────────────────────────────────────────── */
.period-filter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 8px;
    border: 1px solid #d6e2ee;
    border-radius: 10px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.period-filter__label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: #59769a;
    white-space: nowrap;
}

.period-filter__select {
    min-width: 150px;
    height: 32px;
    border: 1px solid #c8d6e4;
    border-radius: 8px;
    background: #ffffff;
    padding: 6px 26px 6px 9px;
    font-size: 10px;
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

/* ─────────────────────────────────────────────────────
   Responsive
   ───────────────────────────────────────────────────── */
@media (max-width: 1280px) {
    .col-coe        { width: 13%; }
    .col-initiative { width: 27%; }
    .col-duration   { width: 11%; }
    .col-month      { width: calc(60% / var(--cellcount)); }
    .ini-name       { font-size: 10px; }
    .badge          { width: 15px; height: 15px; font-size: 8px; }
    .legend-list    { gap: 8px 14px; }
    .period-filter__select { min-width: 138px; }
}

@media (max-width: 900px) {
    .col-coe              { width: 15%; }
    .col-initiative       { width: 32%; }
    .col-duration         { width: 12%; }
    .col-month            { width: calc(53% / var(--cellcount)); }
    .legend-panel         { padding: 12px 14px; }
    .legend-item--button  { width: 100%; justify-content: space-between; }
    .legend-label         { font-size: 10px; }
    .period-filter        { width: 100%; justify-content: space-between; }
    .period-filter__select { min-width: 0; width: 100%; }
}
</style>
