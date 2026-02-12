<template>
    <div class="min-h-screen bg-slate-50 flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-slate-900">PMO Portal</span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Admin link -->
                    <Link
                        v-if="isAdmin"
                        href="/admin/dashboard"
                        class="text-sm text-slate-500 hover:text-indigo-600 transition-colors"
                    >
                        Admin Panel
                    </Link>

                    <!-- User -->
                    <div v-if="auth?.user" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                            {{ auth.user.initials }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth.user.name }}</span>
                    </div>

                    <!-- Logout -->
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-sm text-slate-400 hover:text-red-500 transition-colors"
                    >
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
            <div class="animate-fade-in">
                <h1 class="text-2xl font-bold text-slate-900 mb-1">Dashboard</h1>
                <p class="text-slate-500 mb-8">Selamat datang, {{ auth?.user?.name }}!</p>

                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">PMO Portal Siap Digunakan</h2>
                    <p class="text-slate-500 text-sm max-w-md mx-auto">
                        Modul-modul seperti Project Charter, Program Management, Timeline, dan Goals akan segera tersedia di sini.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;

const isAdmin = computed(() => auth?.user?.roles?.includes('Admin'));
</script>
