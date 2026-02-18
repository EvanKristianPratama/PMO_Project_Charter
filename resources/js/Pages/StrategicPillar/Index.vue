<template>
    <UserLayout title="Strategic Pillars">
        <div class="animate-fade-in">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Strategic Pillars & Themes</h2>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            View by Goal:
                        </label>
                        <select 
                            v-model="selectedGoalId" 
                            @change="applyFilter"
                            class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm min-w-[250px]"
                        >
                            <option :value="null">All Strategic Pillars</option>
                            <option v-for="goal in allGoals" :key="goal.id" :value="goal.id">
                                {{ goal.code }} - {{ goal.title }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Goals Table -->
            <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-300 dark:border-slate-600 overflow-hidden shadow-sm">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 border-b-2 border-slate-300 dark:border-slate-600">
                            <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-600 w-24">
                                Code
                            </th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-600">
                                Strategic Pillar Title
                            </th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700 dark:text-slate-300">
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
                            <td class="p-0 align-top">
                                <!-- Themes Table -->
                                <div v-if="pillar.themes && pillar.themes.length > 0" class="w-full">
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr v-for="theme in pillar.themes" :key="theme.id" class="border-b border-slate-300 dark:border-slate-600 last:border-b-0">
                                                <td class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 text-center w-16 border-r border-slate-300 dark:border-slate-600">
                                                    {{ theme.theme_number }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-200">
                                                    {{ theme.name }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="px-6 py-4">
                                    <span class="text-sm text-slate-400 dark:text-slate-500 italic">
                                        No themes available
                                    </span>
                                </div>
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
                <p class="text-slate-500 dark:text-slate-400">
                    {{ selectedGoalId ? 'No data for the selected pillar.' : 'Start by adding your strategic pillars and themes.' }}
                </p>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    strategicPillars: {
        type: Array,
        default: () => [],
    },
    allGoals: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Initialize selected goal from filters
const selectedGoalId = ref(props.filters.goal_id ? Number(props.filters.goal_id) : null);

// Apply filter function
const applyFilter = () => {
    const url = selectedGoalId.value 
        ? `/strategic-pillars/${selectedGoalId.value}`
        : '/strategic-pillars';

    router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// Sync local state with prop changes (e.g. browser back button)
watch(() => props.filters.goal_id, (newVal) => {
    selectedGoalId.value = newVal ? Number(newVal) : null;
});
</script>