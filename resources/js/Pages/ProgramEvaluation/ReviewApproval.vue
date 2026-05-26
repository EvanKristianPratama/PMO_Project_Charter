<script setup>
import Index from './ReviewApproval/Index.vue';

defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <Index v-bind="$attrs" />
</template>
