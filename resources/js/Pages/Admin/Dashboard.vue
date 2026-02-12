<template>
    <AdminLayout title="Admin Dashboard">
        <div class="space-y-6 animate-fade-in">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-20 -left-16 h-52 w-52 rounded-full bg-emerald-400/10 blur-2xl"></div>

                <div class="relative">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400">Admin Panel</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Dashboard Overview</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola akses user dan monitoring modul utama PMO dalam satu tampilan.</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in cards"
                    :key="card.title"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">{{ card.title }}</p>
                            <p class="mt-2 text-2xl font-semibold tracking-tight" :class="card.valueClass">{{ card.value }}</p>
                        </div>
                        <span :class="card.iconWrapClass">
                            <svg v-if="card.icon === 'module'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg v-else-if="card.icon === 'project'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44L10.94 4.19a1.5 1.5 0 00-1.06-.44H4.5A2.25 2.25 0 002.25 6v12A2.25 2.25 0 004.5 20.25h15A2.25 2.25 0 0021.75 18V9A2.25 2.25 0 0019.5 6.75h-5.38a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                            <svg v-else-if="card.icon === 'program'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h16.5v16.5H3.75V3.75Zm4.5 4.5h7.5m-7.5 3.75h7.5m-7.5 3.75h4.5" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.182 16.318 3.536-3.536m0 0-3.536-3.536m3.536 3.536H8.25m-4.5 6.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v12a1.5 1.5 0 001.5 1.5Z" />
                            </svg>
                        </span>
                    </div>
                </article>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Quick Access</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    <Link
                        href="/admin/users"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.13a9.38 9.38 0 002.63.37 9.34 9.34 0 004.12-.95 4.13 4.13 0 00-7.53-2.49M15 19.13v-.01c0-1.11-.29-2.16-.79-3.06M15 19.13a12.3 12.3 0 01-6.38 1.87c-2.33 0-4.51-.64-6.37-1.77l-.01-.11a6.38 6.38 0 0111.97-3.06M12 6.38a3.38 3.38 0 11-6.75 0 3.38 3.38 0 016.75 0zm8.25 2.25a2.63 2.63 0 11-5.25 0 2.63 2.63 0 015.25 0z" />
                        </svg>
                        Manage Users
                    </Link>
                    <Link
                        href="/dashboard"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6H6.75A2.25 2.25 0 004.5 8.25v9A2.25 2.25 0 006.75 19.5h9a2.25 2.25 0 002.25-2.25V13.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 11.25 19.5 4.5m0 0H15m4.5 0V9" />
                        </svg>
                        Open User Portal
                    </Link>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const cards = computed(() => [
    {
        title: 'Total Modul',
        value: '5',
        icon: 'module',
        valueClass: 'text-slate-900 dark:text-white',
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-300',
    },
    {
        title: 'Projects',
        value: '—',
        icon: 'project',
        valueClass: 'text-indigo-600 dark:text-indigo-300',
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
    },
    {
        title: 'Programs',
        value: '—',
        icon: 'program',
        valueClass: 'text-emerald-600 dark:text-emerald-300',
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
    },
    {
        title: 'Goals',
        value: '—',
        icon: 'goal',
        valueClass: 'text-amber-600 dark:text-amber-300',
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
    },
]);
</script>
