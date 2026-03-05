<template>
    <UserLayout title="IT Initiatives">
        <div class="animate-fade-in">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                :class="tableMode === TABLE_MODE.ROADMAP ? 'mb-3' : 'mb-4'"
            >
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">IT Initiatives</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border border-[#1C75BC]/45 bg-[#1C75BC]/10 px-3 py-1.5 text-xs font-semibold text-[#1C75BC] transition hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]"
                            @click="showAllProjectCharter"
                        >
                            Project Charter
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="tableMode === TABLE_MODE.ROADMAP
                                ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                                : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'"
                            @click="showRoadmapView"
                        >
                            Roadmap Project Charter
                        </button>
                    </div>
                </div>
            </div>

            <section
                v-if="tableMode !== TABLE_MODE.ROADMAP"
                class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3"
            >
                <article
                    class="relative flex cursor-pointer flex-col justify-center rounded-2xl border border-[#A7C942] bg-[#A7C942] p-4 shadow-[0_4px_16px_rgba(167,201,66,0.3)]"
                    role="button"
                    tabindex="0"
                    @click="showMasterItInitiatives"
                    @keydown.enter.prevent="showMasterItInitiatives"
                    @keydown.space.prevent="showMasterItInitiatives"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.08em] text-white"
                        style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                    >
                        Total IT Inisiatif Disetujui
                    </p>
                    <p
                        class="mt-2 flex items-center justify-between text-3xl font-bold text-white"
                        style="text-shadow: 0 2px 6px rgba(0,0,0,0.35);"
                    >
                        <span>{{ totalItApproved }}</span>
                    </p>
                </article>

                <article class="space-y-4 rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-[0_4px_16px_rgba(0,0,0,0.05)] dark:border-white/10 dark:bg-[#171717] lg:col-span-2">
                    <div id="project-charter-it-section">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h2 class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">IT Initiatives Implementation Timelines Status (Project Charter)</h2>
                        </div>

                        <div>
                            <div class="grid" :style="gridStyle(digitalSteps)">
                                <div
                                    v-for="(step, index) in digitalSteps"
                                    :key="`step-${step.key}`"
                                    class="group relative flex cursor-pointer justify-center"
                                    @click="handleFlowFilter(step.statusId)"
                                >
                                    <span
                                        class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold transition-all group-hover:ring-2 group-hover:ring-offset-1 group-hover:ring-slate-300"
                                        :class="[
                                            step.circleClass,
                                            activeFlowFilter === step.statusId && tableMode === TABLE_MODE.FLOW ? 'ring-2 ring-offset-2 ring-blue-500 shadow-md transform scale-110' : ''
                                        ]"
                                    >
                                        {{ step.count }}
                                    </span>
                                    <span
                                        v-if="index < digitalSteps.length - 1"
                                        class="absolute left-1/2 top-1/2 ml-[0.75rem] h-0.5 w-[calc(100%_-_1.5rem)] -translate-y-1/2 rounded-full"
                                        :class="step.lineClass"
                                    ></span>
                                </div>
                            </div>

                            <div class="mt-2 grid gap-1 text-center" :style="gridStyle(digitalSteps)">
                                <div v-for="step in digitalSteps" :key="`label-${step.key}`">
                                    <p class="text-[9px] font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <FlowStatusTable
                v-if="hasTableSelection && tableMode === TABLE_MODE.FLOW"
                :items="flowItems"
                :status-options="statusOptions"
                :active-flow-filter="activeFlowFilter"
            />

            <MasterInitiativeTable
                v-else-if="hasTableSelection && tableMode === TABLE_MODE.MASTER"
                :items="masterItems"
            />

            <section
                v-else-if="hasTableSelection && tableMode === TABLE_MODE.ROADMAP"
                class="space-y-3"
            >
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-[15px] font-bold text-slate-800 dark:text-slate-100 sm:text-base">
                                Roadmap Project Charter IT Initiatives
                            </h3>
                        </div>

                        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end">
                            <div class="w-full sm:w-72">
                                <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Filter Project
                                </label>
                                <select
                                    v-model="selectedRoadmapProjectId"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                                >
                                    <option value="all">Semua Project</option>
                                    <option
                                        v-for="project in roadmapSourceItems"
                                        :key="`roadmap-filter-${project.id}`"
                                        :value="String(project.id)"
                                    >
                                        {{ project.code ? `${project.code} - ${project.name}` : project.name }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5"
                                @click="toggleAllProjects"
                            >
                                {{ allExpanded ? 'Collapse All' : 'Expand All' }}
                            </button>
                            <Link
                                :href="addRoadmapHref"
                                class="inline-flex items-center justify-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                            >
                                Add Roadmap
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-for="(project, roadmapIndex) in roadmapItems" :key="`it-roadmap-${project.id}`">
                    <!-- Summary bar (collapsed) -->
                    <ProjectRoadmapSummary
                        v-if="!expandedProjects.has(project.id)"
                        :project="project"
                        :sequence="roadmapIndex + 1"
                        :year-start="roadmapYearStart"
                        :year-end="roadmapYearEnd"
                        :expanded="false"
                        @toggle="toggleProjectExpand(project.id)"
                    />

                    <!-- Detail view (expanded) -->
                    <div v-else>
                        <ProjectRoadmapSummary
                            :project="project"
                            :sequence="roadmapIndex + 1"
                            :year-start="roadmapYearStart"
                            :year-end="roadmapYearEnd"
                            :expanded="true"
                            @toggle="toggleProjectExpand(project.id)"
                        />
                        <ProjectRoadmap
                            :project="project"
                            :form="{
                                objectives: project.charter?.objectives ?? '',
                                duration: project.charter?.duration ?? '',
                            }"
                            :sequence="roadmapIndex + 1"
                            :year-start="roadmapYearStart"
                            :year-end="roadmapYearEnd"
                        />
                    </div>
                </div>

                <section
                    v-if="roadmapItems.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
                >
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        Belum ada data roadmap untuk filter ini.
                    </p>
                </section>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, reactive } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { statusFlowClassByIndex } from '@/Composables/initiativeStatus';
import { useFlowFilter } from '@/Composables/useFlowFilter';
import FlowStatusTable from '@/Components/ITInitiative/FlowStatusTable.vue';
import MasterInitiativeTable from '@/Components/ITInitiative/MasterInitiativeTable.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';
import ProjectRoadmapSummary from '@/Components/Roadmap/ProjectRoadmapSummary.vue';

const props = defineProps({
    itInitiatives: {
        type: Array,
        default: () => [],
    },
    masterItInitiatives: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    totalItInitiatives: {
        type: Number,
        default: 0,
    },
    totalItApproved: {
        type: Number,
        default: 0,
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
});

const asList = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === 'object') {
        return Object.values(value);
    }

    return [];
};

const { activeFlowFilter, filteredItems, toggleFilter } = useFlowFilter(
    () => asList(props.itInitiatives),
    (item) => item.status
);

const TABLE_MODE = {
    FLOW: 'flow',
    MASTER: 'master',
    ROADMAP: 'roadmap',
};

const tableMode = ref(TABLE_MODE.FLOW);
const hasTableSelection = ref(false);
const showAllCharter = ref(false);
const selectedRoadmapProjectId = ref('all');
const roadmapYearStart = 2025;
const roadmapYearEnd = 2029;

// ── Expand / Collapse per project ──
const expandedProjects = reactive(new Set());

const toggleProjectExpand = (projectId) => {
    if (expandedProjects.has(projectId)) {
        expandedProjects.delete(projectId);
    } else {
        expandedProjects.add(projectId);
    }
};

const allExpanded = computed(() => {
    const items = roadmapItems.value;
    return items.length > 0 && items.every((p) => expandedProjects.has(p.id));
});

const toggleAllProjects = () => {
    const items = roadmapItems.value;
    if (allExpanded.value) {
        expandedProjects.clear();
    } else {
        items.forEach((p) => expandedProjects.add(p.id));
    }
};

const showAllProjectCharter = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.FLOW;
    activeFlowFilter.value = null;
    showAllCharter.value = true;
};

const showMasterItInitiatives = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.MASTER;
    activeFlowFilter.value = null;
};

const showRoadmapView = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.ROADMAP;
    activeFlowFilter.value = null;
};

const handleFlowFilter = (statusId) => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.FLOW;
    showAllCharter.value = false;
    toggleFilter(statusId);
};

const flowItems = computed(() => {
    if (showAllCharter.value && activeFlowFilter.value === null) {
        return asList(props.itInitiatives);
    }
    return filteredItems.value;
});

const masterItems = computed(() => {
    return asList(props.masterItInitiatives);
});

const roadmapSourceItems = computed(() => asList(props.itInitiatives));

const roadmapItems = computed(() => {
    const projects = roadmapSourceItems.value;

    if (selectedRoadmapProjectId.value === 'all') {
        return projects;
    }

    const selectedId = Number(selectedRoadmapProjectId.value);

    return projects.filter((project) => Number(project.id) === selectedId);
});

const addRoadmapHref = computed(() => {
    if (selectedRoadmapProjectId.value === 'all') {
        return '/roadmap/add';
    }

    const projectId = Number(selectedRoadmapProjectId.value);

    return projectId > 0 ? `/roadmap/add?project_id=${projectId}` : '/roadmap/add';
});

const statusOptions = computed(() => {
    if (props.statusOptions.length > 0) {
        return props.statusOptions;
    }

    return [
        { id: 1, name: 'drafting', label: 'Drafting' },
        { id: 2, name: 'propose', label: 'Propose' },
        { id: 3, name: 'review', label: 'Review' },
        { id: 4, name: 'approved', label: 'Approved' },
        { id: 5, name: 'baseline', label: 'Baseline' },
    ];
});

const scopeStatusOrder = ['drafting', 'propose', 'review', 'approved'];
const normalizeStatusName = (value) => String(value ?? '').trim().toLowerCase();

const scopeStatusOptions = computed(() => {
    const sourceOptions = Array.isArray(statusOptions.value) ? statusOptions.value : [];
    const mappedStatusByName = new Map();

    sourceOptions.forEach((status) => {
        const candidateNames = [normalizeStatusName(status?.name), normalizeStatusName(status?.label)];

        if (candidateNames.includes('baseline')) {
            return;
        }

        candidateNames.forEach((candidateName) => {
            if (scopeStatusOrder.includes(candidateName) && !mappedStatusByName.has(candidateName)) {
                mappedStatusByName.set(candidateName, status);
            }
        });
    });

    const fallbackStatusOptions = [
        { id: 1, name: 'drafting', label: 'Drafting' },
        { id: 2, name: 'propose', label: 'Propose' },
        { id: 3, name: 'review', label: 'Review' },
        { id: 4, name: 'approved', label: 'Approved' },
    ];

    return scopeStatusOrder.map((statusName, index) => {
        const matchedStatus = mappedStatusByName.get(statusName);
        const fallbackStatus = fallbackStatusOptions[index];

        return {
            id: Number(matchedStatus?.id ?? fallbackStatus.id),
            name: statusName,
            label: fallbackStatus.label,
        };
    });
});

const digitalSteps = computed(() => {
    const counts = props.statusCounts || {};
    return scopeStatusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = status.name;

        return {
            key,
            statusId: status.id,
            label: status.label,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const gridStyle = (steps = []) => ({
    gridTemplateColumns: `repeat(${Math.max(steps.length, 1)}, minmax(0, 1fr))`,
});
</script>
