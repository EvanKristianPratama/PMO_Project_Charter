<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { useRouteHelper } from "@/Composables/useRouteHelper";
import UserLayout from "@/Layouts/UserLayout.vue";
import RoadmapSummaryTable from "@/Components/StrategicHouse/RoadMap/RoadmapSummaryTable.vue";

const route = useRouteHelper();

const props = defineProps({
    digitalGroups: { type: Array, default: () => [] },
    itGroups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2024 },
    endYear: { type: Number, default: 2029 },
    yearLabels: { type: Array, default: () => [] },
});

/* ── Grid helpers ────────────────────────────────────── */
const years = computed(() =>
    Array.from(
        { length: props.endYear - props.startYear + 1 },
        (_, i) => props.startYear + i,
    ),
);

const totalCells = computed(() => years.value.length * 4);

/** Map backend row shape to what RoadmapSummaryTable expects. */
function normalizeRows(rows) {
    return (Array.isArray(rows) ? rows : []).map((r) => ({
        label: r.label ?? "-",
        count: r.count ?? 0,
        startYear: r.start_year ?? props.startYear,
        startQuarter: r.start_quarter ?? 1,
        endYear: r.end_year ?? props.endYear,
        endQuarter: r.end_quarter ?? 4,
    }));
}

/** Section colours matching the reference image. */
const SECTION_COLORS = {
    digital: {
        bar: "#dc2626",
        section: "#b7cd26",
    },
    it: {
        bar: "#1d4ed8",
        section: "#b7cd26",
    },
};

const normalizedDigitalGroups = computed(() =>
    (Array.isArray(props.digitalGroups) ? props.digitalGroups : []).map((g) => ({
        sectionLabel: g.section_label ?? "-",
        rows: normalizeRows(g.rows),
    })),
);

const normalizedItGroups = computed(() =>
    (Array.isArray(props.itGroups) ? props.itGroups : []).map((g) => ({
        sectionLabel: g.section_label ?? "IT Initiative",
        rows: normalizeRows(g.rows),
    })),
);

const hasData = computed(
    () =>
        normalizedDigitalGroups.value.some((g) => g.rows.length > 0) ||
        normalizedItGroups.value.some((g) => g.rows.length > 0),
);

function yearLabel(year) {
    return props.yearLabels?.find((y) => y.year === year)?.label ?? "";
}
</script>

<template>
    <UserLayout title="Program Initiative Roadmap Summary">
        <div class="space-y-5">
            <!-- Back link -->
            <section
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <Link
                    :href="route('strategic-house.index')"
                    class="text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]"
                >
                    ← Kembali ke Strategic House
                </Link>
            </section>

            <!-- Title -->
            <section class="summary-header">
                <h1 class="summary-header__title">Program Initiative Roadmap Summary</h1>
                <p class="summary-header__subtitle">Review Dokumen Rencana Strategis TI (RSTI)</p>
            </section>

            <!-- Empty state -->
            <div
                v-if="!hasData"
                class="empty-state"
            >
                <p class="empty-state__text">
                    Belum ada data roadmap untuk ditampilkan.
                </p>
            </div>

            <!-- Summary Gantt -->
            <div v-else class="summary-gantt-wrap">
                <table
                    class="summary-gantt"
                    :style="{ '--qcount': Math.max(totalCells, 1) }"
                >
                    <colgroup>
                        <col class="col-section" />
                        <col class="col-label" />
                        <col
                            v-for="(_, i) in totalCells"
                            :key="`qcol-${i}`"
                            class="col-quarter"
                        />
                    </colgroup>

                    <!-- Digital initiatives (red bars) -->
                    <tbody
                        v-for="group in normalizedDigitalGroups"
                        :key="`digital-${group.sectionLabel}`"
                    >
                        <RoadmapSummaryTable
                            :section-label="group.sectionLabel"
                            :rows="group.rows"
                            :start-year="startYear"
                            :end-year="endYear"
                            :bar-color="SECTION_COLORS.digital.bar"
                            :section-color="SECTION_COLORS.digital.section"
                        />
                    </tbody>

                    <!-- Year label banner row -->
                    <tbody class="year-banner-body">
                        <tr class="year-banner-row">
                            <td colspan="2" class="year-banner-cell year-banner-cell--empty" />
                            <td
                                v-for="year in years"
                                :key="`banner-year-${year}`"
                                colspan="4"
                                class="year-banner-cell"
                            >
                                <div class="year-banner-cell__year">{{ year }}</div>
                                <div class="year-banner-cell__label">{{ yearLabel(year) }}</div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- IT initiatives (blue bars) -->
                    <tbody
                        v-for="group in normalizedItGroups"
                        :key="`it-${group.sectionLabel}`"
                    >
                        <RoadmapSummaryTable
                            :section-label="group.sectionLabel"
                            :rows="group.rows"
                            :start-year="startYear"
                            :end-year="endYear"
                            :bar-color="SECTION_COLORS.it.bar"
                            :section-color="SECTION_COLORS.it.section"
                        />
                    </tbody>
                </table>
            </div>
        </div>
    </UserLayout>
</template>

<style scoped>
/* ── Summary header ─────────────────────────────────── */
.summary-header {
    padding: 0 4px;
}

.summary-header__title {
    font-size: 26px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.01em;
    line-height: 1.2;
}

.summary-header__subtitle {
    margin-top: 4px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
}

/* ── Gantt wrapper ──────────────────────────────────── */
.summary-gantt-wrap {
    overflow-x: auto;
    border: 2px solid #1c75bc;
    border-radius: 6px;
    background: #ffffff;
    min-width: 0;
}

.summary-gantt {
    width: 100%;
    min-width: 900px;
    table-layout: fixed;
    border-collapse: collapse;
}

.summary-gantt th,
.summary-gantt td {
    border: none;
    padding: 0;
    overflow: hidden;
}

/* ── Column widths ──────────────────────────────────── */
.col-section {
    width: 40px;
}

.col-label {
    width: 200px;
}

.col-quarter {
    width: calc((100% - 240px) / var(--qcount));
}

/* ── Year banner row ────────────────────────────────── */
.year-banner-body {
    border-top: 2px solid #8ca9c7;
    border-bottom: 2px solid #8ca9c7;
}

.year-banner-row {
    background: linear-gradient(180deg, #c5d830 0%, #a8ba1c 100%);
}

.year-banner-cell {
    padding: 8px 4px;
    text-align: center;
    vertical-align: middle;
    border-left: 2px solid #8ca9c7;
}

.year-banner-cell--empty {
    background: linear-gradient(180deg, #c5d830 0%, #a8ba1c 100%);
    border-left: none;
}

.year-banner-cell__year {
    font-size: 16px;
    font-weight: 800;
    color: #1a2e44;
    line-height: 1.2;
}

.year-banner-cell__label {
    font-size: 10px;
    font-weight: 700;
    color: #3d5a1e;
    letter-spacing: 0.02em;
    font-style: italic;
}

/* ── Empty state ────────────────────────────────────── */
.empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    background: #ffffff;
    padding: 40px;
    text-align: center;
}

.empty-state__text {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 1280px) {
    .col-label {
        width: 170px;
    }
    .summary-header__title {
        font-size: 22px;
    }
}

@media (max-width: 900px) {
    .col-label {
        width: 140px;
    }
    .col-section {
        width: 32px;
    }
    .col-quarter {
        width: calc((100% - 172px) / var(--qcount));
    }
    .summary-header__title {
        font-size: 18px;
    }
    .year-banner-cell__year {
        font-size: 13px;
    }
}
</style>
