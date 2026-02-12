<template>
    <UserLayout title="Dashboard">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Halo, {{ auth?.user?.name }}. Ini ringkasan proyek dan roadmap terbaru.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            href="/projects"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Kelola Projects
                        </Link>
                        <Link
                            href="/roadmap"
                            class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5"
                        >
                            Buka Roadmap
                        </Link>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="item in metricCards"
                    :key="item.key"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">{{ item.label }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ item.value }}</p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ item.note }}</p>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717] xl:col-span-2">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Status Flow Proyek</h2>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Draft → Review → Approved → Complete</span>
                    </div>

                    <div class="mt-5">
                        <div class="grid grid-cols-4">
                            <div
                                v-for="(step, index) in statusFlow"
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
                                    v-if="index < statusFlow.length - 1"
                                    class="absolute left-1/2 top-1/2 ml-[1.2rem] h-[3px] w-[calc(100%-2.4rem)] -translate-y-1/2 rounded-full"
                                    :class="step.lineClass"
                                ></span>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                            <div v-for="step in statusFlow" :key="`label-${step.key}`">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ step.subtitle }}</p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Roadmap Snapshot</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ roadmapSummary }}
                    </p>

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="milestone in nextMilestones"
                            :key="milestone.id"
                            class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5"
                        >
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ milestone.title }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ milestone.project_code }} • {{ milestone.project_name }}</p>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ dateRangeLabel(milestone.start_date, milestone.end_date) }}</p>
                        </div>

                        <div v-if="nextMilestones.length === 0" class="rounded-lg border border-dashed border-slate-300 p-3 text-xs text-slate-500 dark:border-white/15 dark:text-slate-400">
                            Belum ada milestone terjadwal ke depan.
                        </div>
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
            roadmap_projects: 0,
            milestones_this_quarter: 0,
            completed_projects: 0,
            status_counts: {
                draft: 0,
                on_hold: 0,
                active: 0,
                completed: 0,
            },
        }),
    },
    nextMilestones: {
        type: Array,
        default: () => [],
    },
});

const metricCards = computed(() => [
    {
        key: 'total',
        label: 'Total Projects',
        value: props.overview.total_projects,
        note: 'Semua proyek yang sudah terdaftar di sistem.',
    },
    {
        key: 'roadmap',
        label: 'Roadmap Ready',
        value: props.overview.roadmap_projects,
        note: 'Project yang sudah punya durasi atau milestone bertanggal.',
    },
    {
        key: 'quarter',
        label: 'Milestone Quarter Ini',
        value: props.overview.milestones_this_quarter,
        note: 'Jumlah milestone yang jatuh pada quarter berjalan.',
    },
    {
        key: 'completed',
        label: 'Completed Projects',
        value: props.overview.completed_projects,
        note: 'Project dengan status selesai (completed).',
    },
]);

const statusFlow = computed(() => [
    {
        key: 'draft',
        label: 'Diajukan',
        subtitle: 'Draft',
        count: props.overview.status_counts?.draft ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-emerald-300 dark:bg-emerald-500/40',
    },
    {
        key: 'on_hold',
        label: 'Pemeriksaan',
        subtitle: 'On Hold',
        count: props.overview.status_counts?.on_hold ?? 0,
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-amber-300 dark:bg-amber-500/40',
    },
    {
        key: 'active',
        label: 'Disetujui',
        subtitle: 'Active',
        count: props.overview.status_counts?.active ?? 0,
        circleClass: 'border-amber-500 bg-amber-500 text-white',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
    {
        key: 'completed',
        label: 'Selesai',
        subtitle: 'Complete',
        count: props.overview.status_counts?.completed ?? 0,
        circleClass: 'border-slate-300 bg-slate-100 text-slate-600 dark:border-white/20 dark:bg-white/5 dark:text-slate-200',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
]);

const roadmapSummary = computed(() => {
    const roadmap = props.overview.roadmap_projects ?? 0;
    const total = props.overview.total_projects ?? 0;
    const quarter = props.overview.milestones_this_quarter ?? 0;

    return `${roadmap} dari ${total} project sudah roadmap-ready, dengan ${quarter} milestone di quarter ini.`;
});

const dateRangeLabel = (startDate, endDate) => {
    if (startDate && endDate) {
        return `${startDate} → ${endDate}`;
    }

    return startDate || endDate || '-';
};
</script>
