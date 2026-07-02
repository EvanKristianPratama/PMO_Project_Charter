<template>
    <GuidanceIndex v-bind="$props" />
</template>

<script setup>
import GuidanceIndex from '../Index.vue';

defineProps({
    roles: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    responsibles: {
        type: Array,
        default: () => [],
    },
    objectives: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});
</script>
