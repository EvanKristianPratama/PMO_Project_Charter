<template>
    <UserLayout title="Dashboard Monitoring">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Monitoring</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ringkasan total semua initiatives dan statusnya (view only).
                </p>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total IT Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_it_initiatives }}</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total Digital Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_digital_initiatives }}</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total Semua Initiatives</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ summary.total_all_initiatives }}</p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Status Summary</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Digital</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="row in statusRows" :key="row.key">
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="row.badgeClass">
                                        {{ row.label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.it }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">{{ row.digital }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ row.total }}</td>
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
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_it_initiatives: 0,
            total_digital_initiatives: 0,
            total_all_initiatives: 0,
            it_status_counts: {
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
            combined_status_counts: {
                draft: 0,
                on_hold: 0,
                active: 0,
                completed: 0,
            },
        }),
    },
});

const statusRows = computed(() => [
    {
        key: 'draft',
        label: 'draft',
        it: props.summary.it_status_counts?.draft ?? 0,
        digital: props.summary.digital_status_counts?.draft ?? 0,
        total: props.summary.combined_status_counts?.draft ?? 0,
        badgeClass: 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300',
    },
    {
        key: 'on_hold',
        label: 'on hold',
        it: props.summary.it_status_counts?.on_hold ?? 0,
        digital: props.summary.digital_status_counts?.on_hold ?? 0,
        total: props.summary.combined_status_counts?.on_hold ?? 0,
        badgeClass: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
    },
    {
        key: 'active',
        label: 'active',
        it: props.summary.it_status_counts?.active ?? 0,
        digital: props.summary.digital_status_counts?.active ?? 0,
        total: props.summary.combined_status_counts?.active ?? 0,
        badgeClass: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
    },
    {
        key: 'completed',
        label: 'completed',
        it: props.summary.it_status_counts?.completed ?? 0,
        digital: props.summary.digital_status_counts?.completed ?? 0,
        total: props.summary.combined_status_counts?.completed ?? 0,
        badgeClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
    },
]);
</script>
