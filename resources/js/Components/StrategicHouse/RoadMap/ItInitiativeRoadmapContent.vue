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
const selectedPeriod = ref("");
const selectedOrganization = ref("");

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

const organizationFilterMode = computed(() => props.filterMode === "organization");

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

/** Convert a date string (YYYY-MM-...) to a quarter index relative to startYear. */
function toQIdx(value) {
    if (!value) return null;
    const m = String(value).match(/^(\d{4})-(\d{2})/);
    if (!m) return null;
    const year  = parseInt(m[1], 10);
    const month = parseInt(m[2], 10) - 1;
    const raw   = (year - props.startYear) * 4 + Math.floor(month / 3);
    return Math.max(0, Math.min(raw, totalCells.value - 1));
}

function monthToQuarterIndex(monthName) {
    const monthIndex = monthsOrder.indexOf(String(monthName ?? "").trim());

    if (monthIndex < 0) {
        return null;
    }

    return Math.floor(monthIndex / 3);
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

/* ── Range computation ───────────────────────────────── */

/** Compute the quarter-index range that covers all milestones in `projects`. */
function getRange(projects, statusFilter) {
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

/* ── Cell builder ────────────────────────────────────── */

/** Build an array of cell descriptors (gap | bar) for a given quarter range. */
function buildCells(range, keyPrefix = "default") {
    const total = totalCells.value;

    if (!range) {
        return Array.from({ length: total }, (_, i) => ({
            type: "gap",
            span: 1,
            key: `${keyPrefix}-g${i}`,
            startIndex: i,
            endIndex: i,
            endsYear: (i + 1) % 4 === 0,
        }));
    }

    const { start, end } = range;
    const safeStart = Math.max(0, Math.min(start, total - 1));
    const safeEnd   = Math.max(safeStart, Math.min(end, total - 1));
    const cells     = [];
    let   c         = 0;

    while (c < safeStart) {
        cells.push({ type: "gap", span: 1, key: `${keyPrefix}-g${c}`, startIndex: c, endIndex: c, endsYear: (c + 1) % 4 === 0 });
        c++;
    }

    cells.push({
        type: "bar",
        span: safeEnd - safeStart + 1,
        key: `${keyPrefix}-bar-${safeStart}`,
        startIndex: safeStart,
        endIndex: safeEnd,
        endsYear: (safeEnd + 1) % 4 === 0,
    });
    c = safeEnd + 1;

    while (c < total) {
        cells.push({ type: "gap", span: 1, key: `${keyPrefix}-g${c}`, startIndex: c, endIndex: c, endsYear: (c + 1) % 4 === 0 });
        c++;
    }

    return cells;
}

/**
 * Build the set of timeline rows for a single initiative.
 * Returns one row per visible roadmap layer that has data,
 * or a single placeholder row when no layer has data.
 */
 function buildInitiativeTimelineRows(initiative, reviewStatus = null) {
    const initiativeId = initiative?.id ?? "initiative";
    const projects     = Array.isArray(initiative?.projects) ? initiative.projects : [];
    const rows         = [];

    const roadmapRows = roadmapLegendItems
        .filter((item) => isRoadmapLayerVisible(item.key))
        .map((item) => {
            const range = getRange(projects, item.key);
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

    const reviewMarker = !organizationFilterMode.value && selectedPeriodRange.value
        ? {
            anchorIndex: selectedPeriodRange.value.anchorIndex ?? selectedPeriodRange.value.start,
            color: statusMarkerColor(reviewStatus),
            label: selectedPeriodRange.value.label,
            statusLabel: normalizeStatusLabel(reviewStatus),
        }
        : null;

    if (reviewMarker && roadmapRows.length > 0) {
        roadmapRows[0].reviewMarker = reviewMarker;
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

    if (reviewMarker) {
        fallbackRows[0].reviewMarker = reviewMarker;
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

        reviewStatuses.forEach((statusItem) => {
            const value      = String(statusItem?.period_key    ?? "").trim();
            const label      = String(statusItem?.periode_label ?? "").trim();
            const year       = Number(statusItem?.year          ?? 0);
            const startMonth = String(statusItem?.start         ?? "").trim();
            const endMonth   = String(statusItem?.end           ?? "").trim();

            if (!value || !label || periods.has(value)) return;

            periods.set(value, { value, label, year, startMonth, endMonth });
        });
    });

    return Array.from(periods.values()).sort((left, right) => {
        if (left.year !== right.year) return right.year - left.year;

        const li = monthsOrder.indexOf(left.startMonth);
        const ri = monthsOrder.indexOf(right.startMonth);
        if (li !== ri) return ri - li;

        return monthsOrder.indexOf(right.endMonth) - monthsOrder.indexOf(left.endMonth);
    });
});

// Auto-select the first available period; reset when the list becomes empty.
watch(
    availablePeriods,
    (periods) => {
        if (!Array.isArray(periods) || periods.length === 0) {
            selectedPeriod.value = "";
            return;
        }
        const stillValid = periods.some((p) => p.value === selectedPeriod.value);
        if (!stillValid) selectedPeriod.value = periods[0].value;
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

/**
 * Return the implementation status and period label for an initiative
 * at a specific review period. Falls back to `implementation_status` when
 * no period-specific record is found.
 */
function resolveInitiativeStatusByPeriod(initiative, periodValue) {
    const reviewStatuses = Array.isArray(initiative?.review_statuses)
        ? initiative.review_statuses
        : [];

    if (reviewStatuses.length === 0) {
        return { status: initiative?.implementation_status ?? null, period: null };
    }

    const selected = reviewStatuses.findLast(
        (item) => String(item?.period_key ?? "") === String(periodValue),
    );

    return {
        status: selected?.review_status ?? initiative?.implementation_status ?? null,
        period: selected?.periode_label ?? null,
    };
}

const selectedFilterLabel = computed(() => {
    if (organizationFilterMode.value) {
        if (!selectedOrganization.value) {
            return "Semua Organisasi";
        }

        return (
            availableOrganizations.value.find(
                (item) => item.value === selectedOrganization.value,
            )?.label ?? "-"
        );
    }

    return availablePeriods.value.find((p) => p.value === selectedPeriod.value)?.label ?? "-";
});

const selectedPeriodMeta = computed(
    () => availablePeriods.value.find((p) => p.value === selectedPeriod.value) ?? null,
);

const selectedPeriodRange = computed(() => {
    const period = selectedPeriodMeta.value;

    if (!period || organizationFilterMode.value) {
        return null;
    }

    const year = Number(period.year ?? 0);
    const startQuarter = monthToQuarterIndex(period.startMonth);
    const endQuarter = monthToQuarterIndex(period.endMonth);

    if (!Number.isFinite(year) || startQuarter === null) {
        return null;
    }

    const safeEndQuarter = endQuarter ?? startQuarter;
    const start = (year - props.startYear) * 4 + startQuarter;
    const end = (year - props.startYear) * 4 + safeEndQuarter;
    const anchorIndex = Math.max(
        0,
        Math.min(Math.floor((start + end) / 2), totalCells.value - 1),
    );

    return {
        start: Math.min(start, end),
        end: Math.max(start, end),
        anchorIndex,
        label: period.label,
    };
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
            const timelineRows = buildInitiativeTimelineRows(initiative, periodState.status);

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
    if (!organizationFilterMode.value || !selectedOrganization.value) {
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
                if (organizationFilterMode.value && selectedOrganization.value) {
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
                        <label class="period-filter">
                            <span class="period-filter__label">
                                {{ organizationFilterMode ? 'Organization' : 'Status Review Implementation Period' }}
                            </span>
                            <select
                                v-if="organizationFilterMode"
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
                            <select v-else v-model="selectedPeriod" class="period-filter__select">
                                <option v-if="availablePeriods.length === 0" value="" disabled>
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
                :style="{ '--qcount': Math.max(totalCells, 1) }"
            >
                <colgroup>
                    <col class="col-coe" />
                    <col class="col-initiative" />
                    <col class="col-duration" />
                    <col
                        v-for="(_, i) in quarterCells"
                        :key="`qcol-${i}`"
                        class="col-quarter"
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
                            colspan="4"
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
                                        v-if="timelineRow.reviewMarker && cell.startIndex <= timelineRow.reviewMarker.anchorIndex && timelineRow.reviewMarker.anchorIndex <= cell.endIndex"
                                        class="review-marker"
                                        :title="`${timelineRow.reviewMarker.label} · ${timelineRow.reviewMarker.statusLabel || '-'}`"
                                    >
                                        <span
                                            class="review-marker__diamond"
                                            :style="{
                                                backgroundColor: timelineRow.reviewMarker.color,
                                                boxShadow: `0 0 0 2px #ffffff, 0 0 0 3px ${timelineRow.reviewMarker.color}26`,
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
                        <label class="period-filter">
                            <span class="period-filter__label">
                                {{ organizationFilterMode ? 'Organization' : 'Status Review Implementation Period' }}
                            </span>
                            <select
                                v-if="organizationFilterMode"
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
                            <select v-else v-model="selectedPeriod" class="period-filter__select">
                                <option v-if="availablePeriods.length === 0" value="" disabled>
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
.col-quarter    { width: calc(40% / var(--qcount)); }

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
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 3;
    pointer-events: none;
}

.review-marker__diamond {
    display: block;
    width: 12px;
    height: 12px;
    border-radius: 2px;
    transform: rotate(45deg);
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

/* ─────────────────────────────────────────────────────
   Responsive
   ───────────────────────────────────────────────────── */
@media (max-width: 1280px) {
    .col-coe        { width: 13%; }
    .col-initiative { width: 27%; }
    .col-duration   { width: 11%; }
    .col-quarter    { width: calc(60% / var(--qcount)); }
    .ini-name       { font-size: 10px; }
    .badge          { width: 15px; height: 15px; font-size: 8px; }
    .legend-list    { gap: 8px 14px; }
    .period-filter__select { min-width: 170px; }
}

@media (max-width: 900px) {
    .col-coe              { width: 15%; }
    .col-initiative       { width: 32%; }
    .col-duration         { width: 12%; }
    .col-quarter          { width: calc(53% / var(--qcount)); }
    .legend-panel         { padding: 12px 14px; }
    .legend-item--button  { width: 100%; justify-content: space-between; }
    .legend-label         { font-size: 10px; }
    .period-filter        { width: 100%; justify-content: space-between; }
    .period-filter__select { min-width: 0; width: 100%; }
}
</style>
