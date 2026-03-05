<template>
    <UserLayout title="Roadmap Input & Edit">
        <div class="space-y-5 print:space-y-0">
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <Link href="/roadmap" class="mb-2 inline-flex text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]">
                            Kembali
                        </Link>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Roadmap Input & Edit</h1>
                    </div>

                    <Link
                        href="/roadmap"
                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                    >
                        Lihat Roadmap
                    </Link>
                </div>

                <div class="grid max-w-4xl gap-3 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Pilih Project Charter
                        </label>
                        <select
                            v-model="selectedProjectIdLocal"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            @change="handleProjectChange"
                        >
                            <option :value="null">-- Pilih Project --</option>
                            <option v-for="proj in projects" :key="proj.id" :value="proj.id">
                                {{
                                    proj.code
                                        ? `${proj.code} - ${proj.name}${proj.charter?.version_label ? ` (${proj.charter.version_label})` : ''}`
                                        : `${proj.name}${proj.charter?.version_label ? ` (${proj.charter.version_label})` : ''}`
                                }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Versi Project Charter
                        </label>
                        <input
                            :value="activeProject?.charter?.version_label ?? activeProject?.version_label ?? '-'"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700 dark:border-white/10 dark:bg-[#0f1623] dark:text-slate-100"
                            readonly
                        />
                    </div>
                </div>
            </section>

            <main v-if="activeProject" class="space-y-5">
                
                <ActivityQuarterManager
                    :project="activeProject"
                    :selected-roadmap-version-id="selectedRoadmapVersionIdLocal"
                    :milestone-type-options="milestoneTypeOptionsDisplay"
                />
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Project Roadmap</h2>
                    <ProjectRoadmap
                        :project="activeProject"
                        :form="{}"
                        :selected-roadmap-version-id="selectedRoadmapVersionIdLocal"
                        :milestone-type-options="milestoneTypeOptionsDisplay"
                    />
                </section>
            </main>

            <section
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    <template v-if="projects.length">
                        Pilih project di atas untuk mulai input / edit roadmap.
                    </template>
                    <template v-else>
                        Belum ada project dengan data durasi / milestone bertanggal untuk diproses.
                    </template>
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ActivityQuarterManager from '@/Components/Roadmap/ActivityQuarterManager.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';

const props = defineProps({
    projects: { type: Array, default: () => [] },
    selectedProject: { type: Object, default: null },
    selectedProjectId: { type: Number, default: null },
    selectedRoadmapVersion: { type: String, default: null },
});

const selectedProjectIdLocal = ref(props.selectedProjectId ?? null);

const milestoneTypeOptionsDisplay = [
    { value: 1, label: 'Blok', timeline_style: 'block' },
    { value: 2, label: 'Garis', timeline_style: 'dashed' },
];

watch(() => props.selectedProjectId, (nextProjectId) => {
    selectedProjectIdLocal.value = nextProjectId ?? null;
}, { immediate: true });

const handleProjectChange = () => {
    router.get('/roadmap/edit', {
        pc_id: selectedProjectIdLocal.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const activeProject = computed(() =>
    props.projects.find((project) => project.id === selectedProjectIdLocal.value) ?? null
);

const selectedRoadmapVersionIdLocal = computed(() => (
    activeProject.value?.charter?.version_label ?? activeProject.value?.version_label ?? null
));

</script>
