<script setup>
import { computed, ref } from "vue";

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
function getRange(projects, keyword) {
    if (!Array.isArray(projects) || projects.length === 0) return null;

    const pool = keyword
        ? projects.filter((p) =>
              String(p.version_label ?? "")
                  .toLowerCase()
                  .includes(keyword),
          )
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

function barRange(projects) {
    if (showMode.value === "baseline") return getRange(projects, "baseline");
    if (showMode.value === "approved") return getRange(projects, "approv");
    return getRange(projects, "");
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
</script>

<template>
    <div class="space-y-3">
        <!-- ── Header + Toggle ─────────────────────────── -->
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
        >
            <h1
                class="text-xl font-bold text-slate-900 dark:text-white leading-snug"
            >
                <span
                    class="underline underline-offset-2 decoration-[#1a4b8c] dark:decoration-blue-400"
                >
                    {{ totalCount }} IT strategic initiative
                </span>
                telah diidentifikasi dan diselaraskan dengan<br />
                strategi dual growth Pertamina pada tahun {{ startYear }}
            </h1>

            <!-- Toggle -->
            <div class="flex items-center gap-2 shrink-0">
                <span
                    class="text-[11px] font-medium text-slate-500 dark:text-slate-400"
                >
                    Tampilkan:
                </span>
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
                    <tr class="border-b border-slate-200 dark:border-white/10">
                        <th
                            class="th-cell bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-slate-400 border-r border-slate-200 dark:border-white/10"
                        >
                            IT Architecture Building Block
                        </th>
                        <th
                            class="th-cell th-left bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-slate-400 border-r border-slate-200 dark:border-white/10"
                        >
                            List of ITSP Initiatives {{ startYear }}-{{
                                endYear
                            }}
                        </th>
                        <th
                            v-for="year in years"
                            :key="`yr-${year}`"
                            colspan="4"
                            class="th-year bg-slate-50 dark:bg-white/5 text-slate-700 dark:text-slate-200 border-l border-slate-200 dark:border-white/10"
                        >
                            {{ year }}
                        </th>
                    </tr>
                </thead>

                <!-- Body -->
                <tbody>
                    <template
                        v-for="group in groups"
                        :key="`group-${group.coe_name}`"
                    >
                        <tr
                            v-for="(initiative, idx) in group.initiatives"
                            :key="`ini-${initiative.id}`"
                            class="border-b border-slate-100 dark:border-white/5 last:border-b-0 hover:bg-slate-50/60 dark:hover:bg-white/3 transition-colors"
                        >
                            <!-- CoE cell -->
                            <td
                                v-if="idx === 0"
                                :rowspan="coeRowspan(group)"
                                class="cell-coe bg-slate-50 dark:bg-white/5 text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-white/10"
                            >
                                {{ group.coe_name }}
                            </td>

                            <!-- Initiative name -->
                            <td
                                class="cell-initiative border-r border-slate-100 dark:border-white/5"
                            >
                                <div class="flex items-center gap-1.5">
                                    <span class="badge bg-[#1a4b8c] text-white">
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
    width: calc(66% / var(--qcount));
}

/* ── Header cells ────────────────────────────────────── */
.th-cell {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 8px 10px;
    text-align: left;
    vertical-align: middle;
    white-space: nowrap;
}

.th-left {
    text-align: left;
}

.th-year {
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    padding: 8px 4px;
    vertical-align: middle;
}

/* ── CoE cell ────────────────────────────────────────── */
.cell-coe {
    font-size: 10.5px;
    font-weight: 600;
    padding: 7px 10px;
    vertical-align: middle;
    line-height: 1.4;
    word-break: break-word;
}

/* ── Initiative cell ─────────────────────────────────── */
.cell-initiative {
    padding: 5px 8px;
    vertical-align: middle;
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
}

.ini-name {
    font-size: 11px;
    font-weight: 500;
    line-height: 1.35;
    word-break: break-word;
}

/* ── Timeline cells ──────────────────────────────────── */
.cell-gap {
    height: 16px;
}

.gantt-table td.cell-bar {
    height: 16px;
    background: #1a4b8c;
    border-radius: 2px;
}

/* Year boundary — subtle dashed separator */
.year-sep {
    border-right: 1px dashed #cbd5e0 !important;
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
}
</style>
