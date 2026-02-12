<template>
    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', badgeClass]">
        {{ label }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: { type: String, required: true },
});

const statusConfig = {
    approved: { class: 'bg-emerald-50 text-emerald-700', label: 'Approved' },
    pending:  { class: 'bg-amber-50 text-amber-700',     label: 'Pending' },
    rejected: { class: 'bg-red-50 text-red-700',         label: 'Rejected' },
};

const config = computed(() => statusConfig[props.status] || statusConfig.pending);
const badgeClass = computed(() => config.value.class);
const label = computed(() => config.value.label);
</script>
