<template>
    <UserLayout title="Digital Initiatives">
        <div class="animate-fade-in">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Digital Initiatives</h2>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 dark:border-white/5 dark:bg-[#1a1a1a] sm:flex-row">
                <div class="relative flex-1">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by no, use case, or owner..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-10 pr-4 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:placeholder-slate-500"
                        @input="debouncedSearch"
                    />
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select
                    v-model="filters.type"
                    class="rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                    @change="applyFilters"
                >
                    <option value="">All Types</option>
                    <option value="strategic">Strategic</option>
                    <option value="operational">Operational</option>
                    <option value="tactical">Tactical</option>
                </select>
                <select
                    v-model="filters.status"
                    disabled
                    class="rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                >
                    <option :value="completedStatusId">{{ completedStatusLabel }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto overflow-y-visible">
                    <table class="min-w-max w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                <th class="min-w-[280px] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Desc</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rjjp</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Coe</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="sticky right-0 z-10 whitespace-nowrap bg-slate-50 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="item in initiatives" :key="`digital-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02]">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ cellVal(item, 'no') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'type') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'projectOwner', 'project_owner') }}</td>
                                <td class="min-w-[180px] max-w-[320px] px-4 py-3 text-slate-700 dark:text-slate-200">
                                    <span class="whitespace-normal break-words">{{ cellVal(item, 'useCase', 'use_case') }}</span>
                                </td>
                                <td class="min-w-[280px] max-w-[400px] px-4 py-3 text-slate-700 dark:text-slate-200">
                                    <span class="whitespace-normal break-words" :title="cellVal(item, 'desc', 'description')">{{ cellVal(item, 'desc', 'description') }}</span>
                                </td>
                                <td class="max-w-[150px] px-4 py-3 text-slate-700 dark:text-slate-200">
                                    <span class="line-clamp-2" :title="cellVal(item, 'value')">{{ cellVal(item, 'value') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'urgency') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'rjjp') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-slate-200">{{ cellVal(item, 'coe') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(item.status)">
                                        {{ statusLabelFromOptions(item.status, statusOptions) }}
                                    </span>
                                </td>
                                <td class="sticky right-0 z-10 whitespace-nowrap bg-white px-4 py-3 text-right shadow-[-4px_0_8px_rgba(0,0,0,0.05)] dark:bg-[#171717] dark:shadow-[-4px_0_8px_rgba(0,0,0,0.2)]">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="`/digital-initiatives/${item.id}`"
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

                            <tr v-if="initiatives.length === 0">
                                <td colspan="11" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Semua digital initiatives sudah {{ completedStatusLabel.toLowerCase() }}.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="initiatives.length === 0"
                class="mt-6 rounded-xl border border-slate-200 bg-white py-12 text-center dark:border-white/5 dark:bg-[#1a1a1a]"
            >
                <p class="text-slate-500 dark:text-slate-400">No digital initiatives found.</p>
                <p class="mt-2 text-sm text-slate-400 dark:text-slate-500">Belum ada digital initiative dengan status {{ completedStatusLabel.toLowerCase() }}.</p>
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
    initiatives: Object,
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
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    status: completedStatusId.value,
});

const typeBadgeClass = (type) => {
    const t = String(type || '').toLowerCase();
    if (t === 'strategic') return 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400';
    if (t === 'operational') return 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400';
    if (t === 'tactical') return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    router.get('/digital-initiatives', filters.value, { preserveState: true, replace: true });
};

/** Ambil nilai dari item, coba beberapa key (camelCase / snake_case). */
function cellVal(item, ...keys) {
    if (!item || typeof item !== 'object') return '-';
    for (const key of keys) {
        const v = item[key];
        if (v !== undefined && v !== null && String(v).trim() !== '') return v;
    }
    return '-';
}
</script>
