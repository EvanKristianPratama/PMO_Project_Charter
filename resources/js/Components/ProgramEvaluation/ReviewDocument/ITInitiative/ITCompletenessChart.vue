<template>
    <div class="rounded-2xl border border-slate-900 bg-white p-5 dark:border-white/20 dark:bg-[#171717] h-full flex flex-col justify-between">
        <div>
            <div class="border-b border-slate-100 pb-3 mb-4 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                    Penyebab Ketidaklengkapan Dokumen
                </h3>
                <span class="inline-flex items-center rounded bg-rose-50 px-1.5 py-0.5 text-[9px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    {{ chartData.total }} Isian Kosong
                </span>
            </div>

            <!-- Empty/Success State -->
            <div v-if="chartData.total === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-emerald-50 p-3 dark:bg-emerald-500/10 mb-3">
                    <CheckCircleIcon class="h-8 w-8 text-emerald-500" />
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Semua Dokumen Lengkap
                </h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 max-w-[200px]">
                    Tidak ditemukan bagian/isian dokumen yang kosong pada proyek yang dipilih.
                </p>
            </div>

            <!-- Chart & Legend Content -->
            <div v-else class="space-y-4">
                <!-- Doughnut Canvas -->
                <div class="relative flex justify-center h-[180px] w-full">
                    <canvas ref="chartCanvas"></canvas>
                    <!-- Center Summary Metric -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-black text-slate-900 dark:text-white leading-none">
                            {{ chartData.items.length }}
                        </span>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            Bagian Bermasalah
                        </span>
                    </div>
                </div>

                <!-- Custom Legend with scrollbar if items are many -->
                <div class="max-h-[160px] overflow-y-auto pr-1 space-y-1.5 custom-scrollbar">
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
let chartInstance = null;

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

const fieldLabels = {
    category: 'Kategori',
    duration: 'Durasi Proyek',
    tgl_dokumen: 'Tanggal Dokumen',
    owner: 'Project Owner',
    background: 'Latar Belakang Proyek',
    objectives: 'Tujuan Bisnis / Sasaran',
    impact_value: 'Dampak & Nilai bagi Pertamina',
    key_personnel: 'Keterlibatan Lintas Fungsi',
    key_items: 'Sumber Daya yang Dibutuhkan',
    budget: 'Estimasi Biaya / Budget',
    risks_identified: 'Risiko Teridentifikasi',
    risk_mitigation: 'Mitigasi Risiko',
    sponsor: 'Project Sponsor',
    leader: 'Project Leader',
    key_milestone: 'Milestone Utama & Due Date',
    target_kpi: 'Target KPI',
    notes: 'Notes'
};

const fieldConfigs = {
    baseline: {
        categories: [
            { fields: ['category', 'duration', 'tgl_dokumen', 'owner'] },
            { fields: ['background', 'objectives'] },
            { fields: ['impact_value', 'key_personnel', 'key_items', 'budget'] },
            { fields: ['risks_identified', 'risk_mitigation'] }
        ]
    },
    approved: {
        categories: [
            { fields: ['duration', 'tgl_dokumen', 'sponsor', 'owner', 'leader'] },
            { fields: ['background', 'objectives', 'key_milestone'] },
            { fields: ['target_kpi', 'impact_value', 'key_personnel', 'key_items', 'budget'] },
            { fields: ['risks_identified', 'risk_mitigation', 'notes'] }
        ]
    }
};

const getVersionKey = (project) => {
    const status = String(project.status_name || '').toLowerCase();
    if (status.includes('approved') || status.includes('review') || status.includes('propose')) {
        return 'approved';
    }
    const version = String(project.charter_version || '').toLowerCase();
    if (version.includes('approved')) {
        return 'approved';
    }
    return 'baseline';
};

const chartData = computed(() => {
    const counts = {};
    Object.keys(fieldLabels).forEach(key => {
        counts[key] = 0;
    });

    let totalIncompleteFields = 0;

    props.projects.forEach(project => {
        if (!project.details) return;
        const version = getVersionKey(project);
        const categories = fieldConfigs[version].categories;
        
        categories.forEach(cat => {
            cat.fields.forEach(field => {
                // Not filled means details[field] is falsy
                if (!project.details[field]) {
                    counts[field] = (counts[field] || 0) + 1;
                    totalIncompleteFields++;
                }
            });
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

    if (chartInstance) {
        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = dataValues;
        chartInstance.data.datasets[0].backgroundColor = backgroundColors;
        chartInstance.update();
    } else {
        const ctx = chartCanvas.value.getContext('2d');
        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
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
                },
                cutout: '72%',
                spacing: 2
            }
        });
    }
};

watch(() => chartData.value.items, () => {
    // We need to allow DOM to render canvas if we transitioned from total === 0
    setTimeout(() => {
        updateChart();
    }, 0);
}, { deep: true });

onMounted(() => {
    updateChart();
});

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
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
