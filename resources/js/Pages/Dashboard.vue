<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Program Planing Summary</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Halo, {{ auth?.user?.name }}. Ini ringkasan proyek dan roadmap terbaru.
                        </p>
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
                            :href="item.key === 'total' ? '/it-initiatives/create' : '/digital-initiatives/create'"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-transparent bg-sky-400 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-white transition hover:bg-sky-500 focus:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 active:bg-sky-600"
                        >
                            New
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
                                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Status Usulan Digital Initiatives</h2>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Draft → Review → Approved → Complete</span>
                            </div>

                            <div class="mt-5">
                                <div class="grid grid-cols-4">
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
                                            class="absolute left-1/2 top-1/2 ml-[1.2rem] h-[3px] w-[calc(100%-2.4rem)] -translate-y-1/2 rounded-full"
                                            :class="step.lineClass"
                                        ></span>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                                    <div v-for="step in digitalStatusFlow" :key="`label-digital-${step.key}`">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IT Initiatives Flow -->
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Status Usulan IT Initiatives</h2>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Draft → Review → Approved → Complete</span>
                            </div>

                            <div class="mt-5">
                                <div class="grid grid-cols-4">
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
                                            class="absolute left-1/2 top-1/2 ml-[1.2rem] h-[3px] w-[calc(100%-2.4rem)] -translate-y-1/2 rounded-full"
                                            :class="step.lineClass"
                                        ></span>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                                    <div v-for="step in itStatusFlow" :key="`label-it-${step.key}`">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Digital Initiatives (Belum Complete)</h2>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Hanya initiative dengan status selain completed.</p>
                        </div>
                        <Link
                            href="/digital-initiatives?status=completed"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Lihat Completed
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                <tr v-for="item in openDigitalInitiatives" :key="`digital-open-${item.id}`">
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ item.no || '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            <p class="truncate font-medium text-slate-800 dark:text-slate-100">{{ item.useCase || '-' }}</p>
                                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ item.projectOwner || '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClass(item.status)">
                                            {{ statusLabel(item.status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
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
                                    <td colspan="4" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                        Semua digital initiatives sudah completed.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">IT Initiatives (Belum Complete)</h2>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Hanya initiative dengan status selain completed.</p>
                        </div>
                        <Link
                            href="/it-initiatives?status=completed"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Lihat Completed
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
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClass(item.status)">
                                            {{ statusLabel(item.status) }}
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
                                        Semua IT initiatives sudah completed.
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

const page = usePage();
const auth = computed(() => page.props.auth || {});

const props = defineProps({
    overview: {
        type: Object,
        default: () => ({
            total_projects: 0,
            total_digital_initiatives: 0,
            status_counts: {
                draft: 0,
                on_hold: 0,
                active: 0,
                completed: 0,
            },
            digital_status_counts: {
                draft: 0,
                on_hold: 0,
                active: 0,
                completed: 0,
            },
        }),
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

const metricCards = computed(() => [
    {
        key: 'digital',
        label: 'Total Usulan Digital Initiatives',
        value: props.overview.total_digital_initiatives,
        note: 'Semua digital initiatives yang sudah terdaftar di sistem.',
    },
    {   
        key: 'total',
        label: 'Total Usulan IT Initiatives',
        value: props.overview.total_projects,
        note: 'Semua usulan initiatives yang sudah terdaftar di sistem.',
    },

    // {
    //     key: 'roadmap',
    //     label: 'Roadmap Ready',
    //     value: props.overview.roadmap_projects,
    //     note: 'Project yang sudah punya durasi atau milestone bertanggal.',
    // },
    // {
    //     key: 'quarter',
    //     label: 'Milestone Quarter Ini',
    //     value: props.overview.milestones_this_quarter,
    //     note: 'Jumlah milestone yang jatuh pada quarter berjalan.',
    // },
    // {
    //     key: 'completed',
    //     label: 'Completed Projects',
    //     value: props.overview.completed_projects,
    //     note: 'Project dengan status selesai (completed).',
    // },
]);

const itStatusFlow = computed(() => [
    {
        key: 'draft',
        label: 'Usulan',
        count: props.overview.status_counts?.draft ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-emerald-300 dark:bg-emerald-500/40',
    },
    {
        key: 'on_hold',
        label: 'Review',
        count: props.overview.status_counts?.on_hold ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-amber-300 dark:bg-amber-500/40',
    },
    {
        key: 'active',
        label: 'Persetujuan',
        count: props.overview.status_counts?.active ?? 0,
        circleClass: 'border-amber-500 bg-amber-500 text-white',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
    {
        key: 'completed',
        label: 'Selesai as baseline',
        count: props.overview.status_counts?.completed ?? 0,
        circleClass: 'border-slate-300 bg-slate-100 text-slate-600 dark:border-white/20 dark:bg-white/5 dark:text-slate-200',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
]);

const digitalStatusFlow = computed(() => [
    {
        key: 'draft',
        label: 'Usulan',
        count: props.overview.digital_status_counts?.draft ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-emerald-300 dark:bg-emerald-500/40',
    },
    {
        key: 'on_hold',
        label: 'Review',
        count: props.overview.digital_status_counts?.on_hold ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-amber-300 dark:bg-amber-500/40',
    },
    {
        key: 'active',
        label: 'Persetujuan',
        count: props.overview.digital_status_counts?.active ?? 0,
        circleClass: 'border-amber-500 bg-amber-500 text-white',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
    {
        key: 'completed',
        label: 'Selesai as baseline',
        count: props.overview.digital_status_counts?.completed ?? 0,
        circleClass: 'border-slate-300 bg-slate-100 text-slate-600 dark:border-white/20 dark:bg-white/5 dark:text-slate-200',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
]);

const statusLabel = (status) => {
    const normalized = String(status || '').toLowerCase();

    if (!normalized) {
        return 'draft';
    }

    return normalized.replace('_', ' ');
};

const statusBadgeClass = (status) => {
    const normalized = String(status || '').toLowerCase();

    if (normalized === 'active') {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400';
    }

    if (normalized === 'on_hold') {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400';
    }

    if (normalized === 'completed') {
        return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400';
    }

    return 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300';
};
</script>
