<template>
    <UserLayout title="IT Initiatives">
        <div class="animate-fade-in">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">IT Initiatives</h3>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Completed IT initiative entries</p>
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
                    disabled
                    class="rounded-lg border-slate-300 bg-white py-1.5 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                >
                    <option :value="completedStatusId">{{ completedStatusLabel }}</option>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                        <tr v-for="(project, index) in itInitiatives" :key="project.id" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="whitespace-nowrap px-6 py-4 text-xs font-medium text-slate-600 dark:text-slate-400">
                                {{ project.code }}
                            </td>
                            <td 
                                v-if="shouldShowCategory(index)"
                                :rowspan="getCategoryRowspan(index)"
                                class="whitespace-nowrap px-6 py-4 align-top border-r border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]"
                            >
                                <span class="inline-flex rounded-full bg-blue-100 px-2 text-[10px] font-semibold leading-5 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ project.charter?.category || 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ project.name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium capitalize"
                                    :class="statusBadgeClassById(project.status)"
                                >
                                    {{ statusLabelFromOptions(project.status, statusOptions) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 transition-opacity group-hover:opacity-100">
                                    <Link
                                        :href="`/it-initiatives/${project.id}`"
                                        class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-indigo-400"
                                        title="View"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
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
                <p class="mt-2 text-sm text-slate-400 dark:text-slate-500">Belum ada IT initiative dengan status {{ completedStatusLabel.toLowerCase() }}.</p>
            </div>


        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    itInitiatives: Object,
    filters: Object,
    statusOptions: {
        type: Array,
        default: () => [],
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
});

const statusOptions = computed(() => {
    if (props.statusOptions.length > 0) {
        return props.statusOptions;
    }

    return [
        { id: 1, name: 'drafting', label: 'Drafting' },
        { id: 2, name: 'propose', label: 'Propose' },
        { id: 3, name: 'review', label: 'Review' },
        { id: 4, name: 'approve', label: 'Approve' },
        { id: 5, name: 'baseline', label: 'Baseline' },
    ];
});

const completedStatusId = computed(() => {
    return Number(props.completedStatusId || 5);
});

const completedStatusLabel = computed(() => {
    return statusLabelFromOptions(completedStatusId.value, statusOptions.value);
});

const filters = ref({
    search: props.filters.search || '',
    status: completedStatusId.value,
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

const shouldShowCategory = (index) => {
    if (index === 0) return true;
    const current = props.itInitiatives[index].charter?.category || 'Uncategorized';
    const previous = props.itInitiatives[index - 1].charter?.category || 'Uncategorized';
    return current !== previous;
};

const getCategoryRowspan = (index) => {
    let count = 1;
    const current = props.itInitiatives[index].charter?.category || 'Uncategorized';
    for (let i = index + 1; i < props.itInitiatives.length; i++) {
        if ((props.itInitiatives[i].charter?.category || 'Uncategorized') === current) {
            count++;
        } else {
            break;
        }
    }
    return count;
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
