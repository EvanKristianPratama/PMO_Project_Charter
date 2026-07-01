<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { useRouteHelper } from "@/Composables/useRouteHelper";
import UserLayout from "@/Layouts/UserLayout.vue";
import RoadmapSummaryTable from "@/Components/modules/ITSP/StrategicHouse/RoadMap/RoadmapSummaryTable.vue";

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
        hasTimeline: r.has_timeline !== false,
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
        bar: "#dc2626",
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
        <div class="summary-page">
            <!-- Back link -->
            <section
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <Link
                    :href="route('itsp.strategic-house.index')"
                    class="text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]"
                >
                    ← Kembali ke Strategic House
                </Link>
            </section>

            <!-- Title -->
            <section class="summary-header">
                <div class="summary-header__accent" aria-hidden="true">
                    <span class="summary-header__accent-red" />
                    <span class="summary-header__accent-green" />
                </div>
                <div class="summary-header__copy">
                    <h1 class="summary-header__title">Program Initiative Roadmap Summary</h1>
                </div>
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
                        <tr class="year-banner-row year-banner-row--years">
                            <td colspan="2" class="year-banner-cell year-banner-cell--empty" />
                            <td
                                v-for="(year, yearIdx) in years"
                                :key="`banner-year-top-${year}`"
                                colspan="4"
                                class="year-banner-cell"
                            >
                                <div
                                    :class="[
                                        'year-banner-chevron',
                                        yearIdx === 0 ? 'year-banner-chevron--green' : 'year-banner-chevron--blue',
                                        yearIdx === 0 ? 'year-banner-chevron--first' : '',
                                        yearIdx === years.length - 1 ? 'year-banner-chevron--last' : '',
                                    ]"
                                >
                                    <div class="year-banner-cell__year">{{ year }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr class="year-banner-row year-banner-row--labels">
                            <td colspan="2" class="year-banner-cell year-banner-cell--empty" />
                            <td
                                v-for="(year, yearIdx) in years"
                                :key="`banner-year-bottom-${year}`"
                                colspan="4"
                                class="year-banner-cell"
                            >
                                <div
                                    :class="[
                                        'year-banner-chevron',
                                        yearIdx === 0 ? 'year-banner-chevron--green' : 'year-banner-chevron--blue',
                                        yearIdx === 0 ? 'year-banner-chevron--first' : '',
                                        yearIdx === years.length - 1 ? 'year-banner-chevron--last' : '',
                                        'year-banner-chevron--label',
                                    ]"
                                >
                                    <div class="year-banner-cell__label">{{ yearLabel(year) }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- IT initiatives (red bars) -->
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
.summary-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.summary-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 0 4px;
}

.summary-header__accent {
    position: relative;
    flex: 0 0 54px;
    width: 54px;
    height: 92px;
}

.summary-header__accent-red,
.summary-header__accent-green {
    position: absolute;
    inset: 0;
    display: block;
}

.summary-header__accent-red {
    width: 54px;
    height: 30px;
    background: #e53935;
    clip-path: polygon(0 0, 100% 0, 74% 100%, 0 100%);
}

.summary-header__accent-green {
    top: 6px;
    left: 0;
    width: 54px;
    height: 92px;
    background: #c4d61e;
    clip-path: polygon(28% 0, 100% 0, 42% 100%, 0 100%, 0 28%);
}

.summary-header__copy {
    min-width: 0;
}

.summary-header__title {
    font-size: 32px;
    font-weight: 900;
    color: #111111;
    letter-spacing: -0.03em;
    line-height: 1.05;
}

.summary-header__subtitle {
    margin-top: 8px;
    font-size: 15px;
    font-weight: 500;
    color: #171717;
}

/* ── Gantt wrapper ──────────────────────────────────── */
.summary-gantt-wrap {
    overflow-x: auto;
    border: none;
    border-radius: 0;
    background: transparent;
    min-width: 0;
}

.summary-gantt {
    width: 100%;
    min-width: 900px;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
}

.summary-gantt th,
.summary-gantt td {
    border: none;
    padding: 0;
    overflow: hidden;
}

/* ── Column widths ──────────────────────────────────── */
.col-section {
    width: 44px;
}

.col-label {
    width: 56px;
}

.col-quarter {
    width: calc((100% - 100px) / var(--qcount));
}

/* ── Year banner row ────────────────────────────────── */
.year-banner-body {
    background: transparent;
}

.year-banner-row {
    background: transparent;
}

.year-banner-cell {
    padding: 0 0 4px;
    text-align: center;
    vertical-align: middle;
    border-left: none;
    background: transparent;
}

.year-banner-cell--empty {
    background: transparent;
    border-right: 8px solid transparent;
}

.year-banner-chevron {
    position: relative;
    display: flex;
    min-height: 40px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4px 22px 4px 28px;
    clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%, 18px 50%);
    border: 2px solid #ffffff;
}

.year-banner-chevron--green {
    background: linear-gradient(180deg, #c7db22 0%, #bfd11d 100%);
}

.year-banner-chevron--blue {
    background: linear-gradient(180deg, #1871b8 0%, #1468ac 100%);
}

.year-banner-chevron--first {
    padding-left: 18px;
    clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
}

.year-banner-chevron--last {
    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 18px 50%);
}

.year-banner-chevron--label {
    min-height: 34px;
}

.year-banner-cell__year {
    font-size: 15px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.1;
}

.year-banner-cell__label {
    font-size: 11px;
    font-weight: 800;
    font-style: italic;
    color: #ffffff;
    letter-spacing: 0.01em;
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
    .summary-header__title {
        font-size: 28px;
    }
}

@media (max-width: 900px) {
    .summary-header {
        gap: 10px;
    }
    .summary-header__accent {
        flex-basis: 38px;
        width: 38px;
        height: 76px;
    }
    .col-section {
        width: 32px;
    }
    .col-quarter {
        width: calc((100% - 88px) / var(--qcount));
    }
    .summary-header__title {
        font-size: 22px;
    }
    .year-banner-chevron {
        min-height: 34px;
        padding: 6px 14px 6px 18px;
    }
    .year-banner-cell__year {
        font-size: 13px;
    }
    .year-banner-cell__label {
        font-size: 10px;
    }
}
</style>
