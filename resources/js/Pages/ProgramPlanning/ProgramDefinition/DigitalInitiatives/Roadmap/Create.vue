<template>
    <EditorPage page-mode="create" v-bind="props" />
</template>

<script setup>
import EditorPage from './EditorPage.vue';

const props = defineProps({
    milestones: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    startYearRange: { type: Number, default: 2024 },
    endYearRange: { type: Number, default: 2029 },
    selectedInitiativeId: { type: Number, default: null },
});
</script>
