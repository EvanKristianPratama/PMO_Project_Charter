<template>
    <div class="space-y-6">
        <!-- Statistik & Chart Grid -->

        <ITDocumentStatistik :projects="projects" :calculate-score="calculateCompletenessScore"
            v-model:selected-completeness="completenessFilter" />
            
        <ITCompletenessChart :projects="filteredProjects" />

        <div class="rounded-2xl border border-slate-900 bg-white shadow-sm dark:border-white/20 dark:bg-[#171717]">
            <div class="border-b border-slate-900 px-3 py-2 dark:border-white/20">
                <div class="flex flex-wrap items-center justify-start gap-4">
                    <div class="flex items-center gap-2">
                        <label
                            class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                            Status
                        </label>
                        <select v-model="statusFilter"
                            class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                            <option value="">All Status</option>
                            <option v-for="status in availableStatuses" :key="status" :value="status">
                                {{ status }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label
                            class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                            Version
                        </label>
                        <select v-model="versionFilter"
                            class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                            <option value="">All Version</option>
                            <option v-for="version in availableVersions" :key="version" :value="version">
                                {{ version }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label
                            class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                            Completeness
                        </label>
                        <select v-model="completenessSort"
                            class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                            <option value="">Default</option>
                            <option value="desc">Terlengkap</option>
                            <option value="asc">Skor Terendah</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 border-l border-slate-900 dark:border-white/20 pl-4 ml-2">
                        <button
                            type="button"
                            class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                            :class="showITArchitecture
                                ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                            @click="showITArchitecture = !showITArchitecture"
                        >
                            {{ showITArchitecture ? 'Hide IT Architecture' : 'Show IT Architecture' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead
                        class="bg-slate-50 text-[9px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <tr>
                            <th v-if="showITArchitecture" class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">IT Architecture Building Block</th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20 w-10">No</th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Initiatives
                            </th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">
                                Status</th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Document
                                Completeness</th>
                            <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20 w-48">Incomplete</th>
                            <th class="border-b border-slate-900 px-4 py-2 text-right dark:border-white/20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-[#171717] divide-y divide-slate-900 dark:divide-white/20">
                        <template v-for="project in projectsWithRowspan" :key="project.id">
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <!-- IT Architecture Building Block -->
                                <td v-if="showITArchitecture && project.rowspan > 0" :rowspan="project.rowspan"
                                    class="border-r border-slate-900 px-4 py-4 align-top dark:border-white/20">
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                        {{ project.coe_name || 'Unassigned' }}
                                    </span>
                                </td>

                                <td class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20">
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                        {{ project.project_id }}
                                    </span>
                                </td>

                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                            {{ project.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20">
                                    <span
                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-tight text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                        {{ project.status_name }}
                                    </span>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-2 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10 border border-slate-200 dark:border-white/10">
                                            <div class="h-full transition-all duration-500"
                                                :class="getProgressBarColor(calculateCompletenessScore(project))"
                                                :style="{ width: `${calculateCompletenessScore(project)}%` }"></div>
                                        </div>
                                        <span class="text-[10px] font-black"
                                            :class="getScoreColor(calculateCompletenessScore(project))">
                                            {{ calculateCompletenessScore(project) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="label in getIncompleteFields(project)" :key="label"
                                            class="inline-flex rounded bg-rose-50 px-1.5 py-0.5 text-[8px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            {{ label }}
                                        </span>
                                        <span v-if="getIncompleteFields(project).length === 0" class="text-[18px] text-center text-emerald-600">
                                            -
                                        </span>
                                    </div>
                                </td>
                                <td class="border-slate-900 px-4 py-4 text-right dark:border-white/20">
                                    <button @click="toggleRow(project.id)"
                                        class="rounded border border-slate-300 px-2 py-1 text-[8px] font-bold uppercase tracking-wider text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                                        {{ expandedRows.has(project.id) ? 'Tutup' : 'Detail' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="expandedRows.has(project.id)"
                                class="bg-slate-50/30 dark:bg-white/5 border-b border-slate-900 dark:border-white/20">
                                <td :colspan="showITArchitecture ? 7 : 6" class="px-6 py-6 border-r border-slate-900 dark:border-white/20">
                                    <!-- Categorized Detail Tables -->
                                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                        <div v-for="cat in getFieldCategories(project)" :key="'table-' + cat.name"
                                            class="overflow-hidden rounded-xl border border-slate-900 shadow-sm dark:border-white/20">
                                            <table class="w-full border-collapse text-left text-[11px]">
                                                <thead
                                                    class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400">
                                                    <tr>
                                                        <th
                                                            class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">
                                                            {{ cat.name }}</th>
                                                        <th
                                                            class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20 w-32">
                                                            Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-[#1c1c1c]">
                                                    <tr v-for="field in cat.fields" :key="field"
                                                        class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                                        <td
                                                            class="border-b border-r border-slate-900 px-4 py-2 font-bold text-slate-700 dark:border-white/20 dark:text-slate-300">
                                                            {{ getFieldLabel(field, project) }}
                                                        </td>
                                                        <td
                                                            class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <CheckCircleIcon v-if="project.details[field]"
                                                                    class="h-4 w-4 text-emerald-500" />
                                                                <XCircleIcon v-else class="h-4 w-4 text-rose-500" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="projectsWithRowspan.length === 0">
                            <td :colspan="showITArchitecture ? 7 : 6"
                                class="border-slate-900 px-6 py-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:border-white/20">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
    CheckCircleIcon,
    XCircleIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    InformationCircleIcon
} from '@heroicons/vue/24/solid';
import ITDocumentStatistik from './ITDocumentStatistik.vue';
import ITCompletenessChart from './ITCompletenessChart.vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const expandedRows = ref(new Set());
const completenessFilter = ref(''); // '', 'lengkap', 'tidak-lengkap'
const showITArchitecture = ref(false);

const toggleRow = (id) => {
    if (expandedRows.value.has(id)) {
        expandedRows.value.delete(id);
    } else {
        expandedRows.value.add(id);
    }
};
// ... rest of script setup


const fieldConfigs = {
    baseline: {
        labels: {
            category: 'Kategori',
            duration: 'Durasi',
            tgl_dokumen: 'Tanggal Dokumen',
            owner: 'Project Owner',
            background: 'Latar belakang - Gap/peluang saat ini',
            objectives: 'Tujuan',
            has_roadmap: 'Roadmap (Milestone)',
            impact_value: 'Dampak dan nilai bagi Pertamina',
            key_personnel: 'Personel utama',
            key_items: 'Item utama',
            budget: 'Indikatif kebutuhan budget',
            risks_identified: 'Resiko teridentifikasi',
            risk_mitigation: 'Mitigasi risiko'
        },
        categories: [
            {
                name: 'Informasi Umum',
                fields: ['category', 'duration', 'tgl_dokumen', 'owner']
            },
            {
                name: 'Informasi Proyek',
                fields: ['background', 'objectives', 'has_roadmap']
            },
            {
                name: 'Dampak & Sumber Daya',
                fields: ['impact_value', 'key_personnel', 'key_items', 'budget']
            },
            {
                name: 'Potensi Risiko',
                fields: ['risks_identified', 'risk_mitigation']
            }
        ]
    },
    approved: {
        labels: {
            duration: 'Project Duration',
            tgl_dokumen: 'Document Date',
            sponsor: 'Project Sponsor',
            owner: 'Project Owner',
            leader: 'Project Leader',
            background: 'Project Background',
            objectives: 'Business Objectives',
            has_roadmap: 'Roadmap (Milestone)',
            key_milestone: 'Key Milestone & Due Date',
            target_kpi: 'Target KPI',
            impact_value: 'Impact and Value for Pertamina',
            key_personnel: 'Cross Function Involvement',
            key_items: 'Required Resources',
            budget: 'Cost',
            risks_identified: 'Risk',
            risk_mitigation: 'Mitigation',
            notes: 'Notes'
        },
        categories: [
            {
                name: 'General Information',
                fields: ['duration', 'tgl_dokumen', 'sponsor', 'owner', 'leader']
            },
            {
                name: 'Project Context',
                fields: ['background', 'objectives', 'has_roadmap', 'key_milestone']
            },
            {
                name: 'Resources & Impact',
                fields: ['target_kpi', 'impact_value', 'key_personnel', 'key_items', 'budget']
            },
            {
                name: 'Risk & Notes',
                fields: ['risks_identified', 'risk_mitigation', 'notes']
            }
        ]
    }
};

const getVersionKey = (project) => {
    // Prioritize status_name to determine the structure
    const status = String(project.status_name || '').toLowerCase();
    if (status.includes('approved') || status.includes('review') || status.includes('propose')) {
        return 'approved';
    }

    // Fallback to charter_version if status is not decisive
    const version = String(project.charter_version || '').toLowerCase();
    if (version.includes('approved')) {
        return 'approved';
    }

    return 'baseline';
};

const getFieldCategories = (project) => {
    return fieldConfigs[getVersionKey(project)].categories;
};

const getFieldLabel = (field, project) => {
    return fieldConfigs[getVersionKey(project)].labels[field] || field;
};

const calculateCompletenessScore = (project) => {
    const categories = getFieldCategories(project);
    let totalFields = 0;
    let filledFields = 0;

    categories.forEach(cat => {
        totalFields += cat.fields.length;
        filledFields += cat.fields.filter(f => project.details[f]).length;
    });

    return totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;
};

const getIncompleteFields = (project) => {
    const categories = getFieldCategories(project);
    const incomplete = [];
    categories.forEach(cat => {
        cat.fields.forEach(field => {
            if (!project.details[field]) {
                incomplete.push(getFieldLabel(field, project));
            }
        });
    });
    return incomplete;
};

const getCategoryStats = (project, category) => {
    const total = category.fields.length;
    const filled = category.fields.filter(f => project.details[f]).length;
    return { total, filled, percent: Math.round((filled / total) * 100) };
};

const getVerdict = (score) => {
    if (score === 100) return { label: 'Sangat Lengkap', cls: 'bg-emerald-500 text-white' };
    if (score >= 70) return { label: 'Cukup Lengkap', cls: 'bg-amber-500 text-white' };
    return { label: 'Butuh Perbaikan', cls: 'bg-rose-500 text-white' };
};

const getScoreColor = (score) => {
    if (score === 100) return 'text-emerald-600 dark:text-emerald-400';
    if (score >= 90) return 'text-lime-600 dark:text-lime-400';
    if (score >= 70) return 'text-amber-600 dark:text-amber-400';
    return 'text-rose-600 dark:text-rose-400';
};

const getProgressBarColor = (score) => {
    if (score === 100) return 'bg-emerald-500';
    if (score >= 90) return 'bg-lime-500';
    if (score >= 70) return 'bg-amber-500';
    return 'bg-rose-500';
};

const statusFilter = ref('');
const versionFilter = ref('');
const completenessSort = ref('');

const availableStatuses = computed(() => {
    const statuses = props.projects.map(p => p.status_name);
    return [...new Set(statuses)].filter(Boolean).sort();
});

const availableVersions = computed(() => {
    const versions = props.projects.map(p => p.charter_version);
    return [...new Set(versions)].filter(Boolean).sort();
});

const coeOrder = [
    "User Interface and Experience",
    "Integration and Automation",
    "Business Application System",
    "Infrastructure",
    "Data and Analytics",
    "Cybersecurity",
    "People, Process and Technology",
    "Overall Architecture",
];

const filteredProjects = computed(() => {
    let list = props.projects.filter(p => {
        const matchStatus = !statusFilter.value || p.status_name === statusFilter.value;
        const matchVersion = !versionFilter.value || p.charter_version === versionFilter.value;

        let matchCompleteness = true;
        const score = calculateCompletenessScore(p);
        if (completenessFilter.value === 'lengkap') {
            matchCompleteness = score >= 100;
        } else if (completenessFilter.value === 'tidak-lengkap') {
            matchCompleteness = score < 100;
        }

        return matchStatus && matchVersion && matchCompleteness;
    });

    return list;
});

const projectsWithRowspan = computed(() => {
    const initiatives = filteredProjects.value;
    const result = [];

    // Sort logic
    const sorted = [...initiatives].sort((a, b) => {
        if (showITArchitecture.value) {
            const coeA = String(a.coe_name || "Unassigned");
            const coeB = String(b.coe_name || "Unassigned");

            if (coeA !== coeB) {
                const indexA = coeOrder.indexOf(coeA);
                const indexB = coeOrder.indexOf(coeB);

                if (indexA !== -1 && indexB !== -1) return indexA - indexB;
                if (indexA !== -1) return -1;
                if (indexB !== -1) return 1;
                return coeA.localeCompare(coeB);
            }
        }

        // Secondary sort (or primary if coe hidden): completeness if requested, otherwise by code
        if (completenessSort.value === 'desc') {
            const scoreDiff = calculateCompletenessScore(b) - calculateCompletenessScore(a);
            if (scoreDiff !== 0) return scoreDiff;
        } else if (completenessSort.value === 'asc') {
            const scoreDiff = calculateCompletenessScore(a) - calculateCompletenessScore(b);
            if (scoreDiff !== 0) return scoreDiff;
        }

        return String(a.code || "").localeCompare(String(b.code || ""));
    });

    if (!showITArchitecture.value) {
        return sorted.map(item => ({ ...item, rowspan: 1 }));
    }

    for (let i = 0; i < sorted.length; i++) {
        const currentIni = sorted[i];
        const currentCoe = String(currentIni.coe_name || "Unassigned");

        if (i === 0 || String(sorted[i - 1].coe_name || "Unassigned") !== currentCoe) {
            let rowspan = 1;
            for (let j = i + 1; j < sorted.length; j++) {
                if (String(sorted[j].coe_name || "Unassigned") === currentCoe) {
                    rowspan++;
                } else {
                    break;
                }
            }
            result.push({ ...currentIni, rowspan });
        } else {
            result.push({ ...currentIni, rowspan: 0 });
        }
    }
    return result;
});
</script>