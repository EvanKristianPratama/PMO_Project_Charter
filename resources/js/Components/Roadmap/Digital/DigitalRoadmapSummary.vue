<script setup>
import { computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    startYear: {
        type: Number,
        default: 2024,
    },
    endYear: {
        type: Number,
        default: 2029,
    },
});

const roadmapYears = computed(() => {
    const start = props.startYear || 2024;
    const end = props.endYear || 2029;  
    const list = [];
    for (let y = start; y <= end; y++) {
        list.push(y);
    }
    return list;
});

const roadmapSections = computed(() => {
    const items = props.items || [];
    if (!items.length) return [];

    const startGlobal = props.startYear || 2024;
    const sections = new Map();

    items.forEach(it => {
        const sectionLabel = String(it.type || 'Roadmap Activity').trim() || 'Roadmap Activity';
        if (!sections.has(sectionLabel)) sections.set(sectionLabel, []);
        
        const sY = Number(it.startYear || it.start_year);
        const eY = Number(it.endYear || it.end_year);
        const sQ = Number(String(it.startQ || it.start_q || '1').replace(/\D/g, '')) || 1;
        const eQ = Number(String(it.endQ || it.end_q || '4').replace(/\D/g, '')) || 4;
        
        const startIdx = (sY - startGlobal) * 4 + (sQ - 1);
        const endIdx = (eY - startGlobal) * 4 + (eQ - 1);

        sections.get(sectionLabel).push({
            activity: it.activity || it.title || '-',
            start: startIdx,
            end: endIdx,
            hasTimeline: true
        });
    });
    
    return [...sections.entries()].map(([label, rows]) => ({ label, rows }));
});

const quarterCells = computed(() =>
    roadmapYears.value.flatMap((year) => [1, 2, 3, 4].map((quarter) => ({ year, quarter })))
);

const isActivityActive = (row, qIdx) => {
    return row.hasTimeline && qIdx >= row.start && qIdx <= row.end;
};
</script>

<template>
    <div class="roadmap-summary-wrap overflow-x-auto">
        <table class="roadmap-table-evaluation w-full text-[10px] border-collapse" :style="{ '--qcount': Math.max(quarterCells.length, 1) }">
            <colgroup>
                <!-- Activity column -->
                <col style="width: 385px;">
                <!-- Quarter columns - equal width for all quarters -->
                <col v-for="(_, i) in quarterCells" :key="`col-q-${i}`" 
                     :style="{ width: `calc((100% - 485px) / ${quarterCells.length})` }">
                <!-- Status Updated column -->
                <col style="width: 100px;">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2" class="th-eval"></th>
                    <th v-for="year in roadmapYears" :key="`ey-${year}`" colspan="4" class="th-eval th-year-eval">{{ year }}</th>
                    <th rowspan="2" class="th-eval bg-slate-50 border-l-[#3b82f6]"></th>
                </tr>
                <tr>
                    <th v-for="(cell, i) in quarterCells" :key="`eqh-${i}`" 
                        class="th-q-eval"
                        :class="{ 'border-r-blue-eval': cell.quarter === 4 }"
                    >Q{{ cell.quarter }}</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(section, si) in roadmapSections" :key="`esec-${si}`">
                    <!-- Section Header -->
                    <tr class="row-section-eval">
                        <td v-for="(cell, i) in quarterCells" :key="`esg-${si}-${i}`"
                            class="cell-section-gap-eval"
                            :class="{ 'border-r-blue-eval': cell.quarter === 4 }"
                        ></td>
                        <td class="bg-slate-50/50 border-l border-[#b9d1e8]"></td>
                    </tr>
                    <!-- Activities -->
                    <tr v-for="(row, ri) in section.rows" :key="`erow-${si}-${ri}`" class="row-data-eval">
                        <td class="cell-activity-eval">{{ row.activity }}</td>
                        <td v-for="(cell, i) in quarterCells" :key="`etl-${si}-${ri}-${i}`"
                            class="cell-tl-eval"
                            :class="{ 
                                'cell-tl-active-eval': isActivityActive(row, i),
                                'border-r-blue-eval': cell.quarter === 4 
                            }"
                        ></td>
                        <td class="bg-slate-50/30 border-l border-[#b9d1e8]"></td>
                    </tr>
                </template>
                <tr v-if="!roadmapSections.length">
                    <td :colspan="quarterCells.length + 2" class="px-4 py-8 text-center text-slate-500 italic">No roadmap activities found.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.roadmap-summary-wrap {
    font-family: "Segoe UI", Arial, sans-serif;
}

.roadmap-table-evaluation {
    border: 1px solid #3b82f6;
    background: #fff;
    table-layout: fixed;
}

.th-eval {
    color: #fff;
    padding: 4px 8px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid #3b82f6;
    text-align: center;
    vertical-align: middle;
}

.th-year-eval {
    background: #1e4f8f;
}

.th-q-eval {
    background: #e2f0fb;
    color: #1c75bc;
    padding: 2px 4px;
    font-size: 9px;
    font-weight: 700;
    border: 1px solid #b9d1e8;
    text-align: center;
}

.row-section-eval {
    background: #f8fafc;
}

.cell-section-eval {
    font-size: 10px;
    font-weight: 700;
    color: #1e4f8f;
    padding: 4px 8px 4px 12px;
    border-left: 3px solid #1e4f8f;
    border-right: 1px solid #b9d1e8;
    border-bottom: 1px solid #b9d1e8;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cell-section-gap-eval {
    background: #f1f5f9;
    border-right: 1px solid #b9d1e8;
    border-bottom: 1px solid #b9d1e8;
}

.row-data-eval {
    background: #fff;
}

.cell-activity-eval {
    padding: 4px 8px 4px 20px;
    font-size: 10px;
    color: #334155;
    border-right: 1px solid #b9d1e8;
    border-bottom: 1px solid #b9d1e8;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    vertical-align: top;
}

.cell-tl-eval {
    padding: 0;
    height: 18px;
    background: #fff;
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}

.cell-tl-active-eval {
    background: #111111;
}

.border-r-blue-eval {
    border-right: 1.5px solid #3b82f6 !important;
}
</style>
