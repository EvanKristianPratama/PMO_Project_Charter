<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Program Planing Summary</h1>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="item in metricCards"
                    :key="item.key"
                    class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">{{ item.label }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ item.value }}</p>
                    <p class="mt-2 flex-1 text-xs text-slate-500 dark:text-slate-400">{{ item.note }}</p>

                    <div class="mt-4">
                        <Link
                            :href="item.createHref"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-transparent bg-sky-400 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-white transition hover:bg-sky-500 focus:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 active:bg-sky-600"
                        >
                            New Scope Charter
                        </Link>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717] xl:col-span-3">
                    <div class="space-y-8">
                        <!-- Digital Initiatives Flow -->
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Scope Charter Digital Initiative Status</h2>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ statusFlowLegend }}</span>
                            </div>

                            <div class="mt-5">
                                <div
                                    class="grid"
                                    :style="{ gridTemplateColumns: `repeat(${Math.max(digitalStatusFlow.length, 1)}, minmax(0, 1fr))` }"
                                >
                                    <div
                                        v-for="(step, index) in digitalStatusFlow"
                                        :key="step.key"
                                        class="relative flex justify-center"
                                    >
                                        <span
                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold"
                                            :class="step.circleClass"
                                        >
                                            {{ step.count }}
                                        </span>
                                        <span
                                            v-if="index < digitalStatusFlow.length - 1"
                                            class="absolute left-1/2 top-1/2 ml-[1.2rem] h-[3px] w-[calc(100%_-_2.4rem)] -translate-y-1/2 rounded-full"
                                            :class="step.lineClass"
                                        ></span>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 grid gap-2 text-center"
                                    :style="{ gridTemplateColumns: `repeat(${Math.max(digitalStatusFlow.length, 1)}, minmax(0, 1fr))` }"
                                >
                                    <div v-for="step in digitalStatusFlow" :key="`label-digital-${step.key}`">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IT Initiatives Flow -->
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Scope Charter IT Initiative Status</h2>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ statusFlowLegend }}</span>
                            </div>

                            <div class="mt-5">
                                <div
                                    class="grid"
                                    :style="{ gridTemplateColumns: `repeat(${Math.max(itStatusFlow.length, 1)}, minmax(0, 1fr))` }"
                                >
                                    <div
                                        v-for="(step, index) in itStatusFlow"
                                        :key="step.key"
                                        class="relative flex justify-center"
                                    >
                                        <span
                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold"
                                            :class="step.circleClass"
                                        >
                                            {{ step.count }}
                                        </span>
                                        <span
                                            v-if="index < itStatusFlow.length - 1"
                                            class="absolute left-1/2 top-1/2 ml-[1.2rem] h-[3px] w-[calc(100%_-_2.4rem)] -translate-y-1/2 rounded-full"
                                            :class="step.lineClass"
                                        ></span>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 grid gap-2 text-center"
                                    :style="{ gridTemplateColumns: `repeat(${Math.max(itStatusFlow.length, 1)}, minmax(0, 1fr))` }"
                                >
                                    <div v-for="step in itStatusFlow" :key="`label-it-${step.key}`">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Digital</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="row in statusRows" :key="row.key">
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(row.id)">
                                        {{ row.label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.digital }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.it }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ row.total }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Jumlah</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.digital }}</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.it }}</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-slate-900 dark:text-white">{{ statusTotals.total }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Digital Initiatives (Belum {{ completedStatusLabel }})</h2>
                        </div>
                        <Link
                            :href="`/digital-initiatives?status=${completedStatusId}`"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Lihat {{ completedStatusLabel }}
                        </Link>
                    </div>

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
                                <tr v-for="item in openDigitalInitiatives" :key="`digital-open-${item.id}`">
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
                                            <Link
                                                :href="`/digital-initiatives/${item.id}/edit`"
                                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-amber-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-amber-400"
                                                title="Edit"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="openDigitalInitiatives.length === 0">
                                    <td colspan="11" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                        Semua digital initiatives sudah {{ completedStatusLabel.toLowerCase() }}.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">IT Initiatives (Belum {{ completedStatusLabel }})</h2>
                        </div>
                        <Link
                            :href="`/it-initiatives?status=${completedStatusId}`"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Lihat {{ completedStatusLabel }}
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Initiative</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                <tr v-for="item in openItInitiatives" :key="`it-open-${item.id}`">
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ item.code || '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            <p class="truncate font-medium text-slate-800 dark:text-slate-100">{{ item.name || '-' }}</p>
                                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ item.charter?.category || 'Uncategorized' }}</p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(item.status)">
                                            {{ statusLabelFromOptions(item.status, statusOptions) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link
                                                :href="`/it-initiatives/${item.id}`"
                                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-indigo-400"
                                                title="View"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="`/it-initiatives/${item.id}/edit`"
                                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-amber-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-amber-400"
                                                title="Edit"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="openItInitiatives.length === 0">
                                    <td colspan="4" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                        Semua IT initiatives sudah {{ completedStatusLabel.toLowerCase() }}.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { statusBadgeClassById, statusFlowClassByIndex, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const page = usePage();
const auth = computed(() => page.props.auth || {});

const props = defineProps({
    overview: {
        type: Object,
        default: () => ({
            total_projects: 0,
            total_digital_initiatives: 0,
            status_options: [],
            status_counts: {},
            digital_status_counts: {},
        }),
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
    openDigitalInitiatives: {
        type: Array,
        default: () => [],
    },
    openItInitiatives: {
        type: Array,
        default: () => [],
    },
});

const fallbackStatusOptions = [
    { id: 1, name: 'drafting', label: 'Drafting' },
    { id: 2, name: 'propose', label: 'Propose' },
    { id: 3, name: 'review', label: 'Review' },
    { id: 4, name: 'approve', label: 'Approve' },
    { id: 5, name: 'baseline', label: 'Baseline' },
];

const statusOptions = computed(() => {
    return Array.isArray(props.overview?.status_options) && props.overview.status_options.length > 0
        ? props.overview.status_options
        : fallbackStatusOptions;
});

const completedStatusId = computed(() => Number(props.completedStatusId || 5));

const completedStatusLabel = computed(() => {
    return statusLabelFromOptions(completedStatusId.value, statusOptions.value);
});

const statusFlowLegend = computed(() => {
    return statusOptions.value
        .map((status) => statusLabelFromOptions(status.id, statusOptions.value))
        .join(' → ');
});

const statusRows = computed(() => {
    return statusOptions.value.map((status) => {
        const key = String(status.id);
        const itCount = Number(props.overview?.status_counts?.[key] ?? 0);
        const digitalCount = Number(props.overview?.digital_status_counts?.[key] ?? 0);

        return {
            key,
            id: Number(status.id),
            label: statusLabelFromOptions(status.id, statusOptions.value),
            it: itCount,
            digital: digitalCount,
            total: itCount + digitalCount,
        };
    });
});

const statusTotals = computed(() => {
    return statusRows.value.reduce(
        (accumulator, row) => ({
            digital: accumulator.digital + Number(row.digital ?? 0),
            it: accumulator.it + Number(row.it ?? 0),
            total: accumulator.total + Number(row.total ?? 0),
        }),
        { digital: 0, it: 0, total: 0 }
    );
});

const metricCards = computed(() => [
    {
        key: 'digital',
        label: 'Total Usulan Digital Initiatives',
        value: props.overview.total_digital_initiatives,
        createHref: '/digital-initiatives/create',
    },
    {   
        key: 'it',
        label: 'Total Usulan IT Initiatives',
        value: props.overview.total_projects,
        createHref: '/it-initiatives/create',
    },

]);

const mapFlowData = (counts = {}) => {
    return statusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = String(status.id);

        return {
            key,
            label: statusLabelFromOptions(status.id, statusOptions.value),
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
};

const itStatusFlow = computed(() => mapFlowData(props.overview?.status_counts || {}));
const digitalStatusFlow = computed(() => mapFlowData(props.overview?.digital_status_counts || {}));

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
