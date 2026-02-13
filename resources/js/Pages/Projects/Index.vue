<template>
    <UserLayout title="Projects">
        <div class="animate-fade-in">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">IT Initiatives</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Manage projects and charters</p>
                </div>
                <Link
                    href="/projects/create"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900"
                >
                    + New Project
                </Link>
            </div>

            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 dark:border-white/5 dark:bg-[#1a1a1a] sm:flex-row">
                <div class="relative flex-1">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by name or code..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-10 pr-4 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:placeholder-slate-500"
                        @input="debouncedSearch"
                    />
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select
                    v-model="filters.status"
                    class="rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                    @change="applyFilters"
                >
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in projects.data"
                    :key="project.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white transition-shadow hover:shadow-md dark:border-white/5 dark:bg-[#1a1a1a]"
                >
                    <div class="p-5">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="line-clamp-1 text-lg font-semibold text-slate-900 dark:text-white">{{ project.name }}</h3>
                            </div>
                            <Link
                                :href="`/projects/${project.id}/edit`"
                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-slate-300"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </Link>
                        </div>

                        <div class="mb-4 inline-flex items-center gap-2 rounded-md bg-slate-100 px-2.5 py-1 text-sm text-slate-700 dark:bg-white/10 dark:text-slate-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ ownerName(project) }}</span>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 pb-8 dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-start justify-between">
                                <template v-for="(step, index) in statusFlow" :key="`${project.id}-${step.key}`">
                                    <div v-if="index > 0" class="flex-1 mx-1 mt-[13px] h-0.5 rounded-full" :class="stepLineClass(project.status, index - 1)"></div>
                                    
                                    <div class="relative flex flex-col items-center z-10 w-7">
                                        <span
                                            class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border-2 text-[11px] font-bold"
                                            :class="stepCircleClass(project.status, index)"
                                        >
                                            <svg
                                                v-if="isStepChecked(project.status, index)"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span v-else>{{ index + 1 }}</span>
                                        </span>
                                        <span class="absolute top-8 left-1/2 -translate-x-1/2 w-max text-center text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                            {{ step.label }}
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-4 dark:border-white/5">
                            <Link
                                :href="`/projects/${project.id}`"
                                class="rounded-lg px-2 py-1.5 text-center text-sm font-medium text-indigo-600 transition-colors hover:bg-indigo-50 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300"
                            >
                                View Charter
                            </Link>
                            <Link
                                :href="`/roadmap?project_id=${project.id}`"
                                class="rounded-lg px-2 py-1.5 text-center text-sm font-medium text-teal-600 transition-colors hover:bg-teal-50 hover:text-teal-700 dark:text-teal-400 dark:hover:bg-teal-500/10 dark:hover:text-teal-300"
                            >
                                Roadmap
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="projects.data.length === 0"
                class="mt-6 rounded-xl border border-slate-200 bg-white py-12 text-center dark:border-white/5 dark:bg-[#1a1a1a]"
            >
                <p class="text-slate-500 dark:text-slate-400">No projects found.</p>
                <Link href="/projects/create" class="mt-2 inline-block font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    Create your first project
                </Link>
            </div>

            <div v-if="projects.links && projects.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in projects.links"
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="rounded border border-slate-200 bg-white px-3 py-1 text-sm text-slate-700 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        :class="{'border-indigo-600 bg-indigo-600 text-white': link.active, 'pointer-events-none opacity-50': !link.url}"
                    />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    projects: Object,
    filters: Object,
});

const statusFlow = [
    { key: 'draft', label: 'Draft' },
    { key: 'on_hold', label: 'Review' },
    { key: 'active', label: 'Approved' },
    { key: 'completed', label: 'Complete' },
];

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const statusStepIndex = (status) => {
    const normalized = String(status || '').toLowerCase();

    if (normalized === 'on_hold') {
        return 1;
    }

    if (normalized === 'active') {
        return 2;
    }

    if (normalized === 'completed') {
        return 3;
    }

    return 0;
};

const ownerName = (project) => project.owner_name || project.owner?.name || 'Unassigned';

const isStepChecked = (status, stepIndex) => {
    const current = statusStepIndex(status);

    return stepIndex < current || (String(status || '').toLowerCase() === 'completed' && stepIndex === current);
};

const stepCircleClass = (status, stepIndex) => {
    const current = statusStepIndex(status);
    const normalized = String(status || '').toLowerCase();

    if (stepIndex < current || (normalized === 'completed' && stepIndex === current)) {
        return 'border-emerald-500 bg-emerald-500 text-white';
    }

    if (stepIndex === current) {
        return 'border-amber-500 bg-amber-500 text-white ring-2 ring-amber-200 dark:ring-amber-500/30';
    }

    return 'border-slate-300 bg-slate-100 text-slate-500 dark:border-white/20 dark:bg-white/5 dark:text-slate-400';
};

const stepLineClass = (status, stepIndex) => {
    const current = statusStepIndex(status);

    return stepIndex < current
        ? 'bg-emerald-300 dark:bg-emerald-500/40'
        : 'bg-slate-200 dark:bg-white/10';
};

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    router.get('/projects', filters.value, { preserveState: true, replace: true });
};
</script>
