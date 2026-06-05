<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    selectedCompleteness: {
        type: String,
        default: '',
    },
    // We pass the calculation function to stay consistent with the table
    calculateScore: {
        type: Function,
        required: true,
    }
});

const emit = defineEmits(['update:selectedCompleteness']);

const showITArchitecture = ref(false);

const stats = computed(() => {
    let lengkap = 0;
    let tidakLengkap = 0;

    props.projects.forEach(project => {
        const score = props.calculateScore(project);
        if (score >= 100) {
            lengkap++;
        } else {
            tidakLengkap++;
        }
    });

    return [
        { 
            id: 'lengkap', 
            label: 'Lengkap', 
            count: lengkap, 
            cls: 'bg-emerald-500 shadow-emerald-500/20',
            textCls: 'text-emerald-600 dark:text-emerald-400' 
        },
        { 
            id: 'tidak-lengkap', 
            label: 'Tidak Lengkap', 
            count: tidakLengkap, 
            cls: 'bg-rose-500 shadow-rose-500/20',
            textCls: 'text-rose-600 dark:text-rose-400' 
        }
    ];
});

const totalCount = computed(() => props.projects.length);

const toggleFilter = (id) => {
    const newValue = props.selectedCompleteness === id ? '' : id;
    emit('update:selectedCompleteness', newValue);
};

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

const groupedData = computed(() => {
    const allCoEs = [...new Set(props.projects.map(p => p.coe_name || 'Unassigned'))].sort((a, b) => {
        const indexA = coeOrder.indexOf(a);
        const indexB = coeOrder.indexOf(b);

        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;

        if (a === 'Unassigned') return 1;
        if (b === 'Unassigned') return -1;
        return a.localeCompare(b);
    });

    return allCoEs.map(coeName => {
        const projectsInCoe = props.projects.filter(p => (p.coe_name || 'Unassigned') === coeName);
        const total = projectsInCoe.length;
        const completeProjects = projectsInCoe.filter(p => props.calculateScore(p) === 100);
        const incompleteProjects = projectsInCoe.filter(p => props.calculateScore(p) < 100);

        return {
            category: coeName,
            total,
            completeProjects,
            incompleteProjects
        };
    });
});

const approvedProjects = computed(() => {
    return props.projects.filter(p => Number(p.status) === 4);
});

const notApprovedProjects = computed(() => {
    return props.projects.filter(p => Number(p.status) === 5);
});

const approvedCompleteProjects = computed(() => {
    return approvedProjects.value.filter(p => props.calculateScore(p) >= 100);
});

const approvedIncompleteProjects = computed(() => {
    return approvedProjects.value.filter(p => props.calculateScore(p) < 100);
});

const grandTotals = computed(() => {
    if (showITArchitecture.value) {
        let total = 0;
        let completeCount = 0;
        let incompleteCount = 0;
        const allCompleteProjects = [];
        const allIncompleteProjects = [];

        groupedData.value.forEach(coe => {
            total += coe.total;
            completeCount += coe.completeProjects.length;
            incompleteCount += coe.incompleteProjects.length;
            allCompleteProjects.push(...coe.completeProjects);
            allIncompleteProjects.push(...coe.incompleteProjects);
        });

        return {
            total,
            completeCount,
            incompleteCount,
            completeProjects: allCompleteProjects,
            incompleteProjects: allIncompleteProjects
        };
    } else {
        const approvedList = approvedProjects.value;
        const approvedComplete = approvedCompleteProjects.value;
        const approvedIncomplete = approvedIncompleteProjects.value;

        return {
            total: approvedList.length + notApprovedProjects.value.length,
            completeCount: approvedComplete.length,
            incompleteCount: approvedIncomplete.length,
            completeProjects: approvedComplete,
            incompleteProjects: approvedIncomplete
        };
    }
});

const getProjectNumber = (project) => {
    if (!project.code) return '?';
    const parts = project.code.split('-');
    const lastPart = parts[parts.length - 1];
    const num = parseInt(lastPart, 10);
    return isNaN(num) ? lastPart : num;
};

const getCircleColor = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase();
    if (normalized === 'on track' || normalized === 'on-track') return 'bg-emerald-500';
    if (normalized === 'at risk' || normalized === 'at-risk') return 'bg-amber-500';
    if (normalized === 'delayed') return 'bg-orange-500';
    if (normalized === 'done' || normalized === 'completed') return 'bg-slate-500';
    if (normalized === 'on progress' || normalized === 'on-progress' || normalized === 'in progress' || normalized === 'in-progress') return 'bg-blue-500';
    if (normalized === 'on review' || normalized === 'on-review') return 'bg-purple-500';
    if (normalized === 'not started' || normalized === 'not-started' || normalized === 'not start') return 'bg-blue-500';
    if (normalized === 'not signed' || normalized === 'not-signed') return 'bg-rose-500';
    return 'bg-slate-400';
};

const getInitiativeTooltip = (project) => {
    const projectName = String(project.name || '').trim();
    const status = String(project.implementation_status || '').trim();
    const periodLabel = String(project.implementation_period || '').trim();

    const nameParts = [];
    if (projectName !== '') nameParts.push(projectName);
    if (status !== '') nameParts.push(status);
    if (periodLabel !== '') nameParts.push(`(${periodLabel})`);

    return nameParts.join(' - ') || projectName || status || periodLabel;
};
</script>

<template>
    <!-- CoE Document Completeness Table -->
    <div class="rounded-2xl border border-slate-900 bg-white shadow-sm dark:border-white/20 dark:bg-[#171717]">
        <div class="border-b border-slate-900 px-3 py-2 dark:border-white/20">
            <div class="flex flex-wrap items-center justify-start gap-2">
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
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead class="bg-slate-50 text-[9px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    <tr>
                        <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20 align-middle">
                            {{ showITArchitecture ? 'IT Architecture Building Block' : 'Category' }}
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Complete</th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Incomplete</th>
                        <th rowspan="2" class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20 align-middle">Total</th>
                    </tr>
                    <tr>
                        <th class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Initiatives</th>
                        <th class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Initiatives</th>
                    </tr>
                </thead>
                <tbody v-if="showITArchitecture" class="bg-white dark:bg-[#171717] divide-y divide-slate-100 dark:divide-white/5">
                    <tr v-for="coe in groupedData" :key="coe.category" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 px-4 py-3 dark:border-white/20 font-bold text-slate-900 dark:text-white">
                            {{ coe.category }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20 font-bold text-slate-700 dark:text-slate-300">
                            {{ coe.completeProjects.length }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20 font-black text-emerald-600 dark:text-emerald-400">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in coe.completeProjects"
                                    :key="project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!coe.completeProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20 font-bold text-slate-700 dark:text-slate-300">
                            {{ coe.incompleteProjects.length }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20 font-black text-rose-600 dark:text-rose-400">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in coe.incompleteProjects"
                                    :key="project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!coe.incompleteProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ coe.total }}
                        </td>
                    </tr>
                    <tr v-if="groupedData.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                </tbody>
                <tbody v-else class="bg-white dark:bg-[#171717] divide-y divide-slate-100 dark:divide-white/5">
                    <!-- Approved Row -->
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-b border-slate-900 px-4 py-3 dark:border-white/20 font-bold text-slate-900 dark:text-white">
                            Approved
                        </td>
                        <td class="border-r border-b border-slate-900 px-4 py-3 text-center dark:border-white/20 font-bold text-slate-700 dark:text-slate-300">
                            {{ approvedCompleteProjects.length }}
                        </td>
                        <td class="border-r border-b border-slate-900 px-4 py-3 text-center dark:border-white/20 font-black text-emerald-600 dark:text-emerald-400">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in approvedCompleteProjects"
                                    :key="project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!approvedCompleteProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-r border-b border-slate-900 px-4 py-3 text-center dark:border-white/20 font-bold text-slate-700 dark:text-slate-300">
                            {{ approvedIncompleteProjects.length }}
                        </td>
                        <td class="border-r border-b border-slate-900 px-4 py-3 text-center dark:border-white/20 font-black text-rose-600 dark:text-rose-400">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in approvedIncompleteProjects"
                                    :key="project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!approvedIncompleteProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-b border-slate-900 px-4 py-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ approvedProjects.length }}
                        </td>
                    </tr>

                    <!-- Not Approved Row -->
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 px-4 py-3 dark:border-white/20 font-bold text-slate-900 dark:text-white">
                            Not Approved
                        </td>
                        <!-- Merged Complete/Incomplete columns (4 columns total) -->
                        <td colspan="4" class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in notApprovedProjects"
                                    :key="project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!notApprovedProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ notApprovedProjects.length }}
                        </td>
                    </tr>
                    
                    <tr v-if="props.projects.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-if="showITArchitecture" class="bg-slate-50/80 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px] border-t border-slate-900 dark:border-white/40">
                        <td class="border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            Grand Total
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center font-black text-[11px] text-slate-700 dark:text-slate-300">
                            {{ grandTotals.completeCount }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in grandTotals.completeProjects"
                                    :key="'gt-complete-' + project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!grandTotals.completeProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center font-black text-[11px] text-slate-700 dark:text-slate-300">
                            {{ grandTotals.incompleteCount }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in grandTotals.incompleteProjects"
                                    :key="'gt-incomplete-' + project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!grandTotals.incompleteProjects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-black text-[11px]">
                            {{ grandTotals.total }}
                        </td>
                    </tr>
                    <tr v-else class="bg-slate-50/80 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px] border-t border-slate-900 dark:border-white/40">
                        <td class="border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            Grand Total
                        </td>
                        <td colspan="4" class="border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <span
                                    v-for="project in props.projects"
                                    :key="'gt-all-' + project.id"
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[8px] font-bold text-white shadow-sm cursor-pointer hover:scale-110 transition-transform"
                                    :class="getCircleColor(project.implementation_status)"
                                    :title="getInitiativeTooltip(project)"
                                >
                                    {{ getProjectNumber(project) }}
                                </span>
                                <span v-if="!props.projects.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-black text-[11px]">
                            {{ grandTotals.total }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<style scoped>
.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}
</style>