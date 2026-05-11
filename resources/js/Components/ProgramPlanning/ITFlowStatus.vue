<template>
    <FlowStatusCard :title="title" :steps="steps" />
</template>

<script setup>
import { computed } from 'vue';
import FlowStatusCard from '@/Components/ProgramPlanning/FlowStatusCard.vue';

const props = defineProps({
    steps: {
        type: Array,
        default: () => [],
    },
    charterLabel: {
        type: String,
        default: 'Scope Charter',
    },
    timelinePhase: {
        type: String,
        default: 'Planning',
    },
});

const title = computed(() => `IT Initiatives ${props.timelinePhase} Timelines Status (${props.charterLabel})`);
</script>
