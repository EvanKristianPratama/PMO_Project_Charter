<template>
    <UserLayout title="IT Initiatives">
        <div class="animate-fade-in">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                :class="
                    tableMode === TABLE_MODE.ROADMAP ||
                    tableMode === TABLE_MODE.IMPLEMENTATION
                        ? 'mb-3'
                        : 'mb-4'
                "
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-slate-900 dark:text-white"
                    >
                        IT Initiatives
                    </h2>
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
                            :class="
                                tableMode === TABLE_MODE.ROADMAP
                                    ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                                    : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            "
                            @click="showRoadmapView"
                        >
                            Roadmap Project Charter
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="
                                tableMode === TABLE_MODE.IMPLEMENTATION
                                    ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                                    : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            "
                            @click="showImplementationView"
                        >
                            Status Implementation
                        </button>
                    </div>
                </div>

                <div
                    v-if="tableMode === TABLE_MODE.IMPLEMENTATION"
                    class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end"
                >
                    <div class="w-full sm:w-[220px]">
                        <label
                            class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                        >
                            Filter Initiative
                        </label>
                        <select
                            v-model="selectedImplementationInitiativeId"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                        >
                            <option value="all">Semua Initiative</option>
                            <option
                                v-for="initiative in masterItInitiatives"
                                :key="`initiative-filter-${initiative.id}`"
                                :value="String(initiative.id)"
                            >
                                {{ implementationInitiativeLabel(initiative) }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-[160px]">
                        <label
                            class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                        >
                            Filter Progres Status
                        </label>
                        <select
                            v-model="selectedProgresStatus"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                        >
                            <option value="all">Semua Status</option>
                            <option value="Not Started">Not Started</option>
                            <option value="On Track">On Track</option>
                            <option value="Done">Done</option>
                            <option value="At Risk">At Risk</option>
                            <option value="Delayed">Delayed</option>
                        </select>
                    </div>

                    <div class="flex flex-row items-center gap-1.5">
                        <button
                            type="button"
                            class="inline-flex h-[26px] items-center justify-center rounded-full border px-3 text-[10px] font-semibold shadow-none transition-all focus:outline-none focus:ring-1 focus:ring-offset-1"
                            :class="
                                showInitiativeLabel
                                    ? 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-700/40 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50'
                                    : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-700/40 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50'
                            "
                            @click="showInitiativeLabel = !showInitiativeLabel"
                        >
                            {{
                                showInitiativeLabel
                                    ? "Hide Label"
                                    : "Show Label"
                            }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex h-[26px] items-center justify-center rounded-full border px-3 text-[10px] font-semibold shadow-none transition-all focus:outline-none focus:ring-1 focus:ring-offset-1"
                            :class="
                                showTimelineHistory
                                    ? 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-700/40 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50'
                                    : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-700/40 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50'
                            "
                            @click="showTimelineHistory = !showTimelineHistory"
                        >
                            {{
                                showTimelineHistory
                                    ? "Hide Timeline"
                                    : "Show Timeline"
                            }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex h-[26px] items-center justify-center rounded-full border px-3 text-[10px] font-semibold shadow-none transition-all focus:outline-none focus:ring-1 focus:ring-offset-1"
                            :class="
                                showImplementationRoadmap
                                    ? 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-700/40 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50'
                                    : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-700/40 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50'
                            "
                            @click="toggleImplementationRoadmapVisibility"
                        >
                            {{
                                showImplementationRoadmap
                                    ? "Hide Roadmap"
                                    : "Show Roadmap"
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <section
                v-if="
                    tableMode !== TABLE_MODE.ROADMAP &&
                    tableMode !== TABLE_MODE.IMPLEMENTATION
                "
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
                        style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3)"
                    >
                        Total IT Inisiatif Disetujui
                    </p>
                    <p
                        class="mt-2 flex items-center justify-between text-3xl font-bold text-white"
                        style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.35)"
                    >
                        <span>{{ totalItApproved }}</span>
                    </p>
                </article>

                <article
                    class="space-y-4 rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-[0_4px_16px_rgba(0,0,0,0.05)] dark:border-white/10 dark:bg-[#171717] lg:col-span-2"
                >
                    <div id="project-charter-it-section">
                        <div
                            class="mb-2 flex items-center justify-between gap-2"
                        >
                            <h2
                                class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                IT Initiatives Implementation Timelines Status
                                (Project Charter)
                            </h2>
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
                                            activeFlowFilter ===
                                                step.statusId &&
                                            tableMode === TABLE_MODE.FLOW
                                                ? 'ring-2 ring-offset-2 ring-blue-500 shadow-md transform scale-110'
                                                : '',
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

                            <div
                                class="mt-2 grid gap-1 text-center"
                                :style="gridStyle(digitalSteps)"
                            >
                                <div
                                    v-for="step in digitalSteps"
                                    :key="`label-${step.key}`"
                                >
                                    <p
                                        class="text-[9px] font-semibold text-slate-700 dark:text-slate-200"
                                    >
                                        {{ step.label }}
                                    </p>
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
                v-else-if="
                    hasTableSelection && tableMode === TABLE_MODE.ROADMAP
                "
                class="space-y-6"
            >
                <div
                    class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h3
                                class="text-base font-bold text-slate-800 dark:text-slate-100"
                            >
                                Roadmap Project Charter IT Initiatives
                            </h3>
                        </div>

                        <div
                            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end"
                        >
                            <div class="w-full sm:w-72">
                                <label
                                    class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                                >
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
                                        {{
                                            project.code
                                                ? `${project.code} - ${project.name}${
                                                      project.charter
                                                          ?.version_label
                                                          ? `
                                        (${project.charter.version_label})`
                                                          : ""
                                                  }`
                                                : `${project.name}${
                                                      project.charter
                                                          ?.version_label
                                                          ? `
                                        (${project.charter.version_label})`
                                                          : ""
                                                  }`
                                        }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5"
                                @click="toggleAllProjects"
                            >
                                {{
                                    allExpanded ? "Collapse All" : "Expand All"
                                }}
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

                <div
                    v-for="(projectGroup, roadmapIndex) in roadmapItems"
                    :key="`it-roadmap-group-${projectGroup.id}`"
                    class="space-y-3"
                >
                    <!-- Project Group Header -->
                    <div class="flex items-center gap-3 px-1">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-white/10 dark:text-slate-400"
                        >
                            {{ roadmapIndex + 1 }}
                        </span>
                        <h4
                            class="text-base font-bold text-slate-800 dark:text-slate-200"
                        >
                            {{ projectGroup.name }}
                        </h4>
                    </div>

                    <!-- Version Rows Container -->
                    <div
                        class="flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10"
                    >
                        <template
                            v-for="(
                                versionProject, versionIndex
                            ) in projectGroup.versions"
                            :key="`version-${versionProject.uniqueId}`"
                        >
                            <!-- Summary Row -->
                            <ProjectRoadmapSummary
                                :project="versionProject"
                                :sequence="versionIndex + 1"
                                :year-start="roadmapYearStart"
                                :year-end="roadmapYearEnd"
                                :expanded="
                                    expandedProjects.has(
                                        versionProject.uniqueId,
                                    )
                                "
                                :show-date="true"
                                @toggle="
                                    toggleProjectExpand(versionProject.uniqueId)
                                "
                            />

                            <!-- Detail content (ProjectRoadmap) -->
                            <div
                                v-if="
                                    expandedProjects.has(
                                        versionProject.uniqueId,
                                    )
                                "
                                class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5"
                            >
                                <ProjectRoadmap
                                    :project="versionProject"
                                    :form="{
                                        objectives:
                                            versionProject.charter
                                                ?.objectives ?? '',
                                        duration:
                                            versionProject.charter?.duration ??
                                            '',
                                    }"
                                    :selected-roadmap-version-id="
                                        versionProject.charter?.id
                                    "
                                    :sequence="versionIndex + 1"
                                    :year-start="roadmapYearStart"
                                    :year-end="roadmapYearEnd"
                                />
                            </div>
                        </template>
                    </div>
                </div>

                <section
                    v-if="roadmapItems.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
                >
                    <p
                        class="text-sm font-medium text-slate-600 dark:text-slate-300"
                    >
                        Belum ada data roadmap untuk filter ini.
                    </p>
                </section>
            </section>

            <section
                v-else-if="
                    hasTableSelection && tableMode === TABLE_MODE.IMPLEMENTATION
                "
                class="space-y-4"
            >
                <article
                    v-for="(
                        initiative, initiativeIndex
                    ) in filteredImplementationInitiativeItems"
                    :key="`initiative-${initiative.id}`"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div
                        v-if="showInitiativeLabel"
                        class="mb-3 flex flex-wrap items-center justify-between gap-2"
                    >
                        <div>
                            <h2
                                class="text-sm font-semibold text-slate-800 dark:text-slate-100"
                            >
                                {{ implementationInitiativeLabel(initiative) }}
                            </h2>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-700 dark:border-white/15 dark:bg-white/5 dark:text-slate-300"
                        >
                            {{ projectCountLabel(initiative.projects) }}
                        </span>
                    </div>

                    <div
                        v-for="(project, projectIndex) in initiative.projects"
                        :key="`project-impl-${project.id}`"
                        class="mb-8 last:mb-0"
                    >
                        <StatusImplementationTable
                            :project="project"
                            :showTimelineHistory="showTimelineHistory"
                            :showHeader="
                                initiativeIndex === 0 && projectIndex === 0
                            "
                        />
                    </div>

                    <div
                        v-if="
                            showImplementationRoadmap &&
                            roadmapProjectsFor(initiative).length > 0
                        "
                        class="mt-5 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10"
                    >
                        <template
                            v-for="(
                                roadmapProject, roadmapIndex
                            ) in roadmapProjectsFor(initiative)"
                            :key="roadmapEntryKey(initiative, roadmapProject)"
                        >
                            <ProjectRoadmapSummary
                                :project="roadmapProject"
                                :sequence="roadmapIndex + 1"
                                :year-start="roadmapYearStart"
                                :year-end="roadmapYearEnd"
                                :expanded="
                                    isImplementationRoadmapExpanded(
                                        initiative.id,
                                        roadmapProject.roadmap_key,
                                    )
                                "
                                :display-version-label="
                                    roadmapProject.roadmap_version_label ?? null
                                "
                                @toggle="
                                    toggleImplementationRoadmapExpand(
                                        initiative.id,
                                        roadmapProject.roadmap_key,
                                    )
                                "
                            />

                            <!-- Detail content (ProjectRoadmap) -->
                            <div
                                v-if="
                                    isImplementationRoadmapExpanded(
                                        initiative.id,
                                        roadmapProject.roadmap_key,
                                    )
                                "
                                class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5"
                            >
                                <ProjectRoadmap
                                    :project="roadmapProject"
                                    :form="{
                                        objectives:
                                            roadmapProject.charter
                                                ?.objectives ?? '',
                                        duration:
                                            roadmapProject.charter?.duration ??
                                            '',
                                    }"
                                    :selected-roadmap-version-id="
                                        roadmapProject.roadmap_version_label ??
                                        null
                                    "
                                    :sequence="roadmapIndex + 1"
                                    :year-start="roadmapYearStart"
                                    :year-end="roadmapYearEnd"
                                />
                            </div>
                        </template>
                    </div>
                </article>

                <section
                    v-if="filteredImplementationInitiativeItems.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
                >
                    <p
                        class="text-sm font-medium text-slate-600 dark:text-slate-300"
                    >
                        Belum ada data project untuk ditampilkan.
                    </p>
                </section>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, reactive, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useRouteHelper } from "@/Composables/useRouteHelper";
import UserLayout from "@/Layouts/UserLayout.vue";
import { statusFlowClassByIndex } from "@/Composables/initiativeStatus";
import { useFlowFilter } from "@/Composables/useFlowFilter";
import FlowStatusTable from "@/Components/ITInitiative/FlowStatusTable.vue";
import MasterInitiativeTable from "@/Components/ITInitiative/MasterInitiativeTable.vue";
import StatusImplementationTable from "@/Components/ITInitiative/StatusImplementationTable.vue";
import ProjectRoadmap from "@/Components/Roadmap/ProjectRoadmap.vue";
import ProjectRoadmapSummary from "@/Components/Roadmap/ProjectRoadmapSummary.vue";

const props = defineProps({
    itInitiatives: {
        type: Array,
        default: () => [],
    },
    masterItInitiatives: {
        type: Array,
        default: () => [],
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

const route = useRouteHelper();

const asList = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === "object") {
        return Object.values(value);
    }

    return [];
};

const TYPE_IT_INITIATIVE = 2;

const isItProject = (item) =>
    Number(item?.tipe_inisiative) === TYPE_IT_INITIATIVE;

const itProjectItems = computed(() =>
    asList(props.itInitiatives).filter(isItProject),
);

const FLOW_NOT_YET_ID = 0;
const FLOW_STATUS_STEPS = [
    { id: FLOW_NOT_YET_ID, name: "not_start", label: "Not Start" },
    { id: 1, name: "drafting", label: "Drafting" },
    { id: 2, name: "propose", label: "Propose" },
    { id: 3, name: "review", label: "Review" },
    { id: 5, name: "baseline", label: "Baseline" },
    { id: 4, name: "approved", label: "Approved" },
];

const normalizeProjectStatusId = (value) => {
    if (value === null || value === "" || value === undefined) {
        return FLOW_NOT_YET_ID;
    }
    const parsed = Number(value);
    return Number.isInteger(parsed) && parsed >= 0 ? parsed : FLOW_NOT_YET_ID;
};

const latestProjectStatusHistory = (item) => {
    const histories =
        item?.project_status_histories ?? item?.projectStatusHistories ?? [];
    return Array.isArray(histories) && histories.length > 0
        ? histories[0]
        : null;
};

const resolvedProjectStatusId = (item) => {
    // 1. Check history first (most accurate for transitioned projects)
    const historyStatus = latestProjectStatusHistory(item)?.status;
    if (
        historyStatus !== null &&
        historyStatus !== undefined &&
        historyStatus !== ""
    ) {
        return normalizeProjectStatusId(historyStatus);
    }

    // 2. Fallback to project status (for newly synced projects without history)
    if (item?.status !== null && item?.status !== undefined) {
        return normalizeProjectStatusId(item.status);
    }

    return FLOW_NOT_YET_ID;
};

const { activeFlowFilter, filteredItems, toggleFilter } = useFlowFilter(
    () => itProjectItems.value,
    (item) => resolvedProjectStatusId(item),
);

const TABLE_MODE = {
    FLOW: "flow",
    MASTER: "master",
    ROADMAP: "roadmap",
    IMPLEMENTATION: "implementation",
};

const tableMode = ref(TABLE_MODE.FLOW);
const hasTableSelection = ref(false);
const showAllCharter = ref(false);
const selectedRoadmapProjectId = ref("all");
const roadmapYearStart = 2025;
const roadmapYearEnd = 2029;

// ── Implementation View State (Matching ReviewTimeline) ──
const selectedImplementationInitiativeId = ref("all");
const selectedProgresStatus = ref("all");
const showImplementationRoadmap = ref(true);
const showTimelineHistory = ref(true);
const showInitiativeLabel = ref(true);
const expandedImplementationRoadmapItems = reactive(new Set());
const monthOrderMap = new Map([
    ["Januari", 1],
    ["Februari", 2],
    ["Maret", 3],
    ["April", 4],
    ["Mei", 5],
    ["Juni", 6],
    ["Juli", 7],
    ["Agustus", 8],
    ["September", 9],
    ["Oktober", 10],
    ["November", 11],
    ["Desember", 12],
]);

const parseImplementationLogDate = (value) => {
    const parsed = Date.parse(String(value ?? "").trim());
    return Number.isNaN(parsed) ? null : parsed;
};

const parseImplementationLogYear = (value) => {
    const parsed = Number.parseInt(String(value ?? "").trim(), 10);
    return Number.isFinite(parsed) ? parsed : null;
};

const sortImplementationLogs = (left, right) => {
    const leftYear = parseImplementationLogYear(left?.year);
    const rightYear = parseImplementationLogYear(right?.year);

    if (leftYear !== rightYear) {
        return (leftYear ?? Number.MIN_SAFE_INTEGER) - (rightYear ?? Number.MIN_SAFE_INTEGER);
    }

    const leftMonth = monthOrderMap.get(String(left?.month ?? "").trim()) ?? 0;
    const rightMonth = monthOrderMap.get(String(right?.month ?? "").trim()) ?? 0;

    if (leftMonth !== rightMonth) {
        return leftMonth - rightMonth;
    }

    const leftTimestamp =
        parseImplementationLogDate(left?.created_at) ??
        parseImplementationLogDate(left?.updated_at);
    const rightTimestamp =
        parseImplementationLogDate(right?.created_at) ??
        parseImplementationLogDate(right?.updated_at);

    if (leftTimestamp !== rightTimestamp) {
        return (leftTimestamp ?? Number.MIN_SAFE_INTEGER) - (rightTimestamp ?? Number.MIN_SAFE_INTEGER);
    }

    return Number(left?.id || 0) - Number(right?.id || 0);
};

const implementationLogsFor = (project) => {
    return asList(
        project?.pc_status_implementations ?? project?.pcStatusImplementations,
    ).sort(sortImplementationLogs);
};

const latestImplementationLogFor = (project) => {
    const logs = implementationLogsFor(project);
    return logs.length > 0 ? logs[logs.length - 1] : null;
};

const filteredImplementationInitiativeItems = computed(() => {
    let items = asList(props.masterItInitiatives);

    // Filter by Initiative
    if (selectedImplementationInitiativeId.value !== "all") {
        const selectedId = Number(selectedImplementationInitiativeId.value);
        items = items.filter(
            (initiative) => Number(initiative?.id) === selectedId,
        );
    }

    // Filter by Progres Status
    if (selectedProgresStatus.value !== "all") {
        items = items
            .map((init) => {
                const projects = (init.projects || []).filter((proj) => {
                    const latestStatus =
                        String(
                            latestImplementationLogFor(proj)?.status ?? "",
                        ).trim() || "Not Started";

                    return latestStatus === selectedProgresStatus.value;
                });
                return { ...init, projects };
            })
            .filter((init) => init.projects.length > 0);
    }

    return items;
});

const implementationInitiativeLabel = (initiative) => {
    const code = String(initiative?.code ?? "").trim();
    const name = String(initiative?.name ?? "").trim();
    return `Code: ${code || "-"} | Initiative: ${name || "-"}`;
};

const projectCountLabel = (projects) => {
    const total = Array.isArray(projects) ? projects.length : 0;
    return `${total} Project${total === 1 ? "" : "s"}`;
};

const normalizeVersionLabel = (value) => {
    const raw = String(value ?? "").trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === "v") {
        return "v1";
    }

    if (/^v\d+$/.test(lower)) {
        return `v${Math.max(Number(lower.slice(1)) || 1, 1)}`;
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return raw;
};

const roadmapProjectsFor = (initiative) => {
    const projects = Array.isArray(initiative?.projects)
        ? initiative.projects
        : [];

    return projects.flatMap((project) => {
        const charters =
            Array.isArray(project?.charters) && project.charters.length > 0
                ? [...project.charters].sort(
                      (a, b) => Number(b?.id || 0) - Number(a?.id || 0),
                  )
                : project?.charter
                  ? [project.charter]
                  : [];

        if (charters.length === 0) {
            return [
                {
                    ...project,
                    charters: [],
                    charter: null,
                    milestones: [],
                    roadmap_version_label: null,
                    roadmap_version_key: "v1",
                    roadmap_key: `project-${project?.id ?? "x"}-charter-none`,
                },
            ];
        }

        return charters.map((charter, charterIndex) => {
            const versionLabelRaw = String(charter?.version_label ?? "").trim();
            const versionLabelDisplay =
                versionLabelRaw ||
                `v${Math.max(charters.length - charterIndex, 1)}`;
            const versionKey = normalizeVersionLabel(versionLabelDisplay);
            const charterMilestones = Array.isArray(charter?.milestones)
                ? charter.milestones
                : [];

            const milestones = charterMilestones.map((milestone) => ({
                ...milestone,
                version: versionKey || milestone?.version || null,
            }));

            return {
                ...project,
                charters: [charter],
                charter,
                milestones,
                roadmap_version_label: versionLabelDisplay,
                roadmap_version_key: versionKey,
                roadmap_key: `project-${project?.id ?? "x"}-charter-${charter?.id ?? charterIndex}`,
            };
        });
    });
};

const roadmapEntryKey = (initiative, project) =>
    `initiative-${initiative?.id ?? "x"}-${project?.roadmap_key ?? `project-${project?.id ?? "x"}`}`;

const isImplementationRoadmapExpanded = (initiativeId, roadmapKey) => {
    return expandedImplementationRoadmapItems.has(
        `initiative-${initiativeId}-${roadmapKey}`,
    );
};

const toggleImplementationRoadmapExpand = (initiativeId, roadmapKey) => {
    const key = `initiative-${initiativeId}-${roadmapKey}`;

    if (expandedImplementationRoadmapItems.has(key)) {
        expandedImplementationRoadmapItems.delete(key);
    } else {
        expandedImplementationRoadmapItems.add(key);
    }
};

const toggleImplementationRoadmapVisibility = () => {
    showImplementationRoadmap.value = !showImplementationRoadmap.value;

    if (!showImplementationRoadmap.value) {
        expandedImplementationRoadmapItems.clear();
    }
};

// ── Expand / Collapse per project ──
const expandedProjects = reactive(new Set());

const toggleProjectExpand = (uniqueId) => {
    if (expandedProjects.has(uniqueId)) {
        expandedProjects.delete(uniqueId);
    } else {
        expandedProjects.add(uniqueId);
    }
};

const allExpanded = computed(() => {
    const allIds = roadmapItems.value.flatMap((p) =>
        p.versions.map((v) => v.uniqueId),
    );
    return allIds.length > 0 && allIds.every((id) => expandedProjects.has(id));
});

const toggleAllProjects = () => {
    const allIds = roadmapItems.value.flatMap((p) =>
        p.versions.map((v) => v.uniqueId),
    );
    if (allExpanded.value) {
        expandedProjects.clear();
    } else {
        allIds.forEach((id) => expandedProjects.add(id));
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

const showImplementationView = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.IMPLEMENTATION;
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
        return itProjectItems.value;
    }
    return filteredItems.value;
});

const masterItems = computed(() => {
    return asList(props.masterItInitiatives);
});

const roadmapSourceItems = computed(() => itProjectItems.value);

const roadmapItems = computed(() => {
    let projects = roadmapSourceItems.value;

    if (selectedRoadmapProjectId.value !== "all") {
        const selectedId = Number(selectedRoadmapProjectId.value);
        projects = projects.filter(
            (project) => Number(project.id) === selectedId,
        );
    }

    return projects.map((project) => {
        const charters =
            Array.isArray(project?.charters) && project.charters.length > 0
                ? [...project.charters].sort(
                      (a, b) => Number(b.id || 0) - Number(a.id || 0),
                  )
                : project?.charter
                  ? [project.charter]
                  : [];

        const versions = charters.map((charter) => {
            const versionLabel = String(charter?.version_label ?? "").trim();
            const charterMilestones = Array.isArray(charter?.milestones)
                ? charter.milestones
                : [];
            const milestones = charterMilestones.map((m) => ({
                ...m,
                version: versionLabel || m?.version || null,
            }));

            return {
                ...project,
                charters: [charter],
                charter: charter,
                milestones,
                uniqueId: `${project.id}-${charter.id}`,
            };
        });

        if (versions.length === 0) {
            versions.push({
                ...project,
                charters: [],
                charter: null,
                milestones: [],
                uniqueId: `${project.id}-empty`,
            });
        }

        return {
            id: project.id,
            name: project.name,
            versions,
        };
    });
});

const addRoadmapHref = computed(() => {
    if (selectedRoadmapProjectId.value === "all") {
        return route("roadmap.add");
    }

    const selectedProject = roadmapSourceItems.value.find(
        (project) =>
            Number(project.id) === Number(selectedRoadmapProjectId.value),
    );
    const selectedPcId = Number(selectedProject?.charter?.id ?? 0);

    return selectedPcId > 0
        ? route("roadmap.add", { pc_id: selectedPcId })
        : route("roadmap.add");
});

const statusOptions = computed(() => {
    const sourceOptions = Array.isArray(props.statusOptions)
        ? props.statusOptions
        : [];
    const sourceById = new Map(
        sourceOptions
            .map((status) => [Number(status?.id), status])
            .filter(([id]) => Number.isInteger(id)),
    );

    return FLOW_STATUS_STEPS.map((step) => {
        const matched = sourceById.get(step.id);

        return {
            id: step.id,
            name: step.name,
            label: step.label,
            rawName: matched?.name ?? step.name,
        };
    });
});

const countProjectsByStatus = (statusId) => {
    return itProjectItems.value.filter(
        (item) => resolvedProjectStatusId(item) === Number(statusId),
    ).length;
};

const digitalSteps = computed(() => {
    return statusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);

        return {
            key: status.name,
            statusId: status.id,
            label: status.label,
            count: countProjectsByStatus(status.id),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const gridStyle = (steps = []) => ({
    gridTemplateColumns: `repeat(${Math.max(steps.length, 1)}, minmax(0, 1fr))`,
});

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const mode = params.get("tableMode");
    if (mode && Object.values(TABLE_MODE).includes(mode)) {
        tableMode.value = mode;
        hasTableSelection.value = true;
    }
});
</script>
