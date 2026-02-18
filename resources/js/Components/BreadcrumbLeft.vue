<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url);

const planningChildLabels = ['Strategic Pillars', 'Digital Initiatives', 'IT Initiatives'];

const leftScopeItems = computed(() => navItems.value.slice(0, 5));

const programPlanningItem = computed(() => {
    return leftScopeItems.value.find((item) => item.label === 'Program Planning') ?? null;
});

const programPlanningChildren = computed(() => {
    return leftScopeItems.value.filter((item) => planningChildLabels.includes(item.label));
});

const otherLeftItems = computed(() => {
    return leftScopeItems.value.filter((item) => item.label !== 'Program Planning' && !planningChildLabels.includes(item.label));
});

const showPlanningChildren = computed(() => {
    if (programPlanningItem.value?.active(currentUrl.value)) {
        return true;
    }

    return programPlanningChildren.value.some((item) => item.active(currentUrl.value));
});
</script>

<template>
    <div class="inline-flex flex-col gap-1.5">
        <div class="inline-flex items-center gap-0.5">
            <Link
                v-if="programPlanningItem"
                :href="programPlanningItem.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    showPlanningChildren
                        ? 'bg-blue-500 text-white shadow-sm'
                        : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'
                ]"
            >
                <component :is="programPlanningItem.icon" v-if="programPlanningItem.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ programPlanningItem.label }}</span>
            </Link>

            <template v-for="(item, index) in otherLeftItems" :key="'left-other-' + index">
                <span class="select-none px-0.5 text-slate-300 dark:text-slate-600">·</span>
                <Link
                    :href="item.href"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                    :class="[
                        item.active(currentUrl)
                            ? 'bg-blue-500 text-white shadow-sm'
                            : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'
                    ]"
                >
                    <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                    <span>{{ item.label }}</span>
                </Link>
            </template>
        </div>

        <div v-if="showPlanningChildren" class="ml-2 inline-flex flex-wrap items-center gap-1">
            <Link
                v-for="item in programPlanningChildren"
                :key="'left-child-' + item.label"
                :href="item.href"
                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl)
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300'
                        : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200'
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3 w-3 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </div>
    </div>
</template>
