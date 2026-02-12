<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Mobile Overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 transform transition-transform duration-200 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <div>
                    <span class="text-white font-semibold text-sm">PMO Portal</span>
                    <span class="block text-slate-500 text-[10px] tracking-wider uppercase">Admin Panel</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1">
                <SidebarLink
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :active="item.active"
                    :icon="item.icon"
                >
                    {{ item.name }}
                </SidebarLink>
            </nav>

            <!-- Back to Portal -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-800">
                <Link href="/dashboard" class="flex items-center gap-2 text-slate-400 hover:text-white text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke Portal
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Header -->
            <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 sm:px-6">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Page title -->
                <h1 class="text-lg font-semibold text-slate-900 hidden lg:block">
                    <slot name="title" />
                </h1>

                <div class="flex-1 lg:hidden"></div>

                <!-- User menu -->
                <div v-if="auth?.user" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ auth.user.initials }}
                    </div>
                    <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth.user.name }}</span>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';

const sidebarOpen = ref(false);
const page = usePage();
const { auth } = page.props;

const currentUrl = computed(() => page.url);

const navigation = computed(() => [
    {
        name: 'Dashboard',
        href: '/admin/dashboard',
        icon: 'dashboard',
        active: currentUrl.value.startsWith('/admin/dashboard'),
    },
    {
        name: 'Projects',
        href: '/projects',
        icon: 'folder', // We need to update SidebarLink to support this icon or reuse one
        active: currentUrl.value.startsWith('/projects'),
    },
    {
        name: 'User Management',
        href: '/admin/users',
        icon: 'users',
        active: currentUrl.value.startsWith('/admin/users'),
    },
]);
</script>
