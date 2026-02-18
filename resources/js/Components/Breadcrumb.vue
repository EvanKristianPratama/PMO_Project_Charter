<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url);

// Grup kiri: Program Planning s/d IT Initiatives (index 0-4)
const leftNavItems = computed(() => navItems.value.slice(0, 5));
// Grup kanan: Asitecture s/d Company Profile (+ Admin jika ada)
const rightNavItems = computed(() => navItems.value.slice(5));
</script>

<template>
    <div class="sticky top-16 z-40 print:hidden">
        <div class="mx-auto flex items-center justify-between px-4 py-1.5 sm:px-6 lg:px-8">
            <!-- Grup kiri: Program Planning - IT Initiatives -->
            <div class="inline-flex items-center gap-0.5 bg-gray-200 dark:bg-slate-800 rounded-r-lg px-3 py-1">
                <template v-for="(item, index) in leftNavItems" :key="'left-' + index">
                    <span v-if="index > 0" class="mx-1 text-[11px] text-slate-300 dark:text-slate-600">/</span>
                    <Link
                        :href="item.href"
                        class="inline-flex shrink-0 items-center gap-1 rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors"
                        :class="[
                            item.active(currentUrl)
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                : 'text-slate-500 dark:text-slate-400'
                        ]"
                    >
                        <component :is="item.icon" v-if="item.icon" class="h-3 w-3" />
                        <span>{{ item.label }}</span>
                    </Link>
                </template>
            </div>

            <!-- Grup kanan: Asitecture - Company Profile -->
            <div class="inline-flex items-center gap-0.5 bg-gray-200 dark:bg-slate-800 rounded-l-lg px-3 py-1">
                <template v-for="(item, index) in rightNavItems" :key="'right-' + index">
                    <span v-if="index > 0" class="mx-1 text-[11px] text-slate-300 dark:text-slate-600">/</span>
                    <Link
                        :href="item.href"
                        class="inline-flex shrink-0 items-center gap-1 rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors"
                        :class="[
                            item.active(currentUrl)
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                : 'text-slate-500 dark:text-slate-400'
                        ]"
                    >
                        <component :is="item.icon" v-if="item.icon" class="h-3 w-3" />
                        <span>{{ item.label }}</span>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>