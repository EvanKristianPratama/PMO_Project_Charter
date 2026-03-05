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
                            Pilih Project
                        </label>
                        <select
                            v-model.number="selectedProjectIdLocal"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            @change="handleProjectChange"
                        >
                            <option :value="null">-- Pilih Project --</option>
                            <option v-for="proj in projects" :key="proj.id" :value="proj.id">
                                {{ proj.code ? `${proj.code} - ${proj.name}` : proj.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Versi Project Charter
                        </label>
                        <select
                            v-model.number="selectedCharterIdLocal"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            :disabled="charterOptions.length === 0"
                            @change="handleCharterChange"
                        >
                            <option v-if="charterOptions.length === 0" :value="null">-- Tidak ada versi --</option>
                            <option v-for="charter in charterOptions" :key="charter.id" :value="charter.id">
                                {{ charter.version_label || `v${charter.id}` }}
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            <main v-if="roadmapProject" class="space-y-5">
                
                <ActivityQuarterManager
                    :project="roadmapProject"
                    :selected-roadmap-version-id="selectedRoadmapVersionIdLocal"
                    :milestone-type-options="milestoneTypeOptionsDisplay"
                />
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Project Roadmap</h2>
                    <ProjectRoadmap
                        :project="roadmapProject"
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
    selectedProjectId: { type: Number, default: null },
    selectedCharterId: { type: Number, default: null },
});

const selectedProjectIdLocal = ref(props.selectedProjectId ?? null);
const selectedCharterIdLocal = ref(props.selectedCharterId ?? null);

const milestoneTypeOptionsDisplay = [
    { value: 1, label: 'Blok', timeline_style: 'block' },
    { value: 2, label: 'Garis', timeline_style: 'dashed' },
];

watch(() => props.selectedProjectId, (nextProjectId) => {
    selectedProjectIdLocal.value = nextProjectId ?? null;
}, { immediate: true });

watch(() => props.selectedCharterId, (nextCharterId) => {
    selectedCharterIdLocal.value = nextCharterId ?? null;
}, { immediate: true });

const handleProjectChange = () => {
    const selectedId = Number(selectedProjectIdLocal.value);
    const project = props.projects.find((item) => Number(item.id) === selectedId) ?? null;
    const firstCharterId = project?.charters?.[0]?.id ?? null;
    selectedCharterIdLocal.value = firstCharterId;

    router.get('/roadmap/edit', {
        pc_id: Number.isFinite(Number(firstCharterId)) && Number(firstCharterId) > 0 ? firstCharterId : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const handleCharterChange = () => {
    const selectedId = Number(selectedCharterIdLocal.value);
    router.get('/roadmap/edit', {
        pc_id: Number.isFinite(selectedId) && selectedId > 0 ? selectedId : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const activeProject = computed(() => {
    const selectedId = Number(selectedProjectIdLocal.value);
    return props.projects.find((project) => Number(project.id) === selectedId) ?? null;
});

const charterOptions = computed(() => activeProject.value?.charters ?? []);

const activeCharter = computed(() => {
    const selectedId = Number(selectedCharterIdLocal.value);
    const found = charterOptions.value.find((charter) => Number(charter.id) === selectedId);
    return found ?? charterOptions.value[0] ?? null;
});

watch(charterOptions, (options) => {
    if (!options.length) {
        selectedCharterIdLocal.value = null;
        return;
    }

    const selectedId = Number(selectedCharterIdLocal.value);
    if (!options.some((charter) => Number(charter.id) === selectedId)) {
        selectedCharterIdLocal.value = options[0].id;
    }
}, { immediate: true });

const roadmapProject = computed(() => {
    if (!activeProject.value || !activeCharter.value) {
        return null;
    }

    return {
        ...activeProject.value,
        charter: activeCharter.value,
        milestones: Array.isArray(activeCharter.value.milestones) ? activeCharter.value.milestones : [],
    };
});

const selectedRoadmapVersionIdLocal = computed(() => (
    activeCharter.value?.version_label ?? null
));

</script>
