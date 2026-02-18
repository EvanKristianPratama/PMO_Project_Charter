<template>
    <UserLayout title="Matrix">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Matrix Korelasi</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Atas: Digital Initiative, Samping: IT Initiative.</p>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Tabel Matrix</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="w-48 border border-slate-200 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                                    IT Initiative
                                </th>
                                <th
                                    v-for="column in digitalColumns"
                                    :key="`digital-column-${column}`"
                                    class="w-40 border border-slate-200 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400"
                                >
                                    {{ column }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in itRows" :key="`it-row-${row}`">
                                <td class="border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:text-slate-200">
                                    {{ row }}
                                </td>
                                <td
                                    v-for="column in digitalColumns"
                                    :key="`cell-${row}-${column}`"
                                    class="h-16 border border-slate-200 bg-white dark:border-white/10 dark:bg-[#171717]"
                                ></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

const digitalColumns = [
    'Digital Initiative 1',
    'Digital Initiative 2',
    'Digital Initiative 3',
    'Digital Initiative 4',
    'Digital Initiative 5',
];

const itRows = [
    'IT Initiative 1',
    'IT Initiative 2',
    'IT Initiative 3',
    'IT Initiative 4',
    'IT Initiative 5',
];
</script>
