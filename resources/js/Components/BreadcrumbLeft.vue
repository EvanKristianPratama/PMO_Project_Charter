<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();

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

const isMenuItemActive = (item, url = currentUrl.value) => {
    if (!item) return false;
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

const strategicHouseItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Strategic House') ?? null;
});

const programPlanningItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Program Planning') ?? null;
});

const programPlanningChildren = computed(() => {
    return programPlanningItem.value?.children || [];
});

const programImplementationItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Program Implementation') ?? null;
});

const programImplementationChildren = computed(() => {
    return programImplementationItem.value?.children || [];
});

const programInformationItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Program Evaluation') ?? null;
});

const showStrategicHouseChildren = computed(() => {
    if (isMenuItemActive(strategicHouseItem.value)) {
        return true;
    }

    return strategicHouseItem.value?.children?.some((item) => isMenuItemActive(item)) ?? false;
});

const showPlanningChildren = computed(() => {
    if (isMenuItemActive(programPlanningItem.value)) {
        return true;
    }

    return programPlanningChildren.value.some((item) => isMenuItemActive(item));
});

const showImplementationChildren = computed(() => {
    if (isMenuItemActive(programImplementationItem.value)) {
        return true;
    }

    return programImplementationChildren.value.some((item) => isMenuItemActive(item));
});

const showInformationChildren = computed(() => {
    if (isMenuItemActive(programInformationItem.value)) {
        return true;
    }

    return programInformationItem.value?.children?.some((item) => isMenuItemActive(item)) ?? false;
});
</script>

<template>
    <div class="inline-flex flex-col gap-1.5">
        <div class="inline-flex flex-wrap items-center gap-0.5">
            <Link
                v-if="strategicHouseItem"
                :href="strategicHouseItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showStrategicHouseChildren
                        ? 'bg-blue-500 text-white shadow-sm'
                        : 'text-blue-500 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/40 dark:hover:text-blue-200'
                ]"
            >
                <component :is="strategicHouseItem.icon" v-if="strategicHouseItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ strategicHouseItem.label }}</span>
            </Link>

            <span
                v-if="strategicHouseItem && (programPlanningItem || programImplementationItem || programInformationItem)"
                class="select-none px-0.5 text-blue-200 dark:text-blue-900"
            >
                &middot;
            </span>

            <Link
                v-if="programPlanningItem"
                :href="programPlanningItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showPlanningChildren
                        ? 'bg-blue-500 text-white shadow-sm'
                        : 'text-blue-500 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/40 dark:hover:text-blue-200'
                ]"
            >
                <component :is="programPlanningItem.icon" v-if="programPlanningItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ programPlanningItem.label }}</span>
            </Link>

            <span
                v-if="programPlanningItem && (programImplementationItem || programInformationItem)"
                class="select-none px-0.5 text-blue-200 dark:text-blue-900"
            >
                &middot;
            </span>

            <Link
                v-if="programImplementationItem"
                :href="programImplementationItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showImplementationChildren
                        ? 'bg-blue-500 text-white shadow-sm'
                        : 'text-blue-500 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/40 dark:hover:text-blue-200'
                ]"
            >
                <component :is="programImplementationItem.icon" v-if="programImplementationItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ programImplementationItem.label }}</span>
            </Link>

            <span
                v-if="programImplementationItem && programInformationItem"
                class="select-none px-0.5 text-blue-200 dark:text-blue-900"
            >
                &middot;
            </span>

            <Link
                v-if="programInformationItem"
                :href="programInformationItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showInformationChildren
                        ? 'bg-blue-500 text-white shadow-sm'
                        : 'text-blue-500 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/40 dark:hover:text-blue-200'
                ]"
            >
                <component :is="programInformationItem.icon" v-if="programInformationItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ programInformationItem.label }}</span>
            </Link>
        </div>

        <div v-if="showPlanningChildren || showImplementationChildren || showInformationChildren || showStrategicHouseChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in showStrategicHouseChildren ? (strategicHouseItem?.children || []) : showPlanningChildren ? programPlanningChildren : showImplementationChildren ? programImplementationChildren : programInformationItem?.children || []"
                :key="'left-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    isMenuItemActive(item)
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300'
                        : 'text-blue-500 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/40 dark:hover:text-blue-200'
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>
    </div>
</template>
