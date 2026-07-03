<template>
    <FungsiEditor
        ref="fungsiEditorRef"
        :actors="actors"
        :organizations="organizations"
        :activeRegulation="activeRegulation"
        :functions="functions"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import FungsiEditor from '@/Components/modules/ITOM/Regulation/Procedure/FungsiEditor.vue';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    activeRegulation: {
        type: Object,
        default: null,
    },
    functions: {
        type: Array,
        default: () => [],
    },
});

const fungsiEditorRef = ref(null);

const hasUnsavedChanges = computed(() => {
    const val = fungsiEditorRef.value?.hasUnsavedChanges;
    if (val && typeof val === 'object' && 'value' in val) {
        return val.value;
    }
    return !!val;
});

const saveAll = () => {
    if (fungsiEditorRef.value?.saveAll) {
        return fungsiEditorRef.value.saveAll();
    }
    return Promise.resolve();
};

defineExpose({
    hasUnsavedChanges,
    saveAll,
});
</script>
