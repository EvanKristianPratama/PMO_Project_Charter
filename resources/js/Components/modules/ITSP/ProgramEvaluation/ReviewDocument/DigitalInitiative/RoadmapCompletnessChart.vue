<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Card 1: Ketersediaan Dokumen Roadmap (Pie Chart) -->
        <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717]">
            <div class="border-b border-slate-100 pb-3 mb-4 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                    Skor Ketersediaan Dokumen Roadmap
                </h3>
                <span class="inline-flex items-center rounded bg-blue-50 px-1.5 py-0.5 text-[9px] font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    {{ projects.length }} Proyek
                </span>
            </div>

            <!-- Empty State -->
            <div v-if="projects.length === 0" class="flex flex-col items-center justify-center py-12 text-center text-slate-400">
                Tidak ada data proyek yang ditampilkan.
            </div>

            <!-- Chart & Legend Content -->
            <div v-else class="flex flex-col sm:flex-row items-center gap-6 mt-2">
                <!-- Pie Canvas -->
                <div class="relative flex-shrink-0 h-[170px] w-[170px] flex justify-center items-center">
                    <canvas ref="availabilityPieChartCanvas"></canvas>
                </div>

                <!-- Custom Legend -->
                <div class="flex-grow w-full max-h-[170px] overflow-y-auto pr-1 space-y-1.5 custom-scrollbar">
                    <!-- Available -->
                    <div class="flex items-center justify-between text-[10px] p-1.5 rounded hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-2 truncate pr-4">
                            <span class="h-2 w-2 rounded-full flex-shrink-0 bg-[#10b981]"></span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 truncate">
                                Available (Ada Milestone)
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ scoreDistribution.available }} Proyek
                            </span>
                            <span class="text-[8px] font-bold text-slate-400">
                                ({{ Math.round((scoreDistribution.available / projects.length) * 100) }}%)
                            </span>
                        </div>
                    </div>
                    <!-- Not Available -->
                    <div class="flex items-center justify-between text-[10px] p-1.5 rounded hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-2 truncate pr-4">
                            <span class="h-2 w-2 rounded-full flex-shrink-0 bg-[#ef4444]"></span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 truncate">
                                Not Available
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ scoreDistribution.notAvailable }} Proyek
                            </span>
                            <span class="text-[8px] font-bold text-slate-400">
                                ({{ Math.round((scoreDistribution.notAvailable / projects.length) * 100) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Sebaran Proyek Tanpa Roadmap (Pie Chart) -->
        <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717]">
            <div class="border-b border-slate-100 pb-3 mb-4 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                    Sebaran Proyek Tanpa Roadmap Berdasarkan CoE
                </h3>
                <span class="inline-flex items-center rounded bg-rose-50 px-1.5 py-0.5 text-[9px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    {{ missingRoadmapsByCoe.total }} Proyek
                </span>
            </div>

            <!-- Empty/Success State -->
            <div v-if="missingRoadmapsByCoe.total === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-emerald-50 p-3 dark:bg-emerald-500/10 mb-3">
                    <CheckCircleIcon class="h-8 w-8 text-emerald-500" />
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Semua Roadmap Lengkap
                </h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 max-w-[200px]">
                    Tidak ditemukan proyek yang belum memiliki dokumen Roadmap (milestone).
                </p>
            </div>

            <!-- Chart & Legend Content -->
            <div v-else class="flex flex-col sm:flex-row items-center gap-6 mt-2">
                <!-- Pie Canvas -->
                <div class="relative flex-shrink-0 h-[170px] w-[170px] flex justify-center items-center">
                    <canvas ref="coePieChartCanvas"></canvas>
                </div>

                <!-- Custom Legend with scrollbar if items are many -->
                <div class="flex-grow w-full max-h-[170px] overflow-y-auto pr-1 space-y-1.5 custom-scrollbar">
                    <div 
                        v-for="(item, idx) in missingRoadmapsByCoe.items" 
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
                                {{ item.count }} Proyek
                            </span>
                            <span class="text-[8px] font-bold text-slate-400">
                                ({{ Math.round((item.count / missingRoadmapsByCoe.total) * 100) }}%)
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

const coePieChartCanvas = ref(null);
const availabilityPieChartCanvas = ref(null);

let coePieChartInstance = null;
let availabilityPieChartInstance = null;

// Premium palette for doughnut slices
const colors = [
    '#f43f5e', // rose-500
    '#f97316', // orange-500
    '#f59e0b', // amber-500
    '#a855f7', // purple-500
    '#3b82f6', // blue-500
    '#06b6d4', // cyan-500
    '#ec4899', // pink-500
    '#14b8a6', // teal-500
    '#10b981', // emerald-500
    '#6366f1', // indigo-500
    '#84cc16', // lime-500
    '#64748b'  // slate-500
];

const scoreDistribution = computed(() => {
    let available = 0;
    let notAvailable = 0;
    props.projects.forEach(project => {
        if (project.roadmap_score === '100%') {
            available++;
        } else {
            notAvailable++;
        }
    });
    return {
        available,
        notAvailable
    };
});

const missingRoadmapsByCoe = computed(() => {
    const counts = {};
    let totalMissing = 0;
    props.projects.forEach(project => {
        if (project.roadmap_score !== '100%') {
            const coe = project.coe_name || 'CoE Not Identified';
            counts[coe] = (counts[coe] || 0) + 1;
            totalMissing++;
        }
    });
    
    const items = Object.keys(counts)
        .map(key => ({
            key,
            label: key,
            count: counts[key]
        }))
        .filter(item => item.count > 0)
        .sort((a, b) => b.count - a.count);

    return {
        items,
        total: totalMissing
    };
});

const pieLabelsPlugin = {
    id: 'pieLabels',
    afterDraw(chart) {
        if (chart.config.type !== 'pie') return;
        const { ctx } = chart;
        ctx.save();
        ctx.font = 'bold 11px Inter, sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (meta.hidden) return;

            meta.data.forEach((element, index) => {
                const value = dataset.data[index];
                if (value === 0 || value === null) return;

                const total = dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                
                // Show label only if slice has enough space (>= 4%)
                if (percentage < 4) return;

                const centerAngle = element.startAngle + (element.endAngle - element.startAngle) / 2;
                const avgRadius = element.outerRadius * 0.62;
                const x = element.x + Math.cos(centerAngle) * avgRadius;
                const y = element.y + Math.sin(centerAngle) * avgRadius;

                // Subtle text outline for absolute legibility on bright colors
                ctx.strokeStyle = 'rgba(0, 0, 0, 0.4)';
                ctx.lineWidth = 2.5;
                ctx.strokeText(`${percentage}%`, x, y);
                ctx.fillText(`${percentage}%`, x, y);
            });
        });
        ctx.restore();
    }
};

const updateCoeChart = () => {
    if (!coePieChartCanvas.value || missingRoadmapsByCoe.value.total === 0) {
        if (coePieChartInstance) {
            coePieChartInstance.destroy();
            coePieChartInstance = null;
        }
        return;
    }

    const labels = missingRoadmapsByCoe.value.items.map(item => item.label);
    const dataValues = missingRoadmapsByCoe.value.items.map(item => item.count);
    const backgroundColors = missingRoadmapsByCoe.value.items.map((_, idx) => colors[idx % colors.length]);

    const isDark = document.documentElement.classList.contains('dark') || coePieChartCanvas.value.closest('.dark') !== null;
    const borderColor = isDark ? '#171717' : '#ffffff';

    if (coePieChartInstance) {
        coePieChartInstance.data.labels = labels;
        coePieChartInstance.data.datasets[0].data = dataValues;
        coePieChartInstance.data.datasets[0].backgroundColor = backgroundColors;
        coePieChartInstance.data.datasets[0].borderColor = borderColor;
        coePieChartInstance.update();
    } else {
        const ctx = coePieChartCanvas.value.getContext('2d');
        coePieChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 1.5,
                    borderColor: borderColor,
                    hoverOffset: 6
                }]
            },
            plugins: [pieLabelsPlugin],
            options: {
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
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return ` ${value} Proyek (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
};

const updateAvailabilityChart = () => {
    if (!availabilityPieChartCanvas.value || props.projects.length === 0) {
        if (availabilityPieChartInstance) {
            availabilityPieChartInstance.destroy();
            availabilityPieChartInstance = null;
        }
        return;
    }

    const labels = ['Available', 'Not Available'];
    const dataValues = [scoreDistribution.value.available, scoreDistribution.value.notAvailable];
    const backgroundColors = ['#10b981', '#ef4444'];

    const isDark = document.documentElement.classList.contains('dark') || availabilityPieChartCanvas.value.closest('.dark') !== null;
    const borderColor = isDark ? '#171717' : '#ffffff';

    if (availabilityPieChartInstance) {
        availabilityPieChartInstance.data.labels = labels;
        availabilityPieChartInstance.data.datasets[0].data = dataValues;
        availabilityPieChartInstance.data.datasets[0].backgroundColor = backgroundColors;
        availabilityPieChartInstance.data.datasets[0].borderColor = borderColor;
        availabilityPieChartInstance.update();
    } else {
        const ctx = availabilityPieChartCanvas.value.getContext('2d');
        availabilityPieChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 1.5,
                    borderColor: borderColor,
                    hoverOffset: 6
                }]
            },
            plugins: [pieLabelsPlugin],
            options: {
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
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return ` ${value} Proyek (${percentage}%)`;
                            }
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
        updateCoeChart();
        updateAvailabilityChart();
    }, 0);
}, { deep: true });

onMounted(() => {
    updateCoeChart();
    updateAvailabilityChart();
});

onBeforeUnmount(() => {
    if (coePieChartInstance) {
        coePieChartInstance.destroy();
        coePieChartInstance = null;
    }
    if (availabilityPieChartInstance) {
        availabilityPieChartInstance.destroy();
        availabilityPieChartInstance = null;
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
