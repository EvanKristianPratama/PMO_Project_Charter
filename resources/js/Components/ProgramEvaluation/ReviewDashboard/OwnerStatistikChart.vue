<template>
    <div class="animate-fade-in-up delay-150">
        <!-- ═══ CHART PANEL (Adjusted Proportions) ═══ -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
            <!-- LEFT: Chart.js Stacked Bar Chart (Owner Breakdown) - 6/12 Width -->
            <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717] lg:col-span-6">
                <h3 class="mb-4 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400 text-center">
                    Jumlah Inisiatif per Project Owner
                </h3>
                <div class="relative w-full h-[290px]">
                    <canvas ref="leftChartCanvas" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- RIGHT: Chart.js Grouped Bar / Line Chart (Period Breakdown) - 6/12 Width -->
            <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717] lg:col-span-6">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">
                        Peforma Status Implementasi per Periode
                    </h3>
                    <!-- Chart Type Segmented Toggle -->
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-white/5 p-0.5 rounded-lg border border-slate-200 dark:border-white/10 select-none">
                        <button
                            @click="rightChartViewMode = 'bar'"
                            type="button"
                            class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider transition-all"
                            :class="rightChartViewMode === 'bar' 
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#1a1a1a] dark:text-white' 
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                        >
                            Bar
                        </button>
                        <button
                            @click="rightChartViewMode = 'line'"
                            type="button"
                            class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider transition-all"
                            :class="rightChartViewMode === 'line' 
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#1a1a1a] dark:text-white' 
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                        >
                            Line
                        </button>
                    </div>
                </div>
                <div class="relative w-full h-[400px]">
                    <canvas ref="rightChartCanvas" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    matrixData: {
        type: Array,
        required: true
    },
    periodChartData: {
        type: Array,
        required: true
    }
});

const leftChartCanvas = ref(null);
const rightChartCanvas = ref(null);

let leftChartInstance = null;
let rightChartInstance = null;

const rightChartViewMode = ref('bar'); // 'bar' or 'line'

const statuses = [
    'On Track',
    'At Risk',
    'Not Signed',
    'Not Started',
    'Done'
];

const statusColors = {
    'On Track': '#0fa573',
    'At Risk': '#d97706',
    'Not Signed': '#d32f2f',
    'Not Started': '#2563eb',
    'Done': '#64748b'
};

// Konfigurasi untuk Area Kiri (Project Owner)
const OWNER_CHART_CONFIG = {
    barPercentage: 1.0,
    categoryPercentage: 0.7
};

const PERIOD_CHART_CONFIG = {
    barPercentage: 1.0,
    categoryPercentage: 0.75,
    barThickness: 16
};

// ─── Format VP Labels (X-Axis) ───
const formatLabel = (name) => {
    const str = String(name ?? '').trim();
    if (str.includes(' & ')) {
        const parts = str.split(' & ');
        return [parts[0], `& ${parts[1]}`];
    }
    const words = str.split(' ');
    if (words.length > 3) {
        const mid = Math.ceil(words.length / 2);
        return [words.slice(0, mid).join(' '), words.slice(mid).join(' ')];
    }
    if (words.length > 2) {
        return [words.slice(0, 2).join(' '), words.slice(2).join(' ')];
    }
    return [str];
};

// ─── Format Period Labels (X-Axis) ───
const formatPeriodLabel = (periodStr) => {
    const str = String(periodStr ?? '').trim();
    if (!str) return [''];
    
    const parts = str.split(/\s+/);
    if (parts.length >= 2) {
        const year = parts.pop();
        const month = parts.join(' ');
        return [month, year];
    }
    
    return [str];
};

// ─── Left Chart Plugin: Draw numbers inside stacked blocks (Horizontal) ───
const stackedDataLabelsPlugin = {
    id: 'stackedDataLabels',
    afterDatasetsDraw(chart) {
        if (chart.options.indexAxis !== 'y') {
            // Original vertical logic
            const { ctx } = chart;
            ctx.save();
            ctx.font = 'bold 10px Inter, sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            chart.data.datasets.forEach((dataset, datasetIndex) => {
                const meta = chart.getDatasetMeta(datasetIndex);
                if (meta.hidden) return;

                meta.data.forEach((bar, index) => {
                    const val = dataset.data[index];
                    if (val > 0) {
                        const { x, y, base } = bar;
                        const centerY = y + (base - y) / 2;

                        if (Math.abs(base - y) > 14) {
                            ctx.fillText(String(val), x, centerY);
                        }
                    }
                });
            });
            ctx.restore();
            return;
        }

        // Horizontal logic
        const { ctx } = chart;
        ctx.save();
        ctx.font = 'bold 10px Inter, sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (meta.hidden) return;

            meta.data.forEach((bar, index) => {
                const val = dataset.data[index];
                if (val > 0) {
                    const { x, y, base } = bar;
                    const centerX = x - (x - base) / 2;

                    if (Math.abs(x - base) > 16) {
                        ctx.fillText(String(val), centerX, y);
                    }
                }
            });
        });
        ctx.restore();
    }
};

// ─── Right Chart Plugin (Bar): Draw numbers on top of grouped bars ───
const groupedDataLabelsPlugin = {
    id: 'groupedDataLabels',
    afterDatasetsDraw(chart) {
        if (chart.config.type !== 'bar') return;
        const { ctx } = chart;
        ctx.save();
        ctx.font = 'bold 9px Inter, sans-serif';
        
        // Detect dark mode dynamically to match grid text color
        const isDark = document.documentElement.classList.contains('dark') || chart.canvas.closest('.dark') !== null;
        ctx.fillStyle = isDark ? '#94a3b8' : '#475569';
        
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (meta.hidden) return;

            meta.data.forEach((bar, index) => {
                const val = dataset.data[index];
                if (val !== null && val > 0) {
                    const { x, y } = bar;
                    ctx.fillText(String(val), x, y - 4);
                }
            });
        });
        ctx.restore();
    }
};

// ─── Right Chart Plugin (Line): Draw numbers on top of line points ───
const lineDataLabelsPlugin = {
    id: 'lineDataLabels',
    afterDatasetsDraw(chart) {
        if (chart.config.type !== 'line') return;
        const { ctx } = chart;
        ctx.save();
        ctx.font = 'bold 9px Inter, sans-serif';
        
        const isDark = document.documentElement.classList.contains('dark') || chart.canvas.closest('.dark') !== null;
        ctx.fillStyle = isDark ? '#94a3b8' : '#475569';
        
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (meta.hidden) return;

            meta.data.forEach((point, index) => {
                const val = dataset.data[index];
                if (val !== null && val > 0) {
                    const { x, y } = point;
                    ctx.fillText(String(val), x, y - 6);
                }
            });
        });
        ctx.restore();
    }
};

// ─── Build / Update Left Stacked Chart ───
const updateLeftChart = () => {
    if (!leftChartCanvas.value) return;

    const labels = props.matrixData.map(r => formatLabel(r.name));
    
    // Calculate max stacked value for dynamic X-axis
    const maxStackedValue = Math.max(...props.matrixData.map(r => 
        statuses.reduce((sum, status) => sum + (r.statusGroups[status]?.length || 0), 0)
    ), 1);

    const datasets = statuses.map(status => ({
        label: status,
        data: props.matrixData.map(r => r.statusGroups[status]?.length || 0),
        backgroundColor: statusColors[status],
        borderColor: 'transparent',
        borderWidth: 0,
        ...OWNER_CHART_CONFIG
    }));

    if (leftChartInstance) {
        leftChartInstance.data.labels = labels;
        leftChartInstance.data.datasets = datasets;
        leftChartInstance.options.scales.x.suggestedMax = maxStackedValue;
        leftChartInstance.update();
    } else {
        const ctx = leftChartCanvas.value.getContext('2d');
        leftChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets },
            plugins: [stackedDataLabelsPlugin],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 15,
                            font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 8,
                        titleFont: { family: 'Inter, sans-serif', size: 10, weight: 'bold' },
                        bodyFont: { family: 'Inter, sans-serif', size: 9 }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        suggestedMax: maxStackedValue,
                        ticks: {
                            stepSize: 2,
                            font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                            color: '#64748b'
                        },
                        grid: { color: 'rgba(100, 116, 139, 0.08)' }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    }
};

// ─── Build / Update Right Grouped / Line Chart ───
const updateRightChart = () => {
    if (!rightChartCanvas.value || !props.periodChartData) return;

    // Destroy existing instance to switch smoothly between 'bar' and 'line' types
    if (rightChartInstance) {
        rightChartInstance.destroy();
        rightChartInstance = null;
    }

    const labels = props.periodChartData.map(d => formatPeriodLabel(d.period));
    const isLine = rightChartViewMode.value === 'line';

    // Calculate maximum value to determine safe suggestedMax headroom dynamically
    const maxVal = Math.max(...props.periodChartData.flatMap(d => statuses.map(s => d[s] || 0)), 1);
    const dynamicSuggestedMax = maxVal;

    let datasets;

    if (isLine) {
        datasets = statuses.map(status => ({
            label: status,
            type: 'line',
            data: props.periodChartData.map(d => d[status] || 0),
            borderColor: statusColors[status],
            borderWidth: 2.5,
            backgroundColor: 'transparent',
            pointBackgroundColor: statusColors[status],
            pointBorderColor: '#ffffff',
            pointBorderWidth: 1.5,
            pointRadius: 4.5,
            pointHoverRadius: 6,
            tension: 0.35,
            fill: false
        }));
    } else {
        // ── Slot-based dataset approach for truly gapless bars ──
        // For each period, collect only the statuses that have count > 0.
        // Assign them to consecutive "slots" (slot0, slot1, slot2, ...).
        // This ensures bars always touch each other with no empty gaps.

        const periodActiveStatuses = props.periodChartData.map(d => {
            return statuses.filter(s => (d[s] || 0) > 0);
        });

        const maxSlots = Math.max(...periodActiveStatuses.map(a => a.length), 0);

        datasets = [];
        for (let slotIdx = 0; slotIdx < maxSlots; slotIdx++) {
            const data = [];
            const bgColors = [];
            const borderColors = [];
            const statusLabels = []; // for tooltip

            props.periodChartData.forEach((d, periodIdx) => {
                const activeList = periodActiveStatuses[periodIdx];
                if (slotIdx < activeList.length) {
                    const status = activeList[slotIdx];
                    data.push(d[status]);
                    bgColors.push(statusColors[status]);
                    borderColors.push('transparent');
                    statusLabels.push(status);
                } else {
                    data.push(null);
                    bgColors.push('transparent');
                    borderColors.push('transparent');
                    statusLabels.push('');
                }
            });

            datasets.push({
                label: `Slot ${slotIdx}`,
                data,
                backgroundColor: bgColors,
                borderColor: borderColors,
                borderWidth: 0,
                ...PERIOD_CHART_CONFIG,
                skipNull: true,
                // Store status labels for tooltip resolution
                _statusLabels: statusLabels
            });
        }
    }

    const ctx = rightChartCanvas.value.getContext('2d');
    rightChartInstance = new Chart(ctx, {
        type: isLine ? 'line' : 'bar',
        data: { labels, datasets },
        plugins: [isLine ? lineDataLabelsPlugin : groupedDataLabelsPlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: isLine ? {
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 15,
                        font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                        color: '#64748b'
                    } : {
                        // Custom legend: show status colors instead of slot labels
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 15,
                        font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                        color: '#64748b',
                        generateLabels: () => {
                            return statuses.map(status => ({
                                text: status,
                                fillStyle: statusColors[status],
                                strokeStyle: 'transparent',
                                lineWidth: 0,
                                hidden: false
                            }));
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 8,
                    titleFont: { family: 'Inter, sans-serif', size: 10, weight: 'bold' },
                    bodyFont: { family: 'Inter, sans-serif', size: 9 },
                    ...(isLine ? {} : {
                        callbacks: {
                            label: (ctx) => {
                                const ds = ctx.dataset;
                                const statusName = ds._statusLabels?.[ctx.dataIndex] || '';
                                const val = ctx.parsed.y;
                                if (!statusName || val === null || val === 0) return null;
                                return `${statusName}: ${val}`;
                            }
                        },
                        filter: (item) => {
                            return item.parsed.y !== null && item.parsed.y > 0;
                        }
                    })
                }
            },
            scales: {
                x: {
                    stacked: false,
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                        color: '#64748b'
                    }
                },
                y: {
                    stacked: false,
                    suggestedMax: dynamicSuggestedMax,
                    ticks: {
                        stepSize: 2,
                        font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' },
                        color: '#64748b'
                    },
                    grid: { color: 'rgba(100, 116, 139, 0.08)' }
                }
            }
        }
    });
};

const updateAllCharts = () => {
    updateLeftChart();
    updateRightChart();
};

watch(() => props.matrixData, () => {
    updateLeftChart();
}, { deep: true });

watch(() => props.periodChartData, () => {
    updateRightChart();
}, { deep: true });

watch(rightChartViewMode, () => {
    updateRightChart();
});

onMounted(() => {
    updateAllCharts();
});

onBeforeUnmount(() => {
    if (leftChartInstance) {
        leftChartInstance.destroy();
        leftChartInstance = null;
    }
    if (rightChartInstance) {
        rightChartInstance.destroy();
        rightChartInstance = null;
    }
});
</script>
