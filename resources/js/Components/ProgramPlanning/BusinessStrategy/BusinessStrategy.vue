<template>
  <h2 class="text-base font-semibold text-slate-700 dark:text-slate-200 mt-4 mb-4">Prioritas Inisiatif Strategis Perusahaan 2025 -2029 Berdsarkan Dual Growth</h2>
  <div class="grid gap-4">
    <!-- Top: first two main goals -->
    <div v-for="goal in topMainGoals" :key="goal.id" class="rounded-lg bg-white shadow-sm dark:bg-[#171717]">
      <div class="px-4 py-2 bg-sky-100 dark:bg-sky-800/40">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-white">{{ goal.title }}</h3>
      </div>
      <div class="p-4">
        <ul class="space-y-3">
          <li v-for="strategy in strategiesByGoal(goal.id)" :key="strategy.id" class="flex gap-3 items-start">
            <div class="w-8 flex-shrink-0">
              <div
                class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-semibold">
                {{ strategy.code }}</div>
            </div>
            <div class="flex-1 text-sm text-slate-700 dark:text-slate-200">{{ strategy.strategy }}</div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Enabler container: styled like other goal cards but spans two columns -->
    <div v-if="enablerGoals.length" class="col-span-full md:col-span-2 lg:col-span-2">
      <div class="rounded-lg bg-white shadow-sm dark:bg-[#171717]">
        <div class="px-4 py-2 bg-sky-100 dark:bg-sky-800/40">
          <h3 class="text-sm font-semibold text-sky-800 dark:text-white">Enabler</h3>
        </div>
        <div class="p-4">
          <div v-for="goal in enablerGoals" :key="`enabler-${goal.id}`" class="mb-4">
            <h5 v-if="!isHeaderTitle(goal)" class="text-sm font-semibold text-slate-700 dark:text-white mb-1">{{
              goal.title }}</h5>
            <ul class="space-y-3">
              <li v-for="strategy in strategiesByGoal(goal.id)" :key="strategy.id" class="flex gap-3 items-start">
                <div class="w-8 flex-shrink-0">
                  <div
                    class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-semibold">
                    {{ strategy.code }}</div>
                </div>
                <div class="flex-1 text-sm text-slate-700 dark:text-slate-200">{{ strategy.strategy }}</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Remaining main goals (if any) rendered after enabler row -->
    <div v-for="goal in remainingMainGoals" :key="goal.id" class="rounded-lg bg-white shadow-sm dark:bg-[#171717]">
      <div class="px-4 py-2 bg-sky-100 dark:bg-sky-800/40">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-white">{{ goal.title }}</h3>
      </div>
      <div class="p-4">
        <ul class="space-y-3">
          <li v-for="strategy in strategiesByGoal(goal.id)" :key="strategy.id" class="flex gap-3 items-start">
            <div class="w-8 flex-shrink-0">
              <div
                class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-semibold">
                {{ strategy.code }}</div>
            </div>
            <div class="flex-1 text-sm text-slate-700 dark:text-slate-200">{{ strategy.strategy }}</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
  goals: { type: Array, default: () => [] },
  strategies: { type: Object, default: () => ({}) },
});

const strategiesByGoal = (goalId) => {
  const group = props.strategies?.[goalId] || props.strategies?.[String(goalId)] || [];
  return Array.isArray(group) ? group : Object.values(group);
};

const isEnabler = (goal) => {
  const title = String(goal?.title || '').toLowerCase();
  const code = String(goal?.code || '').toLowerCase();
  return title.includes('enabler') || code.includes('enabler');
};

const isHeaderTitle = (goal) => {
  return String(goal?.title || '').trim().toLowerCase() === 'enabler';
};

const mainGoals = computed(() => (props.goals || []).filter((g) => !isEnabler(g)));
const enablerGoals = computed(() => (props.goals || []).filter((g) => isEnabler(g)));

// Split main goals: top two (will sit above enabler), remaining shown after
const topMainGoals = computed(() => mainGoals.value.slice(0, 2));
const remainingMainGoals = computed(() => mainGoals.value.slice(2));
</script>

<style scoped>
/* small visual tweaks to resemble provided image */
</style>
