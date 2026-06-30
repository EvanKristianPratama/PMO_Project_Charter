<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url || '');

const architectureItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Business Process') ?? null;
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

const organizationItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Organization') ?? null;
});

const organizationChildren = computed(() => {
    return organizationItem.value?.children || [];
});

const showOrganizationChildren = computed(() => {
    if (organizationItem.value?.active(currentUrl.value)) {
        return true;
    }

    return organizationChildren.value.some((item) => item.active(currentUrl.value));
});

const policyItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Regulation') ?? null;
});

const operatingModelItem = computed(() => {
    return navItems.value.find((item) => item.label === 'Operating Model') ?? null;
});

const isOperatingModelActive = computed(() => {
    return operatingModelItem.value?.active(currentUrl.value) || false;
});

const operatingModelChildren = computed(() => {
    return operatingModelItem.value?.children || [];
});

const showOperatingModelChildren = computed(() => {
    if (isOperatingModelActive.value) {
        return true;
    }

    return operatingModelChildren.value.some((item) => item.active(currentUrl.value));
});

const raciAnalysisItem = computed(() => {
    return navItems.value.find((item) => item.label === 'RACI Analysis') ?? null;
});





const policyChildren = computed(() => {
    const children = policyItem.value?.children || [];
    return children
        .filter(item => item.label === 'Regulation' || item.label === 'Organization' || item.label === 'Matriks RACI' || item.label === 'BPMN' || item.label === 'DMS' || item.label === 'CMS')
        .map(item => {
            if (item.label === 'Matriks RACI') {
                return { ...item, label: 'RACI' };
            }
            return item;
        });
});

const isPolicyActive = computed(() => {
    return policyItem.value?.active(currentUrl.value) || false;
});

const showPolicyChildren = computed(() => {
    if (isPolicyActive.value) {
        return true;
    }

    return policyChildren.value.some((item) => item.active(currentUrl.value));
});

const isRaciAnalysisActive = computed(() => {
    return raciAnalysisItem.value?.active(currentUrl.value) || false;
});

const raciAnalysisChildren = computed(() => {
    return raciAnalysisItem.value?.children || [];
});

const showRaciAnalysisChildren = computed(() => {
    if (isRaciAnalysisActive.value) {
        return true;
    }

    return raciAnalysisChildren.value.some((item) => item.active(currentUrl.value));
});

// adminItem removed
</script>

<template>
    <div class="inline-flex flex-col gap-1.5">
        <div class="inline-flex flex-wrap items-center gap-0.5">
            <!-- Architecture Link -->
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

            <!-- Separation Dot -->
            <span
                v-if="organizationItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <!-- Organization Link -->
            <Link
                v-if="organizationItem"
                :href="organizationItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showOrganizationChildren
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="organizationItem.icon" v-if="organizationItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ organizationItem.label }}</span>
            </Link>

            <!-- Separation Dot -->
            <span
                v-if="operatingModelItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <!-- Operating Model Link -->
            <Link
                v-if="operatingModelItem"
                :href="operatingModelItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    isOperatingModelActive
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="operatingModelItem.icon" v-if="operatingModelItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ operatingModelItem.label }}</span>
            </Link>

            <!-- Separation Dot -->
            <span
                v-if="policyItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <!-- Regulation Link -->
            <Link
                v-if="policyItem"
                :href="policyItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    isPolicyActive
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="policyItem.icon" v-if="policyItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ policyItem.label }}</span>
            </Link>

            <!-- Separation Dot -->
            <span
                v-if="raciAnalysisItem"
                class="select-none px-0.5 text-indigo-200 dark:text-indigo-900"
            >
                &middot;
            </span>

            <!-- Raci Analysis Link -->
            <Link
                v-if="raciAnalysisItem"
                :href="raciAnalysisItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    isRaciAnalysisActive
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="raciAnalysisItem.icon" v-if="raciAnalysisItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ raciAnalysisItem.label }}</span>
            </Link>






        </div>

        <!-- Architecture Sub-menus -->
        <div v-if="showArchitectureChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in architectureChildren"
                :key="'right-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>

        <!-- Organization Sub-menus -->
        <div v-if="showOrganizationChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in organizationChildren"
                :key="'org-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>

        <!-- Operating Model Sub-menus -->
        <div v-if="showOperatingModelChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in operatingModelChildren"
                :key="'opmodel-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>

        <!-- Regulation Sub-menus (Regulasi and RACI) -->
        <div v-if="showPolicyChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in policyChildren"
                :key="'policy-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>

        <!-- Raci Analysis Sub-menus -->
        <div v-if="showRaciAnalysisChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in raciAnalysisChildren"
                :key="'raci-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl.value)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200',
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>
    </div>
</template>
