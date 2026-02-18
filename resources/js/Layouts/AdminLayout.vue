<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useDarkMode } from '@/Composables/useDarkMode';
import {
    ArrowRightOnRectangleIcon,
    Bars3Icon,
    ChevronDownIcon,
    HomeIcon,
    KeyIcon,
    MoonIcon,
    SunIcon,
    UsersIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Admin',
    },
});

const { isDark, toggleDarkMode } = useDarkMode();
const page = usePage();
const sidebarOpen = ref(false);
const authUser = computed(() => page.props.auth?.user || {});
const currentUrl = computed(() => page.url || '');
const displayName = computed(() => authUser.value?.name || authUser.value?.email || 'Admin');
const userEmail = computed(() => authUser.value?.email || '-');

const navItems = [
    {
        label: 'Dashboard',
        href: '/admin/dashboard',
        icon: HomeIcon,
        active: (url) => url.startsWith('/admin/dashboard'),
    },
    {
        label: 'User Management',
        href: '/admin/users',
        icon: UsersIcon,
        active: (url) => url.startsWith('/admin/users'),
    },
    {
        label: 'Role Management',
        href: '/admin/roles',
        icon: KeyIcon,
        active: (url) => url.startsWith('/admin/roles'),
    },
];

const getInitials = (name) => {
    if (!name) {
        return 'A';
    }

    return name
        .split(' ')
        .map((word) => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 text-slate-900 dark:bg-[#0d1117] dark:text-slate-100">
        <Head :title="title" />

        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414]">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10 md:hidden"
                        @click="sidebarOpen = true"
                    >
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <Link href="/admin/dashboard" class="inline-flex items-center gap-2">
                        <img
                            src="/logo.png"
                            alt="Logo"
                            class="h-8 w-auto"
                        />
                        <div class="hidden sm:block">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">PMO Admin</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ props.title }}</p>
                        </div>
                    </Link>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10"
                        @click="toggleDarkMode"
                    >
                        <SunIcon v-if="isDark" class="h-5 w-5" />
                        <MoonIcon v-else class="h-5 w-5" />
                    </button>

                    <Menu as="div" class="relative">
                        <MenuButton
                            class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1c1c1c] dark:hover:bg-[#252525]"
                        >
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-semibold text-white">
                                {{ getInitials(displayName) }}
                            </span>
                            <div class="hidden max-w-[9rem] text-left sm:block">
                                <p class="truncate text-xs font-semibold text-slate-900 dark:text-white">{{ displayName }}</p>
                            </div>
                            <ChevronDownIcon class="h-4 w-4 text-slate-400" />
                        </MenuButton>

                        <transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="transform scale-95 opacity-0"
                            enter-to-class="transform scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="transform scale-100 opacity-100"
                            leave-to-class="transform scale-95 opacity-0"
                        >
                            <MenuItems
                                class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white shadow-sm focus:outline-none dark:divide-white/10 dark:border-white/10 dark:bg-[#1d1d1d]"
                            >
                                <div class="px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ displayName }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ userEmail }}</p>
                                </div>
                                <div class="p-1">
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            type="button"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors"
                                            :class="active ? 'bg-red-50 dark:bg-red-500/10' : ''"
                                            @click="logout"
                                        >
                                            <ArrowRightOnRectangleIcon class="h-4 w-4" />
                                            Keluar
                                        </button>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>
        </header>

        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/55 md:hidden" @click="closeSidebar"></div>

        <aside
            :class="[
                'fixed bottom-0 left-0 top-16 z-50 flex w-72 flex-col border-r border-slate-200 bg-white transition-transform duration-300 dark:border-white/10 dark:bg-[#141414]',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
            ]"
        >
            <div class="flex h-14 items-center justify-between border-b border-slate-200 px-4 dark:border-white/10 md:hidden">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Menu Admin</p>
                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10"
                    @click="closeSidebar"
                >
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                <Link
                    v-for="item in navItems"
                    :key="`side-${item.href}`"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                    :class="item.active(currentUrl)
                        ? 'bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300'
                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'"
                    @click="closeSidebar"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    <span>{{ item.label }}</span>
                </Link>
            </nav>

        </aside>

        <div class="min-h-[calc(100vh-4rem)] md:ml-72">
            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
</template>
