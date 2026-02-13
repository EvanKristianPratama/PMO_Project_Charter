<template>
    <UserLayout title="IT Initiatives">
        <div class="animate-fade-in">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">IT Initiatives</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Manage projects and charters</p>
                </div>
                <Link
                    href="/it-initiatives/create"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900"
                >
                    + New IT Initiative
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

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/5">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Timelines</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                        <tr v-for="project in itInitiatives.data" :key="project.id" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ project.charter?.category || 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-900 dark:text-white">{{ project.name }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ project.code }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center w-full max-w-xs">
                                    <template v-for="(step, index) in statusFlow" :key="`${project.id}-${step.key}`">
                                        <!-- Line -->
                                        <div v-if="index > 0" class="flex-1 mx-1 h-0.5 rounded-full" :class="stepLineClass(project.status, index - 1)"></div>
                                        
                                        <!-- Circle -->
                                        <div class="relative flex flex-col items-center group/tooltip">
                                            <span
                                                class="inline-flex h-3 w-3 flex-shrink-0 rounded-full border"
                                                :class="stepCircleClass(project.status, index)"
                                            ></span>
                                            
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full mb-2 hidden w-max rounded bg-slate-800 px-2 py-1 text-[10px] text-white shadow-sm group-hover/tooltip:block dark:bg-white dark:text-slate-900">
                                                {{ step.label }}
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    Status: <span class="font-medium capitalize text-slate-700 dark:text-slate-300">{{ project.status?.replace('_', ' ') }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 transition-opacity group-hover:opacity-100">
                                    <Link :href="`/it-initiatives/${project.id}`" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        View
                                    </Link>
                                    <Link :href="`/it-initiatives/${project.id}/edit`" class="text-slate-400 hover:text-indigo-600 dark:text-slate-500 dark:hover:text-indigo-400">
                                        Edit
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="itInitiatives.data.length === 0"
                class="mt-6 rounded-xl border border-slate-200 bg-white py-12 text-center dark:border-white/5 dark:bg-[#1a1a1a]"
            >
                <p class="text-slate-500 dark:text-slate-400">No IT initiatives found.</p>
                <Link href="/it-initiatives/create" class="mt-2 inline-block font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    Create your first IT initiative
                </Link>
            </div>

            <div v-if="itInitiatives.links && itInitiatives.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in itInitiatives.links"
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
    itInitiatives: Object,
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

const stepCircleClass = (status, stepIndex) => {
    const current = statusStepIndex(status);
    const normalized = String(status || '').toLowerCase();

    if (stepIndex < current || (normalized === 'completed' && stepIndex === current)) {
        return 'border-emerald-500 bg-emerald-500';
    }

    if (stepIndex === current) {
        return 'border-amber-500 bg-amber-500 ring-2 ring-amber-200 dark:ring-amber-500/30';
    }

    return 'border-slate-300 bg-slate-100 dark:border-white/20 dark:bg-white/5';
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
    router.get('/it-initiatives', filters.value, { preserveState: true, replace: true });
};
</script>
