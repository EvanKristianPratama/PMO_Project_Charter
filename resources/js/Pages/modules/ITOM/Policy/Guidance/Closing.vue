<template>
    <GuidanceIndex v-bind="$props" />
</template>

<script setup>
import GuidanceIndex from './Index.vue';

defineProps({
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
