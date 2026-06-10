<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Card 1: Sebaran Skor Kelengkapan Dokumen (Pie Chart) -->
        <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717]">
            <div class="border-b border-slate-100 pb-3 mb-4 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                    Skor Kelengkapan Dokumen Appendix Digital Initiative
                </h3>
                <span class="inline-flex items-center rounded bg-blue-50 px-1.5 py-0.5 text-[9px] font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    {{ scoreDistribution.totalProjects }} Proyek
                </span>
            </div>

            <!-- Empty State -->
            <div v-if="scoreDistribution.totalProjects === 0" class="flex flex-col items-center justify-center py-12 text-center text-slate-400">
                Tidak ada data proyek dengan dokumen Appendix yang ditampilkan.
            </div>

            <!-- Chart & Legend Content -->
            <div v-else class="flex flex-col sm:flex-row items-center gap-6 mt-2">
                <!-- Bar Canvas -->
                <div class="relative flex-shrink-0 h-[170px] w-[160px] sm:w-[200px] flex justify-center items-center">
                    <canvas ref="scoreBarChartCanvas"></canvas>
                </div>

                <!-- Custom Legend with scrollbar if items are many -->
                <div class="flex-grow w-full max-h-[170px] overflow-y-auto pr-1 space-y-1.5 custom-scrollbar">
                    <div 
                        v-for="item in scoreDistribution.items" 
                        :key="item.label"
                        class="flex items-center justify-between text-[10px] p-1.5 rounded hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                    >
                        <div class="flex items-center gap-2 truncate pr-4">
                            <span 
                                class="h-2 w-2 rounded-full flex-shrink-0"
                                :style="{ backgroundColor: getBarColorForScore(item.value) }"
                            ></span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 truncate">
                                Skor {{ item.label }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ item.count }} Proyek
                            </span>
                            <span class="text-[8px] font-bold text-slate-400">
                                ({{ Math.round((item.count / scoreDistribution.totalProjects) * 100) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Penyebab Ketidaklengkapan Dokumen (Pie Chart) -->
        <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717]">
            <div class="border-b border-slate-100 pb-3 mb-4 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                    Penyebab Ketidaklengkapan Dokumen Appendix
                </h3>
                <span class="inline-flex items-center rounded bg-rose-50 px-1.5 py-0.5 text-[9px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    {{ chartData.total }} Isian Kosong
                </span>
            </div>

            <!-- Empty/Success State -->
            <div v-if="scoreDistribution.totalProjects === 0" class="flex flex-col items-center justify-center py-12 text-center text-slate-400">
                Tidak ada data proyek dengan dokumen Appendix yang ditampilkan.
            </div>
            <div v-else-if="chartData.total === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-emerald-50 p-3 dark:bg-emerald-500/10 mb-3">
                    <CheckCircleIcon class="h-8 w-8 text-emerald-500" />
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Semua Dokumen Appendix Lengkap
                </h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 max-w-[200px]">
                    Tidak ditemukan bagian/isian dokumen Appendix yang kosong pada proyek yang dipilih.
                </p>
            </div>

            <!-- Chart & Legend Content -->
            <div v-else class="flex flex-col sm:flex-row items-center gap-6 mt-2">
                <!-- Bar Canvas -->
                <div class="relative flex-shrink-0 h-[170px] w-[160px] sm:w-[200px] flex justify-center items-center">
                    <canvas ref="chartCanvas"></canvas>
                </div>

                <!-- Custom Legend with scrollbar if items are many -->
                <div class="flex-grow w-full max-h-[170px] overflow-y-auto pr-1 space-y-1.5 custom-scrollbar">
                    <div 
                        v-for="(item, idx) in chartData.items" 
                        :key="item.key"
                        class="flex items-center justify-between text-[10px] p-1.5 rounded hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                    >
                        <div class="flex items-center gap-2 truncate pr-4">
                            <span 
                                class="h-2 w-2 rounded-full flex-shrink-0"
                                :style="{ backgroundColor: colors[idx % colors.length] }"
                            ></span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 truncate" :title="item.label">
                                {{ item.label }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ item.count }}
                            </span>
                            <span class="text-[8px] font-bold text-slate-400">
                                ({{ Math.round((item.count / chartData.total) * 100) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
import { CheckCircleIcon } from '@heroicons/vue/24/solid';

Chart.register(...registerables);

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    }
});

const chartCanvas = ref(null);
const scoreBarChartCanvas = ref(null);

let chartInstance = null;
let scoreBarChartInstance = null;

// Premium palette for doughnut slices
const colors = [
    '#ef4444', // Red
    '#3b82f6', // Blue
    '#10b981', // Green
    '#f97316', // Orange
    '#a855f7', // Purple
    '#eab308', // Yellow
    '#ec4899', // Pink
    '#06b6d4', // Cyan
    '#84cc16', // Lime
    '#14b8a6', // Teal
    '#d946ef', // Fuchsia
    '#4f46e5'  // Indigo
];

const fieldLabels = {
    usecase: 'Use Case Title',
    owner: 'Project Owner',
    coe: 'CoE',
    organization: 'PIC',
    update_doc: 'Updated',
    description: 'Use Case Description',
    situation: 'Current Situation',
    key_functionalities: 'Key Functionalities',
    value: 'Value',
    value_rationale: 'Value Rationale',
    value_matrics: 'Value Metrics Impacted',
    urgency: 'Urgency',
    urgency_rationale: 'Urgency Rationale',
    urgency_expected: 'Expected Go-Live',
    ease: 'Ease of Implementation',
    ease_rationale: 'Ease Rationale',
    ease_detail: 'Ease Detail',
    resource: 'Resource Requirement',
    resource_rationale: 'Resource Rationale',
    resource_detail: 'Resource Requirement Detail',
    predecessor: 'Predecessor',
    successor: 'Successor',
    otherBU: 'Other BUs Implement',
    sign_by: 'Sign By',
    rjpp_tagging: 'RJPP Tagging',
};

const getBarColorForScore = (score) => {
    if (score === 100) return '#10b981'; // emerald-500
    if (score >= 90) return '#84cc16'; // lime-500
    if (score >= 70) return '#f59e0b'; // amber-500
    return '#ef4444'; // rose-500
};

const chartData = computed(() => {
    const counts = {};
    Object.keys(fieldLabels).forEach(key => {
        counts[key] = 0;
    });

    let totalIncompleteFields = 0;

    props.projects.forEach(project => {
        if (project.appendix_score === 'X') return;
        if (!project.details || !project.details.appendix) return;
        
        Object.keys(fieldLabels).forEach(field => {
            if (!project.details.appendix[field]) {
                counts[field] = (counts[field] || 0) + 1;
                totalIncompleteFields++;
            }
        });
    });

    const items = Object.keys(counts)
        .map(key => ({
            key,
            label: fieldLabels[key] || key,
            count: counts[key]
        }))
        .filter(item => item.count > 0)
        .sort((a, b) => b.count - a.count);

    return {
        items,
        total: totalIncompleteFields
    };
});

// Group projects by completeness score of Appendix document
const scoreDistribution = computed(() => {
    const counts = {};
    let validProjectsCount = 0;
    props.projects.forEach(project => {
        if (project.appendix_score === 'X') return;
        const score = project.appendix_score || '0%';
        counts[score] = (counts[score] || 0) + 1;
        validProjectsCount++;
    });

    const items = Object.keys(counts)
        .map(key => {
            const scoreVal = parseInt(key, 10);
            return {
                label: key,
                value: scoreVal,
                count: counts[key]
            };
        })
        .sort((a, b) => b.value - a.value); // Sort descending (100% to lowest)

    return {
        items,
        totalProjects: validProjectsCount
    };
});

const barLabelsPlugin = {
    id: 'barLabels',
    afterDatasetsDraw(chart) {
        const { ctx } = chart;
        ctx.save();
        ctx.font = 'bold 9px Inter, sans-serif';
        const isDark = document.documentElement.classList.contains('dark') || chart.canvas.closest('.dark') !== null;
        ctx.fillStyle = isDark ? '#cbd5e1' : '#475569';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            meta.data.forEach((bar, index) => {
                const value = dataset.data[index];
                if (value === null || value === undefined) return;
                const xPos = bar.x + 6;
                const yPos = bar.y;
                ctx.fillText(value, xPos, yPos);
            });
        });
        ctx.restore();
    }
};

const updateChart = () => {
    if (!chartCanvas.value || chartData.value.total === 0) {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
        return;
    }

    const labels = chartData.value.items.map(item => item.label);
    const dataValues = chartData.value.items.map(item => item.count);
    const backgroundColors = chartData.value.items.map((_, idx) => colors[idx % colors.length]);

    const isDark = document.documentElement.classList.contains('dark') || chartCanvas.value.closest('.dark') !== null;
    const borderColor = isDark ? '#171717' : '#ffffff';

    if (chartInstance) {
        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = dataValues;
        chartInstance.data.datasets[0].backgroundColor = backgroundColors;
        chartInstance.data.datasets[0].borderColor = borderColor;
        chartInstance.update();
    } else {
        const ctx = chartCanvas.value.getContext('2d');
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 1,
                    borderColor: borderColor,
                    borderRadius: 4,
                    barThickness: 8
                }]
            },
            plugins: [barLabelsPlugin],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 8,
                        titleFont: { family: 'Inter, sans-serif', size: 10, weight: 'bold' },
                        bodyFont: { family: 'Inter, sans-serif', size: 9 },
                        callbacks: {
                            label: (context) => {
                                const value = context.dataset.data[context.dataIndex];
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return ` ${value} Isian (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grace: '8%',
                        grid: {
                            color: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(15, 23, 42, 0.06)',
                            drawTicks: false
                        },
                        ticks: {
                            font: { family: 'Inter, sans-serif', size: 8 },
                            color: isDark ? '#94a3b8' : '#64748b',
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    }
};

const updateScoreBarChart = () => {
    if (!scoreBarChartCanvas.value || scoreDistribution.value.totalProjects === 0) {
        if (scoreBarChartInstance) {
            scoreBarChartInstance.destroy();
            scoreBarChartInstance = null;
        }
        return;
    }

    const labels = scoreDistribution.value.items.map(item => `Skor ${item.label}`);
    const dataValues = scoreDistribution.value.items.map(item => item.count);
    const backgroundColors = scoreDistribution.value.items.map(item => getBarColorForScore(item.value));

    const isDark = document.documentElement.classList.contains('dark') || scoreBarChartCanvas.value.closest('.dark') !== null;
    const borderColor = isDark ? '#171717' : '#ffffff';

    if (scoreBarChartInstance) {
        scoreBarChartInstance.data.labels = labels;
        scoreBarChartInstance.data.datasets[0].data = dataValues;
        scoreBarChartInstance.data.datasets[0].backgroundColor = backgroundColors;
        scoreBarChartInstance.data.datasets[0].borderColor = borderColor;
        scoreBarChartInstance.update();
    } else {
        const ctx = scoreBarChartCanvas.value.getContext('2d');
        scoreBarChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 1,
                    borderColor: borderColor,
                    borderRadius: 4,
                    barThickness: 12
                }]
            },
            plugins: [barLabelsPlugin],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 8,
                        titleFont: { family: 'Inter, sans-serif', size: 10, weight: 'bold' },
                        bodyFont: { family: 'Inter, sans-serif', size: 9 },
                        callbacks: {
                            label: (context) => {
                                const value = context.dataset.data[context.dataIndex];
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return ` ${value} Proyek (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grace: '8%',
                        grid: {
                            color: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(15, 23, 42, 0.06)',
                            drawTicks: false
                        },
                        ticks: {
                            font: { family: 'Inter, sans-serif', size: 8 },
                            color: isDark ? '#94a3b8' : '#64748b',
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    }
};

watch(() => props.projects, () => {
    // We need to allow DOM to render canvas if we transitioned from total === 0 or filters change
    setTimeout(() => {
        updateChart();
        updateScoreBarChart();
    }, 0);
}, { deep: true });

onMounted(() => {
    updateChart();
    updateScoreBarChart();
});

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
    if (scoreBarChartInstance) {
        scoreBarChartInstance.destroy();
        scoreBarChartInstance = null;
    }
});
</script>

<style scoped>
/* Scrollbar Premium Customization */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.2);
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 116, 139, 0.4);
}
</style>
