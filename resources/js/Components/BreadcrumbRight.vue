<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url);

const rightScopeLabels = ['Architecture', 'Resources Management', 'Policy', 'Admin'];

// Menu kanan khusus scope architecture/policy (+ admin jika ada).
const rightNavItems = computed(() => {
    return navItems.value.filter((item) => rightScopeLabels.includes(item.label));
});
</script>

<template>
    <div class="inline-flex items-center gap-0.5">
        <template v-for="(item, index) in rightNavItems" :key="'right-' + index">
            <span v-if="index > 0" class="select-none px-0.5 text-indigo-200 dark:text-indigo-900">·</span>
            <Link
                :href="item.href"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                :class="[
                    item.active(currentUrl)
                        ? 'bg-indigo-500 text-white shadow-sm'
                        : 'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-200'
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ item.label }}</span>
            </Link>
        </template>
    </div>
</template>
