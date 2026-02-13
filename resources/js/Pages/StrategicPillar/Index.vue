<template>
    <UserLayout title="Strategic Pillars">
        <div class="animate-fade-in">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Strategic Pillars & Themes</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Strategic pillars and their associated themes</p>
            </div>

            <!-- Goals Table -->
            <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-300 dark:border-slate-600 overflow-hidden shadow-sm">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 border-b-2 border-slate-300 dark:border-slate-600">
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-600 w-24">
                                Code
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-600">
                                Strategic Pillar Title
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Themes
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pillar in strategicPillars" :key="pillar.id" class="border-b border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 border-r border-slate-300 dark:border-slate-600 text-center">
                                <span class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ pillar.code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-300 dark:border-slate-600 text-center align-center">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ pillar.title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <ol v-if="pillar.themes && pillar.themes.length > 0" class="list-decimal list-inside space-y-1.5 text-sm text-slate-700 dark:text-slate-200">
                                    <li v-for="theme in pillar.themes" :key="theme.id">
                                        {{ theme.name }}
                                    </li>
                                </ol>
                                <span v-else class="text-sm text-slate-400 dark:text-slate-500 italic">
                                    No themes available
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="!strategicPillars || strategicPillars.length === 0" class="text-center py-16 bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No strategic pillars found</h3>
                <p class="text-slate-500 dark:text-slate-400">Start by adding your strategic pillars and themes.</p>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    strategicPillars: {
        type: Array,
        default: () => [],
    },
});
</script>
