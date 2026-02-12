<template>
    <UserLayout title="Roadmap">
        <div class="space-y-5 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Project Roadmap</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Module roadmap terpisah dari project. Hanya project dengan data durasi atau milestone bertanggal yang tampil.
                        </p>
                    </div>
                </div>
            </section>

            <main v-if="selectedProject" class="space-y-5 print:m-0 print:p-0">
                <ActivityQuarterManager :project="selectedProject" />
                <ProjectRoadmap :project="selectedProject" :form="roadmapForm" />
            </main>

            <section v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Belum ada project dengan data durasi/tanggal untuk ditampilkan di roadmap.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProjectRoadmap from './Partials/ProjectRoadmap.vue';
import ActivityQuarterManager from './Partials/ActivityQuarterManager.vue';

const props = defineProps({
    projects: { type: Array, default: () => [] },
    selectedProject: { type: Object, default: null },
    selectedProjectId: { type: Number, default: null },
});

const roadmapForm = computed(() => ({
    objectives: props.selectedProject?.charter?.objectives || '',
    duration: props.selectedProject?.charter?.duration || '',
}));
</script>
