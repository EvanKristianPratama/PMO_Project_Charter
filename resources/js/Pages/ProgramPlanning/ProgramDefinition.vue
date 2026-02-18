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
</script>
