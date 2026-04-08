<template>
    <UserLayout title="Resource Management">
        <div class="animate-fade-in-up space-y-6">
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="w-full sm:w-[180px]">
                                <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Filter Type
                                </label>
                                <select
                                    v-model="selectedType"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                                >
                                    <option value="all">Semua Type</option>
                                    <option
                                        v-for="option in typeOptions"
                                        :key="`type-filter-${option.value}`"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="w-full sm:w-[180px]">
                                <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Filter Status
                                </label>
                                <select
                                    v-model="selectedStatus"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                                >
                                    <option value="all">Semua Status</option>
                                    <option
                                        v-for="option in statusOptions"
                                        :key="`status-filter-${option.value}`"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <button
                                v-if="hasActiveFilters"
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5"
                                @click="resetFilters"
                            >
                                Reset
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <div class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                                {{ totalProjectsLabel }}
                            </div>
                            <div class="inline-flex items-center rounded-full border border-[#1C75BC]/20 bg-[#1C75BC]/8 px-3 py-1 text-xs font-semibold text-[#0B2A8A] dark:border-[#7FC0F2]/20 dark:bg-[#7FC0F2]/10 dark:text-[#A9D7F7]">
                                {{ totalRowsLabel }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="w-[16%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Code
                                </th>
                                <th class="w-[24%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Project Name
                                </th>
                                <th class="w-[16%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Status
                                </th>
                                <th class="w-[18%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Budget
                                </th>
                                <th class="w-[26%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Key Personnel
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Belum ada data project charter yang dapat ditampilkan.
                                </td>
                            </tr>

                            <tr
                                v-for="item in rows"
                                :key="`resource-project-${item.id}`"
                                class="align-top"
                            >
                                <td class="px-4 py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ item.project_code || '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ item.project_name || '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ item.status || '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    <span class="whitespace-pre-line break-words">
                                        {{ item.budget || '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    <span class="whitespace-pre-line break-words">
                                        {{ item.key_personnel || '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    resourceProjects: {
        type: Array,
        default: () => [],
    },
    resourceSummary: {
        type: Object,
        default: () => ({
            total_projects: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({
            type: '2',
            status: 'all',
        }),
    },
    filterOptions: {
        type: Object,
        default: () => ({
            types: [],
            statuses: [],
        }),
    },
});

const route = useRouteHelper();
const rows = computed(() => (Array.isArray(props.resourceProjects) ? props.resourceProjects : []));
const typeOptions = computed(() => (Array.isArray(props.filterOptions?.types) ? props.filterOptions.types : []));
const statusOptions = computed(() => (Array.isArray(props.filterOptions?.statuses) ? props.filterOptions.statuses : []));
const selectedType = ref(String(props.filters?.type ?? '2'));
const selectedStatus = ref(String(props.filters?.status ?? 'all'));

const totalProjectsLabel = computed(() => {
    const total = Number(props.resourceSummary?.total_projects ?? rows.value.length);

    return `${total} Project${total === 1 ? '' : 's'}`;
});

const totalRowsLabel = computed(() => {
    const total = Number(props.resourceSummary?.total_charters ?? rows.value.length);

    return `${total} Project Charter${total === 1 ? '' : 's'}`;
});

const hasActiveFilters = computed(() => {
    return selectedType.value !== '2' || selectedStatus.value !== 'all';
});

const applyFilters = () => {
    router.get(
        route('program-implementation.resources-management.index'),
        {
            type: selectedType.value !== 'all' ? selectedType.value : 'all',
            status: selectedStatus.value !== 'all' ? selectedStatus.value : 'all',
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const resetFilters = () => {
    selectedType.value = '2';
    selectedStatus.value = 'all';
};

watch([selectedType, selectedStatus], () => {
    applyFilters();
});
</script>
