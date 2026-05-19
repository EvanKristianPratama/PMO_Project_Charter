<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url || '');

const architectureItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Architecture') ?? null;
});

const architectureChildren = computed(() => {
    return architectureItem.value?.children || [];
});

const showArchitectureChildren = computed(() => {
    if (architectureItem.value?.active(currentUrl.value)) {
        return true;
    }

    return architectureChildren.value.some((item) => item.active(currentUrl.value));
});

const policyItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Policy') ?? null;
});

const policyChildren = computed(() => {
    return policyItem.value?.children || [];
});

const showPolicyChildren = computed(() => {
    if (policyItem.value?.active(currentUrl.value)) {
        return true;
    }

    return policyChildren.value.some((item) => item.active(currentUrl.value));
});

const adminItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Admin') ?? null;
});
</script>

<template>
    <div class="inline-flex flex-col gap-1.5">
        <div class="inline-flex flex-wrap items-center gap-0.5">
            <Link
                v-if="architectureItem"
                :href="architectureItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showArchitectureChildren
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="architectureItem.icon" v-if="architectureItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ architectureItem.label }}</span>
            </Link>

            <span
                v-if="policyItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <Link
                v-if="policyItem"
                :href="policyItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showPolicyChildren
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="policyItem.icon" v-if="policyItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ policyItem.label }}</span>
            </Link>

            <span
                v-if="adminItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <Link
                v-if="adminItem"
                :href="adminItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    adminItem.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="adminItem.icon" v-if="adminItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ adminItem.label }}</span>
            </Link>
        </div>

        <div v-if="showArchitectureChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in architectureChildren"
                :key="'right-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3 w-3 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>

        <div v-if="showPolicyChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in policyChildren"
                :key="'right-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3 w-3 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>
    </div>
</template>
