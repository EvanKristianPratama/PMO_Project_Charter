<template>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ label }}</p>
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
    slate:   'text-slate-900',
    indigo:  'text-indigo-600',
    emerald: 'text-emerald-600',
    amber:   'text-amber-600',
    red:     'text-red-600',
};

const textColorClass = computed(() => colorMap[props.color] || colorMap.slate);
</script>
