<template>
    <UserLayout title="Roadmap — Detail Program">
        <div class="mx-auto max-w-7xl space-y-5">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex items-center justify-between mb-1">
                    <Link href="/roadmap" class="text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]">
                        ← Semua Roadmap
                    </Link>
                </div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ program.name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Roadmap semua project dalam program ini.
                </p>
            </section>

            <template v-if="program.projects && program.projects.length">
                <div
                    v-for="(project, projectIndex) in program.projects"
                    :key="project.id"
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717] p-4"
                >
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ project.name }}</h2>
                        <Link
                            :href="`/roadmap/edit?pc_id=${project.charter?.id ?? project.id}`"
                            class="inline-flex items-center rounded-lg bg-[#0B2A8A] px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-[#08226F]"
                        >
                            Input / Edit
                        </Link>
                    </div>

                    <ProjectRoadmap
                        :project="project"
                        :form="{ objectives: project.charter?.objectives ?? '', duration: project.charter?.duration ?? '' }"
                        :sequence="projectIndex + 1"
                        :year-start="yearStart"
                        :year-end="yearEnd"
                        :selected-roadmap-version-id="project.charter?.version_label ?? project.version_label ?? null"
                        :milestone-type-options="milestoneTypeOptions"
                    />
                </div>
            </template>

            <section v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Belum ada project dengan data durasi / milestone bertanggal di program ini.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';

defineProps({
    program: { type: Object, default: () => ({}) },
    yearStart: { type: Number, default: 2025 },
    yearEnd: { type: Number, default: 2029 },
    milestoneTypeOptions: { type: Array, default: () => [] },
});
</script>
