<template>
    <UserLayout title="Resource Management">
        <div class="animate-fade-in-up space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            Resource Management
                        </h1>
                        <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan ringkasan resource project charter pada Program Implementation, dengan fokus pada data budget dan key personnel.
                        </p>
                    </div>

                    <div class="inline-flex items-center rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                        {{ totalProjectsLabel }}
                    </div>
                </div>
            </section>

            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                                Resource Management Table
                            </h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Ringkasan resource dari project charter yang sudah memiliki relasi project yang valid.
                            </p>
                        </div>

                        <div class="inline-flex items-center rounded-full border border-[#1C75BC]/20 bg-[#1C75BC]/8 px-3 py-1 text-xs font-semibold text-[#0B2A8A] dark:border-[#7FC0F2]/20 dark:bg-[#7FC0F2]/10 dark:text-[#A9D7F7]">
                            {{ totalRowsLabel }}
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="w-[28%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Project Name
                                </th>
                                <th class="w-[24%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Budget
                                </th>
                                <th class="w-[48%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                    Key Personnel
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="3"
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
                                <td class="px-4 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ item.project_name || '-' }}
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
import { computed } from 'vue';
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
});

const rows = computed(() => (Array.isArray(props.resourceProjects) ? props.resourceProjects : []));

const totalProjectsLabel = computed(() => {
    const total = Number(props.resourceSummary?.total_projects ?? rows.value.length);

    return `${total} Project Charter Resource${total === 1 ? '' : 's'}`;
});

const totalRowsLabel = computed(() => {
    const total = rows.value.length;

    return `${total} Project${total === 1 ? '' : 's'}`;
});
</script>
