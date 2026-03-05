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
                                Filter Project Charter
                            </label>
                            <select
                                v-model="selectedProjectId"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            >
                                <option value="all">Semua Project</option>
                                <option
                                    v-for="project in projects"
                                    :key="`filter-project-${project.id}`"
                                    :value="String(project.id)"
                                >
                                    {{
                                        project.code
                                            ? `${project.code} - ${project.name}${project.charter?.version_label ? ` (${project.charter.version_label})` : ''}`
                                            : `${project.name}${project.charter?.version_label ? ` (${project.charter.version_label})` : ''}`
                                    }}
                                </option>
                            </select>
                        </div>

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

            <section v-if="projects.length > 0 && displayedProjects.length > 0" class="space-y-0">
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
                    :selected-roadmap-version-id="project.active_roadmap_version ?? null"
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
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';

const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
    selectedProjectId: {
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

const selectedProjectId = ref(
    Number.isFinite(props.selectedProjectId) && props.selectedProjectId > 0
        ? String(props.selectedProjectId)
        : 'all'
);

const displayedProjects = computed(() => {
    const allProjects = Array.isArray(props.projects) ? props.projects : [];

    if (selectedProjectId.value === 'all') {
        return allProjects;
    }

    const selectedId = Number(selectedProjectId.value);
    return allProjects.filter((project) => Number(project.id) === selectedId);
});
</script>
