<script setup>
import { computed } from 'vue';

const props = defineProps({
    project: { type: Object, required: true },
    form: { type: Object, required: true },
});

const objectives = computed(() => String(props.form?.objectives || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean));

const toLineItems = (value) => String(value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

const parseDateMeta = (value) => {
    if (!value) {
        return null;
    }

    const matched = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

    if (matched) {
        return {
            year: Number(matched[1]),
            monthIndex: Number(matched[2]) - 1,
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
};

const validMilestones = computed(() => (props.project?.milestones || [])
    .filter((item) => item?.start_date || item?.end_date)
    .sort((a, b) => {
        const aOrder = Number(a.order ?? 0);
        const bOrder = Number(b.order ?? 0);

        if (aOrder !== bOrder) {
            return aOrder - bOrder;
        }

        return String(a.start_date || '').localeCompare(String(b.start_date || ''));
    }));

const timelineBounds = computed(() => {
    const yearsFromMilestones = validMilestones.value.flatMap((item) => {
        const start = parseDateMeta(item.start_date)?.year ?? null;
        const end = parseDateMeta(item.end_date)?.year ?? null;

        return [start, end].filter((value) => Number.isFinite(value));
    });

    if (yearsFromMilestones.length > 0) {
        const minYear = Math.min(...yearsFromMilestones);
        const maxYear = Math.max(...yearsFromMilestones);
        const count = Math.max(3, maxYear - minYear + 1);

        return {
            startYear: minYear,
            yearCount: count,
        };
    }

    const duration = String(props.form?.duration || '');
    const matched = duration.match(/(20\d{2}).*?(20\d{2})/);

    if (matched) {
        const startYear = Number(matched[1]);
        const endYear = Number(matched[2]);

        if (Number.isFinite(startYear) && Number.isFinite(endYear) && endYear >= startYear) {
            return {
                startYear,
                yearCount: Math.max(3, endYear - startYear + 1),
            };
        }
    }

    const currentYear = new Date().getFullYear();

    return {
        startYear: currentYear,
        yearCount: 3,
    };
});

const years = computed(() => Array.from(
    { length: timelineBounds.value.yearCount },
    (_, index) => timelineBounds.value.startYear + index
));

const quarterCells = computed(() => years.value.flatMap((year) => ([
    { year, quarter: 1 },
    { year, quarter: 2 },
    { year, quarter: 3 },
    { year, quarter: 4 },
])));

const totalQuarterCells = computed(() => quarterCells.value.length);

const quarterIndexByDate = (dateValue) => {
    const meta = parseDateMeta(dateValue);

    if (!meta) {
        return null;
    }

    const yearOffset = meta.year - timelineBounds.value.startYear;
    const quarter = Math.floor(meta.monthIndex / 3);
    const index = (yearOffset * 4) + quarter;

    if (index < 0 || index >= totalQuarterCells.value) {
        return null;
    }

    return index;
};

const sectionLabelForType = (type) => {
    const normalized = String(type || '').toLowerCase();

    if (normalized.includes('rollout')) {
        return 'Rollout';
    }

    if (normalized.includes('assess') || normalized.includes('design') || normalized.includes('analysis')) {
        return 'Assessment and Design';
    }

    return 'Assessment and Design';
};

const buildRowsFromMilestones = () => {
    const rows = [];
    let objectiveIndex = 0;
    let usedObjectiveFallback = false;

    for (const milestone of validMilestones.value) {
        const startIndex = quarterIndexByDate(milestone.start_date ?? milestone.end_date);
        const endIndex = quarterIndexByDate(milestone.end_date ?? milestone.start_date);
        const rangedStart = startIndex ?? 0;
        const rangedEnd = endIndex ?? rangedStart;
        const normalizedStart = Math.max(0, Math.min(rangedStart, rangedEnd));
        const normalizedEnd = Math.min(totalQuarterCells.value - 1, Math.max(rangedStart, rangedEnd));
        let output = toLineItems(milestone.output);

        if (output.length === 0 && objectives.value[objectiveIndex]) {
            output = [objectives.value[objectiveIndex]];
            objectiveIndex += 1;
            usedObjectiveFallback = true;
        }

        rows.push({
            section: sectionLabelForType(milestone.type),
            activity: milestone.title || `Milestone ${rows.length + 1}`,
            start: normalizedStart,
            end: normalizedEnd,
            output,
        });
    }

    if (rows.length > 0 && usedObjectiveFallback && objectiveIndex < objectives.value.length) {
        rows[rows.length - 1].output.push(...objectives.value.slice(objectiveIndex));
    }

    return rows;
};

const buildRowsFromObjectives = () => {
    const objectiveRows = objectives.value.length > 0
        ? objectives.value
        : ['Define project objectives and rollout plan'];
    const span = Math.max(1, Math.floor(totalQuarterCells.value / objectiveRows.length));

    return objectiveRows.map((objective, index) => {
        const start = Math.min(totalQuarterCells.value - 1, index * span);
        const end = Math.min(totalQuarterCells.value - 1, start + Math.max(0, span - 1));

        return {
            section: index < Math.ceil(objectiveRows.length / 2) ? 'Assessment and Design' : 'Rollout',
            activity: objective.length > 80 ? `${objective.slice(0, 77)}...` : objective,
            start,
            end,
            output: [objective],
        };
    });
};

const roadmapSections = computed(() => {
    const rows = validMilestones.value.length > 0
        ? buildRowsFromMilestones()
        : buildRowsFromObjectives();

    const orderedSections = ['Assessment and Design', 'Rollout'];
    const grouped = orderedSections.map((label) => ({
        label,
        rows: rows.filter((row) => row.section === label),
    })).filter((section) => section.rows.length > 0);

    return grouped.length > 0 ? grouped : [{
        label: 'Assessment and Design',
        rows,
    }];
});

const isQuarterActive = (row, quarterIndex) => quarterIndex >= row.start && quarterIndex <= row.end;
</script>

<template>
    <section class="roadmap-sheet mt-6 border border-slate-300 bg-[#e9e9e9] p-6 text-slate-900 shadow-sm print:mt-0 print:break-before-page">
        <header class="border-b-2 border-[#2e6ea2] pb-2">
            <h2 class="text-4xl font-extrabold leading-tight tracking-tight">
                Project Roadmap: {{ project.name || '-' }}
            </h2>
        </header>

        <div class="mt-4 overflow-x-auto">
            <table class="roadmap-table w-full border-collapse border border-[#2e6ea2] bg-transparent">
                <thead>
                    <tr class="h-10">
                        <th rowspan="2" class="th-main w-[24%] min-w-[280px] text-left">ACTIVITY</th>
                        <th
                            v-for="year in years"
                            :key="`year-${year}`"
                            colspan="4"
                            class="th-year"
                        >
                            {{ year }}
                        </th>
                        <th rowspan="2" class="th-main w-[38%] min-w-[360px] text-left">OUTPUT</th>
                    </tr>
                    <tr>
                        <th
                            v-for="(cell, index) in quarterCells"
                            :key="`q-head-${cell.year}-${cell.quarter}-${index}`"
                            class="th-quarter"
                            :class="{ 'th-quarter-last': cell.quarter === 4 }"
                        >
                            Q{{ cell.quarter }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="section in roadmapSections" :key="section.label">
                        <tr class="section-row">
                            <td class="section-title">{{ section.label }}</td>
                            <td
                                v-for="(_, index) in quarterCells"
                                :key="`section-gap-${section.label}-${index}`"
                                class="section-gap"
                                :class="{ 'timeline-divider': quarterCells[index].quarter === 4 }"
                            ></td>
                            <td class="section-gap"></td>
                        </tr>

                        <tr v-for="(row, rowIndex) in section.rows" :key="`row-${section.label}-${rowIndex}`" class="data-row">
                            <td class="activity-cell">{{ row.activity }}</td>

                            <td
                                v-for="(cell, index) in quarterCells"
                                :key="`quarter-${section.label}-${rowIndex}-${index}`"
                                class="timeline-cell"
                                :class="{
                                    'timeline-active': isQuarterActive(row, index),
                                    'timeline-divider': cell.quarter === 4,
                                }"
                            ></td>

                            <td class="output-cell">
                                <ul class="output-list" v-if="row.output.length">
                                    <li v-for="(item, idx) in row.output" :key="`output-${section.label}-${rowIndex}-${idx}`">
                                        {{ item }}
                                    </li>
                                </ul>
                                <span v-else>-</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
.roadmap-sheet {
    font-family: "Segoe UI", Inter, Arial, sans-serif;
}

.roadmap-table {
    border: 2px solid #2e6ea2;
}

.th-main {
    background: #0f63b5;
    color: #fff;
    border: 1px solid #2e6ea2;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.02em;
}

.th-year {
    background: #0f63b5;
    color: #fff;
    border: 1px solid #2e6ea2;
    padding: 6px 0;
    font-size: 14px;
    font-weight: 800;
    text-align: center;
}

.th-quarter {
    background: #f6f7fa;
    border: 1px solid #b8b8b8;
    border-right: 2px dotted #6b7280;
    padding: 6px 0;
    width: 52px;
    min-width: 52px;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    color: #111827;
}

.th-quarter-last {
    border-right: 2px solid #2e6ea2;
}

.section-row td {
    height: 28px;
    border: 1px solid #2e6ea2;
    background: #0f63b5;
}

.section-title {
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    padding: 0 14px;
}

.section-gap {
    padding: 0;
}

.activity-cell {
    border: 1px solid #b8b8b8;
    padding: 10px 14px;
    font-size: 16px;
    line-height: 1.25;
    vertical-align: top;
    background: #e9e9e9;
}

.timeline-cell {
    border: 1px solid #b8b8b8;
    border-right: 2px dotted #6b7280;
    width: 52px;
    min-width: 52px;
    height: 44px;
    background: #e9e9e9;
}

.timeline-cell.timeline-divider {
    border-right: 2px solid #2e6ea2;
}

.timeline-active {
    background: #49c9c2;
}

.output-cell {
    border: 1px solid #b8b8b8;
    padding: 8px 14px;
    vertical-align: top;
    font-size: 16px;
    line-height: 1.3;
    background: #e9e9e9;
}

.output-list {
    margin: 0;
    padding-left: 18px;
}

.output-list li + li {
    margin-top: 4px;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    .roadmap-sheet {
        margin: 0;
        border: 0;
        padding: 0;
        box-shadow: none;
        background: #ffffff !important;
    }

    .activity-cell,
    .timeline-cell,
    .output-cell,
    .th-quarter {
        background: #ffffff !important;
    }

    .timeline-active {
        background: #49c9c2 !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .th-main,
    .th-year,
    .section-row td {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
