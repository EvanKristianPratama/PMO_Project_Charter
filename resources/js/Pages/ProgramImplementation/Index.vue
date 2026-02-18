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
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

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
