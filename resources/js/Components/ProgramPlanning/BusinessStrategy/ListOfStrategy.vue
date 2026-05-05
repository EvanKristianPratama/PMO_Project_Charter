<template>
  <div class="rounded-lg bg-white shadow-sm dark:bg-[#171717] p-4">
    <h3 class="text-sm font-semibold text-sky-800 dark:text-white mb-3">Prioritas Initiative Strategis Perusahaan 2025 - 2029</h3>
    <ul class="space-y-2">
      <li v-for="item in flatStrategies" :key="item._uniqueId" class="flex items-start gap-3">
        <div class="w-8 flex-shrink-0">
          <div class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-semibold">{{ item.code }}</div>
        </div>
        <div class="flex-1 text-sm text-slate-700 dark:text-slate-200">{{ item.strategy }}</div>
      </li>
      <li v-if="flatStrategies.length === 0" class="text-sm text-slate-500">Belum ada strategi.</li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  strategies: { type: [Object, Array], default: () => ({}) },
});

const flatStrategies = computed(() => {
  // Accept either an object keyed by goalId, or an array of strategies
  const s = props.strategies || {};

  if (Array.isArray(s)) {
    return s.map((it, idx) => ({ ...it, _uniqueId: it.id ?? idx }));
  }

  // s is object: combine all arrays/objects into one flat array
  const out = [];
  Object.values(s).forEach((group) => {
    if (!group) return;
    if (Array.isArray(group)) {
      group.forEach((it) => out.push({ ...it, _uniqueId: it.id ?? Math.random().toString(36).slice(2) }));
    } else if (typeof group === 'object') {
      Object.values(group).forEach((it) => out.push({ ...it, _uniqueId: it.id ?? Math.random().toString(36).slice(2) }));
    }
  });

  return out;
});
</script>

<style scoped>
/* keep compact list styling; font sizes follow parent utility classes */
</style>
