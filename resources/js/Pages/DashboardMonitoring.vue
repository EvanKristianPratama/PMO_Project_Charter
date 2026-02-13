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
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="statusBadgeClassById(row.id)">
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
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_it_initiatives: 0,
            total_digital_initiatives: 0,
            total_all_initiatives: 0,
            status_options: [],
            it_status_counts: {},
            digital_status_counts: {},
            combined_status_counts: {},
            status_rows: [],
        }),
    },
});

const fallbackStatusOptions = [
    { id: 1, name: 'propose', label: 'Propose' },
    { id: 2, name: 'review', label: 'Review' },
    { id: 3, name: 'approve', label: 'Approve' },
    { id: 4, name: 'baseline', label: 'Baseline' },
];

const statusOptions = computed(() => {
    return Array.isArray(props.summary?.status_options) && props.summary.status_options.length > 0
        ? props.summary.status_options
        : fallbackStatusOptions;
});

const statusRows = computed(() => {
    if (Array.isArray(props.summary?.status_rows) && props.summary.status_rows.length > 0) {
        return props.summary.status_rows.map((row) => ({
            key: String(row.id),
            id: Number(row.id),
            label: row.label || statusLabelFromOptions(row.id, statusOptions.value),
            it: Number(row.it ?? 0),
            digital: Number(row.digital ?? 0),
            total: Number(row.total ?? 0),
        }));
    }

    return statusOptions.value.map((status) => {
        const key = String(status.id);
        const itCount = Number(props.summary.it_status_counts?.[key] ?? 0);
        const digitalCount = Number(props.summary.digital_status_counts?.[key] ?? 0);

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
</script>
