<template>
    <UserLayout title="Dashboard">
        <div class="animate-fade-in-up">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400 mb-8">Selamat datang, {{ auth?.user?.name }}!</p>

            <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 p-8 text-center shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-900/20 mb-4">
                    <svg class="w-8 h-8 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">PMO Portal Siap Digunakan</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto">
                    Modul-modul seperti Project Charter, Program Management, Timeline, dan Goals dapat diakses melalui menu navigasi.
                </p>
                
                <div class="mt-6">
                     <Link href="/projects" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Ke Menu Projects
                     </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const { auth } = usePage().props;
</script>
