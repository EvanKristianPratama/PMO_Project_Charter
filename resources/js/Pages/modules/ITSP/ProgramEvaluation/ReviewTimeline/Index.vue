<template>
    <UserLayout title="Review Timeline">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end">
                    <div class="w-full sm:w-[360px]">
                        <label
                            class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                        >
                            Filter Initiative
                        </label>
                        <select
                            v-model="selectedInitiativeId"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                        >
                            <option value="all">Semua Initiative</option>
                            <option
                                v-for="initiative in initiativeItems"
                                :key="`initiative-filter-${initiative.id}`"
                                :value="String(initiative.id)"
                            >
                                {{ initiativeLabel(initiative) }}
                            </option>
                        </select>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs font-semibold transition"
                        :class="showRoadmap
                            ? 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/30'
                            : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-700/40 dark:bg-emerald-900/20 dark:text-emerald-300 dark:hover:bg-emerald-900/30'"
                        @click="toggleRoadmapVisibility"
                    >
                        {{ showRoadmap ? 'Hide Roadmap' : 'Show Roadmap' }}
                    </button>
                </div>
            </div>

            <section v-if="filteredInitiativeItems.length > 0" class="space-y-4">
                <article
                    v-for="initiative in filteredInitiativeItems"
                    :key="`initiative-${initiative.id}`"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ initiativeLabel(initiative) }}
                            </h2>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-700 dark:border-white/15 dark:bg-white/5 dark:text-slate-300"
                        >
                            {{ projectCountLabel(initiative.projects) }}
                        </span>
                    </div>

                    <StatusImplementationTable
                        :projects="initiative.projects"
                        history-source="review"
                        store-route-name="program-evaluation.review-timeline.review-status.store"
                        update-route-name="program-evaluation.review-timeline.review-status.update"
                    />

                    <div v-if="showRoadmap && roadmapProjectsFor(initiative).length > 0" 
                         class="mt-5 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10"
                    >
                        <template
                            v-for="(roadmapProject, roadmapIndex) in roadmapProjectsFor(initiative)"
                            :key="roadmapEntryKey(initiative, roadmapProject)"
                        >
                            <ProjectRoadmapSummary
                                :project="roadmapProject"
                                :sequence="roadmapIndex + 1"
                                :year-start="roadmapYearStart"
                                :year-end="roadmapYearEnd"
                                :expanded="isRoadmapExpanded(initiative.id, roadmapProject.roadmap_key)"
                                :display-version-label="roadmapProject.roadmap_version_label ?? null"
                                @toggle="toggleRoadmapExpand(initiative.id, roadmapProject.roadmap_key)"
                            />
                            
                            <!-- Detail content (ProjectRoadmap) -->
                            <div
                                v-if="isRoadmapExpanded(initiative.id, roadmapProject.roadmap_key)"
                                class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5"
                            >
                                <ProjectRoadmap
                                    :project="roadmapProject"
                                    :form="{
                                        objectives: roadmapProject.charter?.objectives ?? '',
                                        duration: roadmapProject.charter?.duration ?? '',
                                    }"
                                    :selected-roadmap-version-id="roadmapProject.roadmap_version_label ?? null"
                                    :sequence="roadmapIndex + 1"
                                    :year-start="roadmapYearStart"
                                    :year-end="roadmapYearEnd"
                                />
                            </div>
                        </template>
                    </div>
                </article>
            </section>

            <section
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Belum ada data initiative dengan project charter.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import StatusImplementationTable from '@/Components/modules/ITSP/ITInitiative/ReviewStatusImplementationTable.vue';
import ProjectRoadmapSummary from '@/Components/modules/ITSP/Roadmap/ProjectRoadmapSummary.vue';
import ProjectRoadmap from '@/Components/modules/ITSP/Roadmap/ProjectRoadmap.vue';

const props = defineProps({
    initiatives: {
        type: Array,
        default: () => [],
    },
});

const initiativeItems = computed(() => (
    Array.isArray(props.initiatives) ? props.initiatives : []
));
const selectedInitiativeId = ref('all');
const showRoadmap = ref(true);
const roadmapYearStart = 2025;
const roadmapYearEnd = 2029;
const expandedRoadmapItems = reactive(new Set());

const filteredInitiativeItems = computed(() => {
    if (selectedInitiativeId.value === 'all') {
        return initiativeItems.value;
    }

    const selectedId = Number(selectedInitiativeId.value);
    return initiativeItems.value.filter((initiative) => Number(initiative?.id) === selectedId);
});

const initiativeLabel = (initiative) => {
    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();
    return `Code: ${code || '-'} | Initiative: ${name || '-'}`;
};

const projectCountLabel = (projects) => {
    const total = Array.isArray(projects) ? projects.length : 0;
    return `${total} Project${total === 1 ? '' : 's'}`;
};

const normalizeVersionLabel = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === 'v') {
        return 'v1';
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
    const projects = Array.isArray(initiative?.projects) ? initiative.projects : [];

    return projects.flatMap((project) => {
        const charters = Array.isArray(project?.charters) && project.charters.length > 0
            ? [...project.charters].sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))
            : (project?.charter ? [project.charter] : []);

        if (charters.length === 0) {
            return [{
                ...project,
                charters: [],
                charter: null,
                milestones: [],
                roadmap_version_label: null,
                roadmap_version_key: 'v1',
                roadmap_key: `project-${project?.id ?? 'x'}-charter-none`,
            }];
        }

        return charters.map((charter, charterIndex) => {
            const versionLabelRaw = String(charter?.version_label ?? '').trim();
            const versionLabelDisplay = versionLabelRaw || `v${Math.max(charters.length - charterIndex, 1)}`;
            const versionKey = normalizeVersionLabel(versionLabelDisplay);
            const charterMilestones = Array.isArray(charter?.milestones) ? charter.milestones : [];

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
                roadmap_key: `project-${project?.id ?? 'x'}-charter-${charter?.id ?? charterIndex}`,
            };
        });
    });
};

const roadmapEntryKey = (initiative, project) => (
    `initiative-${initiative?.id ?? 'x'}-${project?.roadmap_key ?? `project-${project?.id ?? 'x'}`}`
);

const isRoadmapExpanded = (initiativeId, roadmapKey) => {
    return expandedRoadmapItems.has(`initiative-${initiativeId}-${roadmapKey}`);
};

const toggleRoadmapExpand = (initiativeId, roadmapKey) => {
    const key = `initiative-${initiativeId}-${roadmapKey}`;

    if (expandedRoadmapItems.has(key)) {
        expandedRoadmapItems.delete(key);
    } else {
        expandedRoadmapItems.add(key);
    }
};

const toggleRoadmapVisibility = () => {
    showRoadmap.value = !showRoadmap.value;

    if (!showRoadmap.value) {
        expandedRoadmapItems.clear();
    }
};
</script>
