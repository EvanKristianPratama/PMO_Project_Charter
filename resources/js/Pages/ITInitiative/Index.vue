<template>
    <UserLayout title="IT Initiatives">
        <div class="animate-fade-in">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">IT Initiatives</h3>
                </div>
            </div>

            <div class="mb-4 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-2.5 dark:border-white/5 dark:bg-[#1a1a1a] sm:flex-row">
                <div class="relative flex-1">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by name or code..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-1.5 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:placeholder-slate-500"
                        @input="debouncedSearch"
                    />
                    <svg class="absolute left-3 top-2 h-4 w-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select
                    v-model="filters.status"
                    class="rounded-lg border-slate-300 bg-white py-1.5 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                    @change="applyFilters"
                >
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
                <select
                    v-model="filters.month"
                    class="rounded-lg border-slate-300 bg-white py-1.5 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                    @change="applyFilters"
                >
                    <option value="">All Months</option>
                    <option v-for="month in months" :key="month.value" :value="month.value">
                        {{ month.label }}
                    </option>
                </select>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/5">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">ID Project</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                        <tr v-for="(project, index) in itInitiatives" :key="project.id" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="whitespace-nowrap px-6 py-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ index + 1 }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs font-medium text-slate-600 dark:text-slate-400">
                                {{ project.id }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ project.charter?.category || 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900 dark:text-white">{{ project.name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400': project.status === 'completed',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400': project.status === 'active',
                                        'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-400': project.status === 'draft',
                                    }"
                                >
                                    {{ project.status?.replace('_', ' ') }}
                                </span>
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
                v-if="itInitiatives.length === 0"
                class="mt-6 rounded-xl border border-slate-200 bg-white py-12 text-center dark:border-white/5 dark:bg-[#1a1a1a]"
            >
                <p class="text-slate-500 dark:text-slate-400">No IT initiatives found.</p>
                <Link href="/it-initiatives/create" class="mt-2 inline-block font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    Create your first IT initiative
                </Link>
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

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    month: props.filters.month || '',
});

const months = [
    { value: '01', label: 'January' },
    { value: '02', label: 'February' },
    { value: '03', label: 'March' },
    { value: '04', label: 'April' },
    { value: '05', label: 'May' },
    { value: '06', label: 'June' },
    { value: '07', label: 'July' },
    { value: '08', label: 'August' },
    { value: '09', label: 'September' },
    { value: '10', label: 'October' },
    { value: '11', label: 'November' },
    { value: '12', label: 'December' },
];

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    router.get('/it-initiatives', filters.value, { preserveState: true, replace: true });
};
</script>
