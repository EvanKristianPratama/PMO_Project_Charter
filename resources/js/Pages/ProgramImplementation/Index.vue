<template>
    <UserLayout title="Program Implementation">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Program Implementation Summary</h1>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article
                    v-for="item in projectCharterActions"
                    :key="item.key"
                    class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">{{ item.label }}</p>
                    <p class="mt-2 flex-1 text-xs text-slate-500 dark:text-slate-400">{{ item.note }}</p>

                    <div class="mt-4">
                        <Link
                            :href="item.href"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-transparent bg-sky-400 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-white transition hover:bg-sky-500 focus:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 active:bg-sky-600"
                        >
                            {{ item.buttonLabel }}
                        </Link>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-5">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Project Charter Digital Initiative Status</h2>
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

                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Project Charter IT Initiative Status</h2>
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
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Program Implementation Table</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">BUs</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Digital Initiative Status Updated</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Implementasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Belum ada data.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { statusFlowClassByIndex, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    projectCharterOverview: {
        type: Object,
        default: () => ({
            status_options: [],
            digital_status_counts: {},
            it_status_counts: {},
        }),
    },
});

const fallbackStatusOptions = [
    { id: 1, name: 'drafting', label: 'Drafting' },
    { id: 2, name: 'propose', label: 'Propose' },
    { id: 3, name: 'review', label: 'Review' },
    { id: 4, name: 'approve', label: 'Approve' },
    { id: 5, name: 'baseline', label: 'Baseline' },
];

const desiredStatusOrder = ['drafting', 'propose', 'baseline', 'review', 'approve'];

const statusOptions = computed(() => {
    const sourceOptions = Array.isArray(props.projectCharterOverview?.status_options) && props.projectCharterOverview.status_options.length > 0
        ? props.projectCharterOverview.status_options
        : fallbackStatusOptions;

    return sourceOptions
        .map((status) => ({
            ...status,
            label: status.label || statusLabelFromOptions(status.id, sourceOptions),
        }))
        .sort((a, b) => {
            const aIndex = desiredStatusOrder.indexOf(String(a?.name ?? '').toLowerCase());
            const bIndex = desiredStatusOrder.indexOf(String(b?.name ?? '').toLowerCase());
            const safeA = aIndex === -1 ? Number.MAX_SAFE_INTEGER : aIndex;
            const safeB = bIndex === -1 ? Number.MAX_SAFE_INTEGER : bIndex;

            return safeA - safeB;
        });
});

const statusFlowLegend = computed(() => {
    return statusOptions.value
        .map((status) => status.label)
        .join(' → ');
});

const digitalStatusFlow = computed(() => {
    const counts = props.projectCharterOverview?.digital_status_counts || {};

    return statusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = String(status.id);

        return {
            key,
            label: status.label,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const itStatusFlow = computed(() => {
    const counts = props.projectCharterOverview?.it_status_counts || {};

    return statusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = String(status.id);

        return {
            key,
            label: status.label,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const projectCharterActions = [
    {
        key: 'digital',
        label: 'Digital Initiative',
        buttonLabel: 'New Project Charter',
        href: '/digital-initiatives/create',
    },
    {
        key: 'it',
        label: 'IT Initiative',
        buttonLabel: 'New Project Charter',
        href: '/it-initiatives/create',
    },
];
</script>
