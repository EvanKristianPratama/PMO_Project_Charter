<template>
    <UserLayout title="Roadmap">
        <div class="space-y-6 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Roadmap {{ yearStart }}-{{ yearEnd }}</h1>
                    </div>

                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end">
                        <div class="w-full sm:w-72">
                            <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Filter Project
                            </label>
                            <select
                                v-model="selectedProjectIdLocal"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            >
                                <option value="all">Semua Project</option>
                                <option
                                    v-for="project in projectOptions"
                                    :key="`filter-project-${project.id}`"
                                    :value="String(project.id)"
                                >
                                    {{ project.code ? `${project.code} - ${project.name}` : project.name }}
                                </option>
                            </select>
                        </div>
                        <div class="w-full sm:w-56">
                            <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Versi Project Charter
                            </label>
                            <select
                                v-model="selectedCharterIdLocal"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                                :disabled="selectedProjectIdLocal === 'all' || charterOptions.length === 0"
                            >
                                <option v-if="selectedProjectIdLocal === 'all'" value="">-- Pilih project dulu --</option>
                                <option v-else-if="charterOptions.length === 0" value="">-- Tidak ada versi --</option>
                                <option v-for="charter in charterOptions" :key="`filter-charter-${charter.id}`" :value="String(charter.id)">
                                    {{ charter.version_label || `v${charter.id}` }}
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
                            href="/roadmap/edit"
                            class="inline-flex items-center justify-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Buka Proses Input / Edit
                        </Link>
                        <Link
                            href="/roadmap/add"
                            class="inline-flex items-center justify-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Add Roadmap
                        </Link>
                    </div>
                </div>
            </section>

            <section v-if="roadmapEntries.length > 0 && displayedProjects.length > 0" class="space-y-0">
                <ProjectRoadmap
                    v-for="(project, projectIndex) in displayedProjects"
                    :key="`roadmap-${project.id}`"
                    :project="project"
                    :form="{
                        objectives: project.charter?.objectives ?? '',
                        duration: project.charter?.duration ?? '',
                    }"
                    :sequence="projectIndex + 1"
                    :year-start="yearStart"
                    :year-end="yearEnd"
                    :selected-roadmap-version-id="project.charter?.version_label ?? project.version_label ?? null"
                    :milestone-type-options="milestoneTypeOptions"
                />
            </section>

            <section
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Belum ada data project untuk ditampilkan atau filter belum cocok.
                </p>
            </section>

        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';
import ProjectRoadmapSummary from '@/Components/Roadmap/ProjectRoadmapSummary.vue';

const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
    selectedProjectId: {
        type: Number,
        default: null,
    },
    selectedCharterId: {
        type: Number,
        default: null,
    },
    yearStart: {
        type: Number,
        default: 2025,
    },
    yearEnd: {
        type: Number,
        default: 2029,
    },
    milestoneTypeOptions: {
        type: Array,
        default: () => [],
    },
});

const selectedProjectIdLocal = ref(
    Number.isFinite(props.selectedProjectId) && props.selectedProjectId > 0
        ? String(props.selectedProjectId)
        : 'all'
);

const selectedCharterIdLocal = ref(
    Number.isFinite(props.selectedCharterId) && props.selectedCharterId > 0
        ? String(props.selectedCharterId)
        : ''
);

const projectOptions = computed(() => Array.isArray(props.projects) ? props.projects : []);

const charterOptions = computed(() => {
    if (selectedProjectIdLocal.value === 'all') {
        return [];
    }

    const selectedId = Number(selectedProjectIdLocal.value);
    const project = projectOptions.value.find((item) => Number(item.id) === selectedId);
    return project?.charters ?? [];
});

const roadmapEntries = computed(() => {
    return projectOptions.value.flatMap((project) => {
        const charters = Array.isArray(project.charters) ? project.charters : [];
        return charters.map((charter) => ({
            id: charter.id,
            project_id: project.id,
            code: project.code,
            name: project.name,
            charter,
            milestones: Array.isArray(charter.milestones) ? charter.milestones : [],
        }));
    });
});

watch(() => selectedProjectIdLocal.value, (nextValue) => {
    if (nextValue === 'all') {
        selectedCharterIdLocal.value = '';
        return;
    }

    const options = charterOptions.value;
    if (!options.length) {
        selectedCharterIdLocal.value = '';
        return;
    }

    const selectedId = Number(selectedCharterIdLocal.value);
    if (!options.some((charter) => Number(charter.id) === selectedId)) {
        selectedCharterIdLocal.value = String(options[0].id);
    }
}, { immediate: true });

const displayedProjects = computed(() => {
    const allEntries = roadmapEntries.value;

    if (selectedProjectIdLocal.value === 'all') {
        return allEntries;
    }

    const selectedProjectId = Number(selectedProjectIdLocal.value);
    let filtered = allEntries.filter((entry) => Number(entry.project_id) === selectedProjectId);

    const selectedCharterId = Number(selectedCharterIdLocal.value);
    if (Number.isFinite(selectedCharterId) && selectedCharterId > 0) {
        filtered = filtered.filter((entry) => Number(entry.id) === selectedCharterId);
    }

    return filtered;
});

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
    const items = displayedProjects.value;
    return items.length > 0 && items.every((p) => expandedProjects.has(p.id));
});

const toggleAllProjects = () => {
    const items = displayedProjects.value;
    if (allExpanded.value) {
        expandedProjects.clear();
    } else {
        items.forEach((p) => expandedProjects.add(p.id));
    }
};
</script>
