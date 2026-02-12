<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => ['pending', 'approved', 'rejected', 'deactivated'].includes(value)
    }
});

const config = computed(() => {
    switch (props.status) {
        case 'approved':
            return {
                bg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                dot: 'bg-emerald-500',
                label: 'Approved'
            };
        case 'pending':
            return {
                bg: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                dot: 'bg-amber-500',
                label: 'Pending'
            };
        case 'rejected':
            return {
                bg: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                dot: 'bg-red-500',
                label: 'Rejected'
            };
        case 'deactivated':
            return {
                bg: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                dot: 'bg-gray-500',
                label: 'Deactivated'
            };
        default:
            return {
                bg: 'bg-slate-100 text-slate-700',
                dot: 'bg-slate-500',
                label: props.status
            };
    }
});
</script>

<template>
    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', config.bg]">
        <span :class="['w-1.5 h-1.5 rounded-full mr-1.5', config.dot]"></span>
        {{ config.label }}
    </span>
</template>
