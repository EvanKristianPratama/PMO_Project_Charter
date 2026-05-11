<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { useDarkMode } from '@/Composables/useDarkMode';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import BreadcrumbLeft from '@/Components/BreadcrumbLeft.vue';
import BreadcrumbRight from '@/Components/BreadcrumbRight.vue';
import { useNavigation } from '@/Composables/useNavigation';
import {
    Bars3Icon,
    XMarkIcon,
    SunIcon,
    MoonIcon,
    ChevronDownIcon,
    TableCellsIcon,
    ArrowRightOnRectangleIcon,
    
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const { isDark, toggleDarkMode } = useDarkMode();
const route = useRouteHelper();
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
    router.post(route('logout'));
};

// Premium Splash Loader States
const showLoader = ref(false);
const progress = ref(0);

const loadingText = computed(() => {
    if (progress.value < 25) return 'Inisialisasi sistem & database SQLite...';
    if (progress.value < 55) return 'Sinkronisasi relasi program & inisiatif strategis...';
    if (progress.value < 85) return 'Memuat modul Vue & antarmuka sistem...';
    return 'Hampir selesai, selamat datang!';
});

onMounted(() => {
    // TIPS TESTING: Diubah ke false agar loading selalu muncul setiap di-refresh (sangat cocok untuk testing tampilan).
    // Ubah kembali ke sessionStorage.getItem('app_loaded') jika ingin hanya muncul sekali per pembukaan aplikasi.
    const hasLoaded = sessionStorage.getItem('app_loaded'); 
    if (!hasLoaded) {
        showLoader.value = true;
        const interval = setInterval(() => {
            if (progress.value < 100) {
                let increment = Math.floor(Math.random() * 6) + 3;
                if (progress.value + increment > 100) {
                    progress.value = 100;
                } else {
                    progress.value += increment;
                }
            } else {
                clearInterval(interval);
                sessionStorage.setItem('app_loaded', 'true');
                setTimeout(() => {
                    showLoader.value = false;
                }, 400);
            }
        }, 50);
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100">
        <!-- Premium 0-100% Loading Screen Overlay -->
        <transition name="fade">
            <div v-if="showLoader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950/95 text-white backdrop-blur-xl transition-all duration-500 ease-in-out">
                <div class="flex flex-col items-center max-w-md px-6 text-center">
                    <!-- Glowing Logo Container -->
                    <div class="relative mb-8 h-20 w-20 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 shadow-[0_0_50px_rgba(59,130,246,0.15)] animate-pulse">
                        <img src="/logo.png" alt="Logo" class="h-10 w-auto" />
                    </div>

                    <!-- App Title -->
                    <h2 class="text-lg font-bold tracking-tight text-white mb-1">IT Strategic Planning System</h2>
                    <p class="text-[11px] text-slate-400 mb-8">Review ITSP Pertamina 2025-2029 Collaboration System</p>

                    <!-- Large Glowing Percentage -->
                    <div class="relative mb-4">
                        <span class="text-5xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 drop-shadow-[0_0_15px_rgba(99,102,241,0.4)]">
                            {{ progress }}%
                        </span>
                    </div>

                    <!-- Glowing Horizontal Progress Bar -->
                    <div class="w-64 h-1.5 bg-white/10 rounded-full overflow-hidden mb-4 relative">
                        <div 
                            class="h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full transition-all duration-100 ease-out shadow-[0_0_12px_rgba(99,102,241,0.6)]"
                            :style="{ width: `${progress}%` }"
                        ></div>
                    </div>

                    <!-- Dynamic Subtle Status Text -->
                    <p class="text-[11px] text-slate-400 font-medium tracking-wide animate-pulse min-h-[16px]">
                        {{ loadingText }}
                    </p>
                </div>
            </div>
        </transition>

        <Head :title="title" />

        <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414] print:hidden">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <Link :href="route('dashboard')" class="group inline-flex items-center gap-3">
                        <img
                            src="/logo.png"
                            alt="Logo"
                            class="h-7 w-auto transition-opacity group-hover:opacity-90"
                        />
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white">IT Strategic Planning System</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Review ITSP Pertamina 2025-2029 Collaboration System</p>
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

                    <Link
                        :href="route('master-data.index')"
                        class="hidden sm:flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1c1c1c] dark:text-slate-300 dark:hover:bg-[#252525]"
                    >
                        <TableCellsIcon class="h-4 w-4" />
                        Master Data
                    </Link>

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
                    <template v-for="item in navItems" :key="`mobile-${item.href}`">
                        <div v-if="item.children && item.children.length > 0">
                            <Link
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
                            <div class="ml-4 space-y-1">
                                <Link
                                    v-for="child in item.children"
                                    :key="`mobile-sub-${child.href}`"
                                    :href="child.href"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                    :class="child.active(currentUrl)
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                        : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300'"
                                    @click="mobileMenuOpen = false"
                                >
                                    <component :is="child.icon" v-if="child.icon" class="h-4 w-4 shrink-0" />
                                    {{ child.label }}
                                </Link>
                            </div>
                        </div>
                        <Link
                            v-else
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
                    </template>

                    <div class="my-2 border-t border-slate-200 dark:border-white/5"></div>

                    <!-- User Info & Logout -->
                    <div class="px-3 py-2">
                        <Link
                            :href="route('master-data.index')"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300"
                            @click="mobileMenuOpen = false"
                        >
                            <TableCellsIcon class="h-5 w-5" />
                            Master Data
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <div class="sticky top-16 z-40 print:hidden">
            <div class="flex flex-col gap-2 border-b border-white/50 bg-white/40 px-4 py-2 backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <!-- Kiri: Program Planning / Program Implementation -->
                <div class="w-full min-w-0 rounded-full border border-white/70 bg-white/60 px-2.5 py-1 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:w-auto">
                    <div class="overflow-x-auto">
                        <div class="min-w-max">
                            <BreadcrumbLeft />
                        </div>
                    </div>
                </div>
                <!-- Kanan: Architecture / Policy -->
                <div class="w-full min-w-0 rounded-full border border-white/70 bg-white/60 px-2.5 py-1 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:w-auto">
                    <div class="overflow-x-auto sm:flex sm:justify-end">
                        <div class="min-w-max">
                            <BreadcrumbRight />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="mx-auto w-full max-w-7xl px-4 pt-5 pb-8 sm:px-6 lg:px-8 print:max-w-none print:px-0 print:py-0">
            <slot />
        </main>

        <footer class="mx-auto w-full max-w-7xl px-4 pb-8 sm:px-6 lg:px-8 print:hidden">
            <div class="border-t border-slate-200/70 pt-5 dark:border-white/5">
                <p class="text-center text-xs text-slate-400 dark:text-slate-500">IT Strategic Planning System</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.fade-leave-active {
  transition: opacity 0.4s ease-out;
}
.fade-leave-to {
  opacity: 0;
}
</style>
