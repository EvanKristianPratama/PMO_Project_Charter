<template>
    <UserLayout title="Program Definition">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Program Definition</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Halaman awal untuk membuat usulan initiative.
                </p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <!-- Stats row -->
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-white/5 dark:bg-white/5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-500/20">
                            <svg class="h-5 w-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Digital Initiative</p>
                            <p class="mt-0.5 text-2xl font-bold text-slate-900 dark:text-white">{{ totalDigitalInitiatives }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-white/5 dark:bg-white/5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total IT Initiative</p>
                            <p class="mt-0.5 text-2xl font-bold text-slate-900 dark:text-white">{{ totalItInitiatives }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <Link
                        href="/digital-initiatives/create"
                        class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-sky-600"
                    >
                        New Digital Initiative
                    </Link>
                    <Link
                        href="/it-initiatives/create"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-600"
                    >
                        New IT Initiative
                    </Link>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    totalDigitalInitiatives: {
        type: Number,
        default: 0,
    },
    totalItInitiatives: {
        type: Number,
        default: 0,
    },
});
</script>
