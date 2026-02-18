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
        <div class="border-b border-slate-200/70 bg-white/80 backdrop-blur-sm dark:border-white/5 dark:bg-slate-900/80">
            <div class="mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Grup kiri: Program Planning - IT Initiatives -->
                <div class="inline-flex items-center gap-0.5 py-1">
                    <template v-for="(item, index) in leftNavItems" :key="'left-' + index">
                        <span v-if="index > 0" class="text-slate-300 dark:text-slate-600 select-none px-0.5">·</span>
                        <Link
                            :href="item.href"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                            :class="[
                                item.active(currentUrl)
                                    ? 'bg-blue-500 text-white dark:bg-blue-500 dark:text-white shadow-sm'
                                    : 'text-slate-500 hover:bg-slate-200/80 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-700/80 dark:hover:text-slate-200'
                            ]"
                        >
                            <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                            <span>{{ item.label }}</span>
                        </Link>
                    </template>
                </div>

                <!-- Grup kanan: Arsitektur - Company Profile (background terpisah) -->
                <div class="inline-flex items-center gap-0.5 py-1">
                    <template v-for="(item, index) in rightNavItems" :key="'right-' + index">
                        <span v-if="index > 0" class="text-slate-300 dark:text-slate-600 select-none px-0.5">·</span>
                        <Link
                            :href="item.href"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-medium transition-all duration-150"
                            :class="[
                                item.active(currentUrl)
                                    ? 'bg-blue-500 text-white dark:bg-blue-500 dark:text-white shadow-sm'
                                    : 'text-slate-500 hover:bg-slate-200/80 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-700/80 dark:hover:text-slate-200'
                            ]"
                        >
                            <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 shrink-0" />
                            <span>{{ item.label }}</span>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>