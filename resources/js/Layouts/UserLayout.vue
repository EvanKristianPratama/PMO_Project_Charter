<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { useDarkMode } from '@/Composables/useDarkMode';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useNavigation } from '@/Composables/useNavigation';
import {
    Bars3Icon,
    XMarkIcon,
    SunIcon,
    MoonIcon,
    ChevronDownIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const { isDark, toggleDarkMode } = useDarkMode();
const page = usePage();
const mobileMenuOpen = ref(false);
const authUser = computed(() => page.props.auth?.user || {});
const currentUrl = computed(() => page.url || '');
const displayName = computed(() => authUser.value?.name || authUser.value?.email || 'User');
const userEmail = computed(() => authUser.value?.email || '-');
const { navItems } = useNavigation();

const getInitials = (name) => {
    if (!name) return 'U';

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
    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100">
        <Head :title="title" />

        <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414] print:hidden">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <Link href="/dashboard" class="group inline-flex items-center gap-3">
                        <img
                            src="/logo-divusi.png"
                            alt="Divusi Logo"
                            class="h-9 w-auto transition-opacity group-hover:opacity-90"
                        />
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white">IT Strategic Planning System</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Divusi Workspace</p>
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

                    <Menu as="div" class="relative hidden sm:block">
                        <MenuButton
                            class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1c1c1c] dark:hover:bg-[#252525]"
                        >
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-indigo-600 text-xs font-semibold text-white">
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

                    <!-- Mobile Menu Button -->
                    <button
                        type="button"
                        class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10 md:hidden"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <XMarkIcon v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <div v-if="mobileMenuOpen" class="border-t border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414] md:hidden">
                <div class="space-y-1 px-4 py-4">
                    <Link
                        v-for="item in navItems"
                        :key="`mobile-${item.href}`"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="item.active(currentUrl)
                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300'"
                        @click="mobileMenuOpen = false"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        {{ item.label }}
                    </Link>

                    <div class="my-2 border-t border-slate-200 dark:border-white/5"></div>

                    <!-- User Info & Logout -->
                    <div class="px-3 py-2">
                        <div class="mb-3 flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-semibold text-white">
                                {{ getInitials(displayName) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ displayName }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ userEmail }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                            @click="logout"
                        >
                            <ArrowRightOnRectangleIcon class="h-5 w-5" />
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <Breadcrumb />

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 print:max-w-none print:px-0 print:py-0">
            <slot />
        </main>

        <footer class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8 print:hidden">
            <div class="border-t border-slate-200/70 pt-5 dark:border-white/5">
                <p class="text-center text-xs text-slate-400 dark:text-slate-500">IT Strategic Planning System Workspace</p>
            </div>
        </footer>
    </div>
</template>
