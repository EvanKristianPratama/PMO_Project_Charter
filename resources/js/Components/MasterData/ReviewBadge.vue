<template>
    <span v-if="status"
        class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium"
        :class="badgeClass">
        {{ status }}
    </span>
    <span v-else class="text-slate-400">-</span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: { type: String, default: null },
});

const badgeClass = computed(() => {
    const map = {
        'On Track':    'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300',
        'At Risk':     'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
        'Not Started': 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
        'Not Signed':  'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    };
    return map[props.status] ?? 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300';
});
</script>
