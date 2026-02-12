<template>
    <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 p-5">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ label }}</p>
        <p :class="['text-2xl font-bold mt-1', textColorClass]">{{ value }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [Number, String], required: true },
    color: { type: String, default: 'slate' },
});

const colorMap = {
    slate:   'text-slate-900 dark:text-white',
    indigo:  'text-indigo-600 dark:text-indigo-400',
    emerald: 'text-emerald-600 dark:text-emerald-400',
    amber:   'text-amber-600 dark:text-amber-400',
    red:     'text-red-600 dark:text-red-400',
};

const textColorClass = computed(() => colorMap[props.color] || colorMap.slate);
</script>
