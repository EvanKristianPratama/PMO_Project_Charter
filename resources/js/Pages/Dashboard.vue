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
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ item.note }}</p>
                    
                    <div v-if="item.key === 'total' || item.key === 'digital'" class="mt-4">
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
</script>
