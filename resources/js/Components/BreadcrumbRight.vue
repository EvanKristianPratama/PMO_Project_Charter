<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useNavigation } from "@/Composables/useNavigation";

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

const sectionLabels = [
    "Business Process",
    "Organization",
    "Operating Model",
    "Regulation",
    "RACI Analysis",
    "DMS",
    "Admin",
];

const sections = computed(() => {
    return sectionLabels
        .map(
            (label) =>
                navItems.value.find((item) => item.label === label) ?? null,
        )
        .filter(Boolean)
        .map((item) => {
            const children = (item.children || []).map((child) => ({
                ...child,
                isActive: isMenuItemActive(child),
            }));
            const isActive = isMenuItemActive(item);
            const hasActiveChild = children.some((child) => child.isActive);

            return {
                ...item,
                children,
                isActive,
                showChildren: isActive || hasActiveChild,
            };
        });
});

const sectionsWithChildren = computed(() => {
    return sections.value.filter(
        (section) => section.children.length > 0 && section.showChildren,
    );
});
</script>

<template>
    <div class="inline-flex flex-col gap-1.5">
        <div class="inline-flex flex-wrap items-center gap-0.5">
            <template v-for="(section, index) in sections" :key="section.label">
                <span
                    v-if="index > 0"
                    class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
                >
                    &middot;
                </span>

                <Link
                    :href="section.href"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                    :class="[
                        section.showChildren || section.isActive
                            ? 'bg-indigo-500 text-white shadow-sm'
                            : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                    ]"
                >
                    <component
                        :is="section.icon"
                        v-if="section.icon"
                        class="h-3.5 w-3.5 shrink-0"
                    />
                    <span>{{ section.label }}</span>
                </Link>
            </template>
        </div>

        <div
            v-for="section in sectionsWithChildren"
            :key="`submenu-${section.label}`"
            class="ml-2 inline-flex flex-wrap items-center gap-1"
        >
            <Link
                v-for="item in section.children"
                :key="`${section.label}-${item.label}`"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.isActive
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component
                    :is="item.icon"
                    v-if="item.icon"
                    class="h-3.5 w-3.5 shrink-0"
                />
                <span>{{ item.label }}</span>
            </Link>
        </div>
    </div>
</template>
