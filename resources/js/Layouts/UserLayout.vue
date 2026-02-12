<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { useDarkMode } from '@/Composables/useDarkMode';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const { isDark, toggleDarkMode } = useDarkMode();
const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});
const currentUrl = computed(() => page.url);
const displayName = computed(() => authUser.value?.name || authUser.value?.email || 'User');

const roleNames = computed(() => {
    const roles = authUser.value?.roles;

    if (Array.isArray(roles)) {
        return roles;
    }

    if (roles && typeof roles === 'object') {
        return Object.values(roles);
    }

    if (typeof roles === 'string') {
        return [roles];
    }

    return [];
});

const primaryRole = computed(() => roleNames.value[0] || authUser.value?.role || 'Viewer');
const isAdmin = computed(() => roleNames.value.includes('Admin') || primaryRole.value === 'Admin');

const getInitials = (name) => {
    if (!name) {
        return 'U';
    }

    return name
        .split(' ')
        .map((word) => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100 transition-colors duration-300">
        <Head :title="title" />

        <nav class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/85 backdrop-blur-xl dark:border-white/5 dark:bg-[#171717]/85">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6">
                    <Link href="/dashboard" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-500/40">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75V5.25Zm4.5 4.5h.008v.008H8.25V9.75Zm0 4.5h.008v.008H8.25v-.008Zm4.5-4.5h3m-3 4.5h3" />
                            </svg>
                        </span>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white">PMO Portal</p>
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">Workspace</p>
                        </div>
                    </Link>

                    <div class="hidden items-center gap-1 md:flex">
                        <Link
                            href="/dashboard"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="currentUrl.startsWith('/dashboard')
                                ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/projects"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="currentUrl.startsWith('/projects')
                                ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'"
                        >
                            Projects
                        </Link>
                        <Link
                            v-if="isAdmin"
                            href="/admin/dashboard"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="currentUrl.startsWith('/admin')
                                ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white'
                                : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'"
                        >
                            Admin
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="toggleDarkMode"
                        class="rounded-xl p-2.5 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5"
                        aria-label="Toggle dark mode"
                    >
                        <svg v-if="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <Menu as="div" class="relative">
                        <MenuButton class="flex items-center gap-3 rounded-xl p-1.5 pr-3 transition-colors hover:bg-slate-100 focus:outline-none dark:hover:bg-white/5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-sm font-medium text-white">
                                {{ getInitials(displayName) }}
                            </div>
                            <span class="hidden text-sm font-medium text-slate-700 dark:text-slate-200 sm:block">{{ displayName }}</span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </MenuButton>

                        <transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="scale-95 opacity-0"
                            enter-to-class="scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="scale-100 opacity-100"
                            leave-to-class="scale-95 opacity-0"
                        >
                            <MenuItems class="absolute right-0 mt-2 w-56 rounded-xl bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-[#1a1a1a] dark:ring-white/10">
                                <div class="border-b border-slate-100 px-4 py-3 dark:border-white/5">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ displayName }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ authUser?.email }}</p>
                                </div>

                                <MenuItem v-if="isAdmin" v-slot="{ active }">
                                    <Link
                                        href="/admin/dashboard"
                                        :class="[active ? 'bg-slate-50 dark:bg-white/5' : '', 'flex w-full items-center gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300']"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75V5.25Zm4.5 4.5h.008v.008H8.25V9.75Zm0 4.5h.008v.008H8.25v-.008Zm4.5-4.5h3m-3 4.5h3" />
                                        </svg>
                                        Admin Dashboard
                                    </Link>
                                </MenuItem>

                                <MenuItem v-slot="{ active }">
                                    <button
                                        type="button"
                                        @click="logout"
                                        :class="[active ? 'bg-slate-50 dark:bg-white/5' : '', 'flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400']"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m12 15 3-3m0 0-3-3m3 3H3" />
                                        </svg>
                                        Keluar
                                    </button>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </main>

        <footer class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <div class="border-t border-slate-200/70 pt-5 dark:border-white/5">
                <p class="text-center text-xs text-slate-400 dark:text-slate-500">PMO Portal Workspace</p>
            </div>
        </footer>
    </div>
</template>
