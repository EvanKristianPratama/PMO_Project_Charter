<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onUnmounted } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { useDarkMode } from "@/Composables/useDarkMode";
import { useRouteHelper } from "@/Composables/useRouteHelper";
import BreadcrumbLeft from "@/Components/BreadcrumbLeft.vue";
import BreadcrumbRight from "@/Components/BreadcrumbRight.vue";
import { useNavigation } from "@/Composables/useNavigation";
import { useModulState } from "@/Composables/useModulState";
import {
    Bars3Icon,
    XMarkIcon,
    SunIcon,
    MoonIcon,
    ChevronDownIcon,
    TableCellsIcon,
    ArrowRightOnRectangleIcon,
    CircleStackIcon,
    CogIcon,
    ShieldCheckIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    title: {
        type: String,
        default: "Dashboard",
    },
});

const { isDark, toggleDarkMode } = useDarkMode();
const { activeModul, setActiveModul } = useModulState();

const isItspsChecked = computed(
    () => activeModul.value === "all" || activeModul.value === "itsps",
);
const isItomChecked = computed(
    () => activeModul.value === "all" || activeModul.value === "itom",
);

const toggleItsps = () => {
    if (activeModul.value === "all") {
        setActiveModul("itom");
    } else if (activeModul.value === "itsps") {
        // Prevent unchecking the last remaining active module
    } else {
        setActiveModul("all");
    }
};

const toggleItom = () => {
    if (activeModul.value === "all") {
        setActiveModul("itsps");
    } else if (activeModul.value === "itom") {
        // Prevent unchecking the last remaining active module
    } else {
        setActiveModul("all");
    }
};
const route = useRouteHelper();
const page = usePage();
const mobileMenuOpen = ref(false);
const authUser = computed(() => page.props.auth?.user || {});
const normalizeUrl = (url) => {
    const value = String(url || "");

    if (!value || value === "#") return value;

    try {
        const parsed = new URL(value, "http://localhost");
        return `${parsed.pathname}${parsed.search}`;
    } catch (error) {
        return value.startsWith("/") ? value : `/${value}`;
    }
};

const currentUrl = computed(() => normalizeUrl(page.url));
const displayName = computed(
    () => authUser.value?.name || authUser.value?.email || "User",
);

const isMenuItemActive = (item, url = currentUrl.value) => {
    const normalizedUrl = normalizeUrl(url);

    if (typeof item.active === "function") {
        return item.active(normalizedUrl);
    }

    const href = normalizeUrl(item.href);
    return (
        !!href &&
        href !== "#" &&
        (normalizedUrl === href ||
            normalizedUrl.startsWith(`${href}?`) ||
            normalizedUrl.startsWith(`${href}/`))
    );
};

const itspsLabels = [
    "Strategic House",
    "Program Planning",
    "Program Evaluation",
    "Program Implementation",
];
const itomLabels = [
    "Business Process",
    "Organization",
    "Operating Model",
    "Service Portofolio",
    "Regulation",
    "RACI Analysis",
    "Master Data",
    "Sinkronisasi Data",
];

const isItspsSubmenuActive = computed(() => {
    return navItems.value.some(
        (item) => itspsLabels.includes(item.label) && isMenuItemActive(item),
    );
});

const isItomSubmenuActive = computed(() => {
    return navItems.value.some(
        (item) => itomLabels.includes(item.label) && isMenuItemActive(item),
    );
});
const userEmail = computed(() => authUser.value?.email || "-");
const { navItems, isAdmin } = useNavigation();

const currentDb = computed(() => page.props.currentConnection || "sqlite");

const getInitials = (name) => {
    if (!name) return "U";

    return name
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

const logout = () => {
    router.post(route("logout"));
};

function handleAfterPrint() {
    window.focus();
    if (
        document.activeElement &&
        typeof document.activeElement.blur === "function"
    ) {
        document.activeElement.blur();
    }
}

onMounted(() => {
    window.addEventListener("afterprint", handleAfterPrint);
});

onUnmounted(() => {
    window.removeEventListener("afterprint", handleAfterPrint);
});
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100"
    >
        <Head :title="title" />

        <nav
            class="sticky top-0 z-50 border-b border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414] print:hidden"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="hidden md:block">
                            <Link
                                :href="route('dashboard')"
                                class="group inline-flex items-center gap-2.5 mb-1"
                            >
                                <img
                                    src="/CISS1.png"
                                    alt="Logo"
                                    class="h-7 w-auto transition-opacity group-hover:opacity-90"
                                />
                                <div class="hidden md:block">
                                    <p
                                        class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white leading-none"
                                    >
                                        Collaboration Information System
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <Link
                                            v-if="
                                                activeModul === 'all' ||
                                                activeModul === 'itsps'
                                            "
                                            :href="route('dashboard')"
                                            class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[9.5px] font-extrabold uppercase tracking-wider shadow-sm select-none transition-all duration-205"
                                            :class="[
                                                activeModul === 'all' &&
                                                isItomSubmenuActive
                                                    ? 'bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200 hover:text-slate-600 dark:bg-zinc-800/60 dark:text-zinc-500 dark:border-zinc-800 dark:hover:bg-zinc-700/60 dark:hover:text-zinc-300'
                                                    : 'bg-blue-500 text-white border-blue-500 dark:bg-blue-600 dark:border-blue-600',
                                            ]"
                                        >
                                            ITSPS
                                        </Link>
                                        <Link
                                            v-if="
                                                activeModul === 'all' ||
                                                activeModul === 'itom'
                                            "
                                            :href="
                                                route(
                                                    'itom.business-process.apqc.index',
                                                )
                                            "
                                            class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[9.5px] font-extrabold uppercase tracking-wider shadow-sm select-none transition-all duration-205"
                                            :class="[
                                                activeModul === 'all' &&
                                                isItspsSubmenuActive
                                                    ? 'bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200 hover:text-slate-600 dark:bg-zinc-800/60 dark:text-zinc-500 dark:border-zinc-800 dark:hover:bg-zinc-700/60 dark:hover:text-zinc-300'
                                                    : 'bg-indigo-500 text-white border-indigo-500 dark:bg-indigo-600 dark:border-indigo-600',
                                            ]"
                                        >
                                            ITOM
                                        </Link>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
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
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-indigo-600 text-xs font-semibold text-white"
                            >
                                {{ getInitials(displayName) }}
                            </span>
                            <div class="hidden max-w-[9rem] text-left sm:block">
                                <p
                                    class="truncate text-xs font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ displayName }}
                                </p>
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
                                    <p
                                        class="truncate text-sm font-semibold text-slate-900 dark:text-white"
                                    >
                                        {{ displayName }}
                                    </p>
                                    <p
                                        class="truncate text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{ userEmail }}
                                    </p>
                                </div>
                                <div class="p-1">
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider"
                                    >
                                        Kelola Modul
                                    </div>
                                    <MenuItem v-slot="{ active }">
                                        <div
                                            @click.stop="toggleItsps"
                                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold transition-colors"
                                            :class="[
                                                isItspsChecked
                                                    ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                                    : 'text-slate-700 dark:text-slate-300',
                                                active
                                                    ? 'bg-slate-100 dark:bg-white/5'
                                                    : '',
                                            ]"
                                        >
                                            <span
                                                class="flex items-center gap-2 select-none"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :checked="isItspsChecked"
                                                    readonly
                                                    class="pointer-events-none h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-[#121212] dark:focus:ring-offset-[#1d1d1d]"
                                                />
                                                ITSPS
                                            </span>
                                        </div>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                        <div
                                            @click.stop="toggleItom"
                                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold transition-colors"
                                            :class="[
                                                isItomChecked
                                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400'
                                                    : 'text-slate-700 dark:text-slate-300',
                                                active
                                                    ? 'bg-slate-100 dark:bg-white/5'
                                                    : '',
                                            ]"
                                        >
                                            <span
                                                class="flex items-center gap-2 select-none"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :checked="isItomChecked"
                                                    readonly
                                                    class="pointer-events-none h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-700 dark:bg-[#121212] dark:focus:ring-offset-[#1d1d1d]"
                                                />
                                                ITOM
                                            </span>
                                        </div>
                                    </MenuItem>
                                </div>
                                <div v-if="isAdmin" class="p-1">
                                    <MenuItem v-slot="{ active }">
                                        <Link
                                            :href="route('admin.dashboard')"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-indigo-600 transition-colors dark:text-indigo-300"
                                            :class="
                                                active
                                                    ? 'bg-indigo-50 dark:bg-indigo-500/10'
                                                    : ''
                                            "
                                        >
                                            <ShieldCheckIcon class="h-4 w-4" />
                                            Admin
                                        </Link>
                                    </MenuItem>
                                </div>
                                <div class="p-1">
                                    <MenuItem v-slot="{ active }">
                                        <Link
                                            :href="route('master-data.index')"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-indigo-600 transition-colors dark:text-indigo-300"
                                            :class="
                                                active
                                                    ? 'bg-indigo-50 dark:bg-indigo-500/10'
                                                    : ''
                                            "
                                        >
                                            <TableCellsIcon class="h-4 w-4" />
                                            Master Data
                                        </Link>
                                    </MenuItem>
                                </div>
                                <div class="p-1">
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            type="button"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors"
                                            :class="
                                                active
                                                    ? 'bg-red-50 dark:bg-red-500/10'
                                                    : ''
                                            "
                                            @click="logout"
                                        >
                                            <ArrowRightOnRectangleIcon
                                                class="h-4 w-4"
                                            />
                                            Keluar
                                        </button>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>

                    <!-- Database Connection Indicator -->
                    <div
                        class="hidden sm:flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors"
                        :class="
                            currentDb === 'sqlite'
                                ? 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-white/5 dark:text-slate-300 dark:border-white/10'
                                : 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20'
                        "
                    >
                        <CircleStackIcon
                            class="h-4 w-4 shrink-0"
                            :class="
                                currentDb === 'sqlite'
                                    ? 'text-slate-400 dark:text-slate-500'
                                    : 'text-indigo-500 dark:text-indigo-400'
                            "
                        />
                        <span class="text-slate-500 dark:text-slate-400"
                            >Data:</span
                        >
                        <span class="font-bold">
                            {{
                                currentDb === "sqlite"
                                    ? "Lokal (SQLite)"
                                    : "Master (Server)"
                            }}
                        </span>
                    </div>

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

            <div
                v-if="mobileMenuOpen"
                class="border-t border-slate-200 bg-white dark:border-white/10 dark:bg-[#141414] md:hidden"
            >
                <div class="space-y-1 px-4 py-4">
                    <template
                        v-for="item in navItems"
                        :key="`mobile-${item.href}`"
                    >
                        <div v-if="item.children && item.children.length > 0">
                            <Link
                                :href="item.href"
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                :class="
                                    isMenuItemActive(item)
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                        : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300'
                                "
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
                                    :class="
                                        isMenuItemActive(child)
                                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300'
                                    "
                                    @click="mobileMenuOpen = false"
                                >
                                    <component
                                        :is="child.icon"
                                        v-if="child.icon"
                                        class="h-4 w-4 shrink-0"
                                    />
                                    {{ child.label }}
                                </Link>
                            </div>
                        </div>
                        <Link
                            v-else
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="
                                isMenuItemActive(item)
                                    ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-300'
                            "
                            @click="mobileMenuOpen = false"
                        >
                            <component :is="item.icon" class="h-5 w-5" />
                            {{ item.label }}
                        </Link>
                    </template>

                    <div
                        class="my-2 border-t border-slate-200 dark:border-white/5"
                    ></div>

                    <!-- User Info & Logout -->
                    <div class="px-3 py-2">
                        <!-- Database Connection Indicator (Mobile) -->
                        <div
                            class="mb-4 flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors"
                            :class="
                                currentDb === 'sqlite'
                                    ? 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-white/5 dark:text-slate-300 dark:border-white/10'
                                    : 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20'
                            "
                        >
                            <CircleStackIcon
                                class="h-4 w-4 shrink-0"
                                :class="
                                    currentDb === 'sqlite'
                                        ? 'text-slate-400 dark:text-slate-500'
                                        : 'text-indigo-500 dark:text-indigo-400'
                                "
                            />
                            <span class="text-slate-500 dark:text-slate-400"
                                >Data:</span
                            >
                            <span class="font-bold">
                                {{
                                    currentDb === "sqlite"
                                        ? "Lokal (SQLite)"
                                        : "Master (Server)"
                                }}
                            </span>
                        </div>
                        <div class="mb-3 flex items-center gap-3">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-semibold text-white"
                            >
                                {{ getInitials(displayName) }}
                            </span>
                            <div>
                                <p
                                    class="text-sm font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ displayName }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ userEmail }}
                                </p>
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

        <div
            class="sticky top-16 z-40 print:hidden"
            v-if="
                activeModul === 'all' ||
                activeModul === 'itsps' ||
                activeModul === 'itom'
            "
        >
            <div
                class="flex flex-col gap-2 border-b border-white/50 bg-white/40 px-4 py-2 backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
            >
                <!-- Kiri: Program Planning / Program Implementation -->
                <div
                    v-if="
                        activeModul === 'itsps' ||
                        (activeModul === 'all' &&
                            (!isItomSubmenuActive || isItspsSubmenuActive))
                    "
                    class="w-full min-w-0 rounded-2xl border border-white/70 bg-white/60 px-3 py-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:w-auto"
                >
                    <div class="overflow-x-auto">
                        <div class="min-w-max">
                            <BreadcrumbLeft />
                        </div>
                    </div>
                </div>
                <!-- Kanan: Architecture / Policy -->
                <div
                    v-if="
                        activeModul === 'itom' ||
                        (activeModul === 'all' &&
                            (!isItspsSubmenuActive || isItomSubmenuActive))
                    "
                    class="w-full min-w-0 rounded-2xl border border-white/70 bg-white/60 px-3 py-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/5 sm:w-auto"
                >
                    <div
                        class="overflow-x-auto"
                        :class="{
                            'sm:flex sm:justify-end':
                                activeModul === 'all' &&
                                !isItspsSubmenuActive &&
                                !isItomSubmenuActive,
                        }"
                    >
                        <div class="min-w-max">
                            <BreadcrumbRight />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main
            class="mx-auto w-full max-w-7xl px-4 pt-5 pb-8 sm:px-6 lg:px-8 print:max-w-none print:px-0 print:py-0"
        >
            <slot />
        </main>

        <footer
            class="mx-auto w-full max-w-7xl px-4 pb-8 sm:px-6 lg:px-8 print:hidden"
        >
            <div class="border-t border-slate-200/70 pt-5 dark:border-white/5">
                <p
                    class="text-center text-xs text-slate-400 dark:text-slate-500"
                >
                    Collaboration System
                </p>
            </div>
        </footer>
    </div>
</template>
