<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useNavigation } from '@/Composables/useNavigation';

const { navItems } = useNavigation();
const page = usePage();
const currentUrl = computed(() => page.url);
</script>

<template>
    <div class="sticky top-16 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl shadow-sm dark:border-white/5 dark:bg-[#171717]/90 print:hidden">
        <div class="mx-auto flex items-center gap-0.5 overflow-x-auto px-4 py-1.5 sm:px-6 lg:px-8">
            <template v-for="(item, index) in navItems" :key="index">
                <!-- Separator (only if not first item) -->
                <span v-if="index > 0" class="mx-1 text-[11px] text-slate-300 dark:text-slate-600">/</span>

                <!-- Breadcrumb Item -->
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
</template>
