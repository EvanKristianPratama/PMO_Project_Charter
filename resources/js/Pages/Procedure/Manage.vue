<template>
    <ProcedureIndex v-bind="$attrs" />
</template>

<script setup>
import ProcedureIndex from './Index.vue';

defineOptions({
    inheritAttrs: false,
});
</script>
