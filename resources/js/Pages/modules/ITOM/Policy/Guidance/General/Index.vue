<template>
    <GuidanceIndex v-bind="$props" />
</template>

<script setup>
import GuidanceIndex from '../Index.vue';

defineProps({
    policies: {
        type: Array,
        required: true,
    },
    objectives: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});
</script>
