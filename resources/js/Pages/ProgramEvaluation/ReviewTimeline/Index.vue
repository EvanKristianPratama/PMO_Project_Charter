<template>
    <UserLayout title="Review Timeline">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Review Timeline</h1>
                </div>
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

                    <StatusImplementationTable :projects="initiative.projects" />

                    <div v-if="roadmapProjectsFor(initiative).length > 0" class="mt-4 space-y-2">
                        <div
                            v-for="(roadmapProject, roadmapIndex) in roadmapProjectsFor(initiative)"
                            :key="roadmapEntryKey(initiative, roadmapProject)"
                        >
                            <ProjectRoadmapSummary
                                :project="roadmapProject"
                                :sequence="roadmapIndex + 1"
                                :year-start="roadmapYearStart"
                                :year-end="roadmapYearEnd"
                                :expanded="isRoadmapExpanded(initiative.id, roadmapProject.id)"
                                @toggle="toggleRoadmapExpand(initiative.id, roadmapProject.id)"
                            />
                            <div
                                v-if="isRoadmapExpanded(initiative.id, roadmapProject.id)"
                                class="rounded-b-xl border border-t-0 border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-[#171717]"
                            >
                                <ProjectRoadmap
                                    :project="roadmapProject"
                                    :form="{
                                        objectives: roadmapProject.charter?.objectives ?? '',
                                        duration: roadmapProject.charter?.duration ?? '',
                                    }"
                                    :selected-roadmap-version-id="null"
                                    :sequence="roadmapIndex + 1"
                                    :year-start="roadmapYearStart"
                                    :year-end="roadmapYearEnd"
                                />
                            </div>
                        </div>
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
import StatusImplementationTable from '@/Components/ITInitiative/StatusImplementationTable.vue';
import ProjectRoadmapSummary from '@/Components/Roadmap/ProjectRoadmapSummary.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';

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

    return projects.map((project) => {
        const charters = Array.isArray(project?.charters) && project.charters.length > 0
            ? [...project.charters].sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))
            : (project?.charter ? [project.charter] : []);

        const latestCharter = charters[0] ?? null;

        const milestones = charters.flatMap((charter) => {
            const versionLabel = normalizeVersionLabel(charter?.version_label);
            const charterMilestones = Array.isArray(charter?.milestones) ? charter.milestones : [];

            return charterMilestones.map((milestone) => ({
                ...milestone,
                version: versionLabel || milestone?.version || null,
            }));
        });

        return {
            ...project,
            charters,
            charter: latestCharter,
            milestones,
        };
    });
};

const roadmapEntryKey = (initiative, project) => `initiative-${initiative?.id ?? 'x'}-project-${project?.id ?? 'x'}`;

const isRoadmapExpanded = (initiativeId, projectId) => {
    return expandedRoadmapItems.has(`initiative-${initiativeId}-project-${projectId}`);
};

const toggleRoadmapExpand = (initiativeId, projectId) => {
    const key = `initiative-${initiativeId}-project-${projectId}`;

    if (expandedRoadmapItems.has(key)) {
        expandedRoadmapItems.delete(key);
    } else {
        expandedRoadmapItems.add(key);
    }
};
</script>
