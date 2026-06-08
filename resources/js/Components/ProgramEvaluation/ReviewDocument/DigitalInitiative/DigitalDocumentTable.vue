<template>
    <div class="space-y-6">
        <!-- Filters & Search -->
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
                            CoE
                        </label>
                        <select v-model="coeFilter"
                            class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                            <option value="">All CoE</option>
                            <option v-for="coe in availableCoes" :key="coe" :value="coe">
                                {{ coe }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label
                            class="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-400 dark:text-slate-500">
                            Shorting
                        </label>
                        <select v-model="sortBy"
                            class="cursor-pointer rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] font-bold outline-none transition-all focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                            <option value="">Default</option>
                            <option value="master">Master (Terlengkap -> Terendah)</option>
                            <option value="compendium">Compendium (Terlengkap -> Terendah)</option>
                            <option value="appendix">Appendix (Terlengkap -> Terendah)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 border-l border-slate-900 dark:border-white/20 pl-4 ml-2">
                        <button
                            type="button"
                            class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                            :class="showMaster
                                ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                            @click="showMaster = !showMaster"
                        >
                            {{ showMaster ? 'Hide Master' : 'Show Master' }}
                        </button>
                        <button
                            type="button"
                            class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                            :class="showRoadmap
                                ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                            @click="showRoadmap = !showRoadmap"
                        >
                            {{ showRoadmap ? 'Hide Roadmap' : 'Show Roadmap' }}
                        </button>
                        <button
                            type="button"
                            class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                            :class="showAppendix
                                ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                            @click="showAppendix = !showAppendix"
                        >
                            {{ showAppendix ? 'Hide Appendix' : 'Show Appendix' }}
                        </button>
                        <button
                            type="button"
                            class="rounded border px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-[0.08em] transition-all"
                            :class="showCompendium
                                ? 'border-slate-400 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5'"
                            @click="showCompendium = !showCompendium"
                        >
                            {{ showCompendium ? 'Hide Compendium' : 'Show Compendium' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead
                        class="bg-slate-50 text-[9px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <tr>
                            <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20 w-12">No</th>
                            <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Digital Initiative</th>
                            <th v-if="showMaster" colspan="2" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Master</th>
                            <th v-if="showRoadmap" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Roadmap</th>
                            <th v-if="showAppendix" colspan="2" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Appendix</th>
                            <th v-if="showCompendium" colspan="2" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20">Compendium</th>
                            <th rowspan="2" class="border-b border-slate-900 px-4 py-2 text-right dark:border-white/20">Aksi</th>
                        </tr>
                        <tr>
                            <th v-if="showMaster" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20 w-20">Completeness</th>
                            <th v-if="showMaster" class="border-b border-r border-slate-900 px-4 py-1 dark:border-white/20 min-w-[120px]">Incomplete</th>
                            <th v-if="showRoadmap" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20 w-28">Completeness</th>
                            <th v-if="showAppendix" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20 w-20">Completeness</th>
                            <th v-if="showAppendix" class="border-b border-r border-slate-900 px-4 py-1 dark:border-white/20 min-w-[150px]">Incomplete</th>
                            <th v-if="showCompendium" class="border-b border-r border-slate-900 px-4 py-1 text-center dark:border-white/20 w-20">Completeness</th>
                            <th v-if="showCompendium" class="border-b border-r border-slate-900 px-4 py-1 dark:border-white/20 min-w-[120px]">Incomplete</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-[#171717] divide-y divide-slate-900 dark:divide-white/20">
                        <template v-for="project in filteredProjects" :key="project.id">
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <!-- No -->
                                <td class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20">
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                        {{ project.code }}
                                    </span>
                                </td>

                                <!-- Digital Initiative Name -->
                                <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                            {{ project.name }}
                                        </span>
                                        <span class="text-[8px] text-slate-400 uppercase tracking-wider font-semibold">
                                            {{ project.coe_name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Master Completeness -->
                                <td v-if="showMaster" class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20 font-black"
                                    :class="getScoreColorClass(project.master_score)">
                                    {{ project.master_score }}
                                </td>
                                <!-- Master Incomplete -->
                                <td v-if="showMaster" class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="label in splitIncomplete(project.master_incomplete)" :key="label"
                                            class="inline-flex rounded bg-rose-50 px-1.5 py-0.5 text-[8px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            {{ label }}
                                        </span>
                                        <span v-if="project.master_incomplete === '-'" class="text-[14px] font-bold text-emerald-600">
                                            -
                                        </span>
                                    </div>
                                </td>

                                <!-- Roadmap Completeness -->
                                <td v-if="showRoadmap" class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20 font-bold"
                                    :class="project.roadmap_score === '100%' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-600'">
                                    {{ project.roadmap_score === '100%' ? 'Available' : 'Not Available' }}
                                </td>

                                <!-- Appendix Completeness -->
                                <td v-if="showAppendix" class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20 font-black"
                                    :class="getScoreColorClass(project.appendix_score)">
                                    {{ project.appendix_score }}
                                </td>
                                <!-- Appendix Incomplete -->
                                <td v-if="showAppendix" class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="label in splitIncomplete(project.appendix_incomplete)" :key="label"
                                            class="inline-flex rounded bg-rose-50 px-1.5 py-0.5 text-[8px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            {{ label }}
                                        </span>
                                        <span v-if="project.appendix_score === 'X'"
                                            class="inline-flex rounded bg-slate-100 px-1.5 py-0.5 text-[8px] font-bold text-slate-600 dark:bg-white/10 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                            Not Available
                                        </span>
                                        <span v-else-if="project.appendix_incomplete === '-'" class="text-[14px] font-bold text-emerald-600">
                                            -
                                        </span>
                                    </div>
                                </td>

                                <!-- Compendium Completeness -->
                                <td v-if="showCompendium" class="border-r border-slate-900 px-4 py-4 text-center dark:border-white/20 font-black"
                                    :class="getScoreColorClass(project.compendium_score)">
                                    {{ project.compendium_score }}
                                </td>
                                <!-- Compendium Incomplete -->
                                <td v-if="showCompendium" class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="label in splitIncomplete(project.compendium_incomplete)" :key="label"
                                            class="inline-flex rounded bg-rose-50 px-1.5 py-0.5 text-[8px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            {{ label }}
                                        </span>
                                        <span v-if="project.compendium_score === 'X'"
                                            class="inline-flex rounded bg-slate-100 px-1.5 py-0.5 text-[8px] font-bold text-slate-600 dark:bg-white/10 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                            Not Available
                                        </span>
                                        <span v-else-if="project.compendium_incomplete === '-'" class="text-[14px] font-bold text-emerald-600">
                                            -
                                        </span>
                                    </div>
                                </td>

                                <!-- Action Button -->
                                <td class="border-slate-900 px-4 py-4 text-right dark:border-white/20">
                                    <button @click="toggleRow(project.id)"
                                        class="rounded border border-slate-300 px-2 py-1 text-[8px] font-bold uppercase tracking-wider text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                                        {{ expandedRows.has(project.id) ? 'Tutup' : 'Detail' }}
                                    </button>
                                </td>
                            </tr>

                            <!-- Expanded Detail Row -->
                            <!-- Expanded Detail Row -->
                            <tr v-if="expandedRows.has(project.id)"
                                class="bg-slate-50/30 dark:bg-white/5 border-b border-slate-900 dark:border-white/20">
                                <td :colspan="visibleColumnsCount" class="px-6 py-6 border-r border-slate-900 dark:border-white/20">
                                    <div class="grid grid-cols-1 gap-6" :class="{
                                        'lg:grid-cols-3': (showMaster ? 1 : 0) + (showCompendium ? 1 : 0) + (showAppendix ? 1 : 0) === 3,
                                        'lg:grid-cols-2': (showMaster ? 1 : 0) + (showCompendium ? 1 : 0) + (showAppendix ? 1 : 0) === 2,
                                        'lg:grid-cols-1': (showMaster ? 1 : 0) + (showCompendium ? 1 : 0) + (showAppendix ? 1 : 0) <= 1
                                    }">
                                        <!-- Master Checklist -->
                                        <div v-if="showMaster" class="overflow-hidden rounded-xl border border-slate-900 shadow-sm dark:border-white/20">
                                            <table class="w-full border-collapse text-left text-[10px]">
                                                <thead class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400 border-b border-slate-900 dark:border-white/20">
                                                    <tr>
                                                        <th class="px-4 py-2">Master Document Fields</th>
                                                        <th class="px-4 py-2 text-center w-20">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-[#1c1c1c] divide-y divide-slate-100 dark:divide-white/5">
                                                    <tr v-for="field in Object.keys(masterConfig)" :key="field">
                                                        <td class="px-4 py-2 font-semibold text-slate-700 dark:text-slate-300">
                                                            {{ masterConfig[field] }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            <div class="flex items-center justify-center">
                                                                <CheckCircleIcon v-if="project.details.master[field]" class="h-4 w-4 text-emerald-500" />
                                                                <XCircleIcon v-else class="h-4 w-4 text-rose-500" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Compendium Checklist -->
                                        <div v-if="showCompendium" class="overflow-hidden rounded-xl border border-slate-900 shadow-sm dark:border-white/20">
                                            <table class="w-full border-collapse text-left text-[10px]">
                                                <thead class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400 border-b border-slate-900 dark:border-white/20">
                                                    <tr>
                                                        <th class="px-4 py-2">Compendium Document</th>
                                                        <th class="px-4 py-2 text-center w-20">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-[#1c1c1c] divide-y divide-slate-100 dark:divide-white/5">
                                                    <tr v-if="project.compendium_score === 'X'">
                                                        <td colspan="2" class="px-4 py-8 text-center italic text-slate-400">
                                                            Document Not Available
                                                        </td>
                                                    </tr>
                                                    <tr v-else v-for="field in Object.keys(compConfig)" :key="field">
                                                        <td class="px-4 py-2 font-semibold text-slate-700 dark:text-slate-300">
                                                            {{ compConfig[field] }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            <div class="flex items-center justify-center">
                                                                <CheckCircleIcon v-if="project.details.compendium[field]" class="h-4 w-4 text-emerald-500" />
                                                                <XCircleIcon v-else class="h-4 w-4 text-rose-500" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Appendix Checklist -->
                                        <div v-if="showAppendix" class="overflow-hidden rounded-xl border border-slate-900 shadow-sm dark:border-white/20">
                                            <table class="w-full border-collapse text-left text-[10px]">
                                                <thead class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400 border-b border-slate-900 dark:border-white/20">
                                                    <tr>
                                                        <th class="px-4 py-2">Appendix Document</th>
                                                        <th class="px-4 py-2 text-center w-20">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-[#1c1c1c] divide-y divide-slate-100 dark:divide-white/5">
                                                    <tr v-if="project.appendix_score === 'X'">
                                                        <td colspan="2" class="px-4 py-8 text-center italic text-slate-400">
                                                            Document Not Available
                                                        </td>
                                                    </tr>
                                                    <tr v-else v-for="field in Object.keys(appConfig)" :key="field">
                                                        <td class="px-4 py-2 font-semibold text-slate-700 dark:text-slate-300">
                                                            {{ appConfig[field] }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            <div class="flex items-center justify-center">
                                                                <CheckCircleIcon v-if="project.details.appendix[field]" class="h-4 w-4 text-emerald-500" />
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
                        <tr v-if="filteredProjects.length === 0">
                            <td :colspan="visibleColumnsCount" class="border-slate-900 px-6 py-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:border-white/20">
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
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const expandedRows = ref(new Set());
const statusFilter = ref('');
const coeFilter = ref('');
const sortBy = ref('');

const showMaster = ref(true);
const showRoadmap = ref(true);
const showAppendix = ref(true);
const showCompendium = ref(true);

const visibleColumnsCount = computed(() => {
    let count = 3; // No, Name, Action
    if (showMaster.value) count += 2;
    if (showRoadmap.value) count += 1;
    if (showAppendix.value) count += 2;
    if (showCompendium.value) count += 2;
    return count;
});

const toggleRow = (id) => {
    if (expandedRows.value.has(id)) {
        expandedRows.value.delete(id);
    } else {
        expandedRows.value.add(id);
    }
};

const splitIncomplete = (val) => {
    if (!val || val === '-') return [];
    // Split on space between brackets e.g. [PIC] [CoE]
    return val.match(/\[[^\]]+\]/g) || [];
};

const getScoreColorClass = (score) => {
    if (score === 'X') return 'text-slate-400 dark:text-slate-600';
    const num = parseInt(score);
    if (num === 100) return 'text-emerald-600 dark:text-emerald-400';
    if (num >= 70) return 'text-amber-600 dark:text-[#E2B93B]';
    return 'text-rose-600 dark:text-rose-400';
};

const availableStatuses = computed(() => {
    const statuses = props.projects.map(p => p.status_name);
    return [...new Set(statuses)].filter(Boolean).sort();
});

const availableCoes = computed(() => {
    const coes = props.projects.map(p => p.coe_name);
    return [...new Set(coes)].filter(Boolean).sort();
});

const parseScore = (score) => {
    if (!score || score === 'X') return -1;
    return parseInt(score) || 0;
};

const filteredProjects = computed(() => {
    let list = props.projects.filter(p => {
        const matchStatus = !statusFilter.value || p.status_name === statusFilter.value;
        const matchCoe = !coeFilter.value || p.coe_name === coeFilter.value;

        return matchStatus && matchCoe;
    });

    if (sortBy.value === 'master') {
        list.sort((a, b) => parseScore(b.master_score) - parseScore(a.master_score));
    } else if (sortBy.value === 'compendium') {
        list.sort((a, b) => parseScore(b.compendium_score) - parseScore(a.compendium_score));
    } else if (sortBy.value === 'appendix') {
        list.sort((a, b) => parseScore(b.appendix_score) - parseScore(a.appendix_score));
    }

    return list;
});

const masterConfig = {
    usecase: 'Use Case Title',
    description: 'Description',
    owner: 'Project Owner',
    pic: 'PIC',
    coe: 'CoE',
    source: 'Data Source',
    source_date: 'Data Source Date',
    goals: 'Goal & Strategic Pillar',
};

const compConfig = {
    usecase: 'Use Case Title',
    description: 'Description',
    value: 'Value',
    urgency: 'Urgency',
    owner: 'Project Owner',
    coe: 'CoE',
    source_id: 'Data Source',
    rjpp_tagging: 'RJPP Tagging',
};

const appConfig = {
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
</script>