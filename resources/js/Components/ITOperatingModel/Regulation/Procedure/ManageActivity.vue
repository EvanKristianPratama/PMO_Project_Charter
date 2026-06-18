<template>
    <ProsedurEditor
        ref="prosedurEditorRef"
        :categories="categories"
        :sop="sop"
        :flowChartSops="flowChartSops"
        :actors="actors"
        :activeRegulation="activeRegulation"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import ProsedurEditor from '@/Components/Procedure/ProsedurEditor.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    flowChartSops: {
        type: Array,
        default: () => [],
    },
    actors: {
        type: Array,
        default: () => [],
    },
    activeRegulation: {
        type: Object,
        default: null,
    },
});

const prosedurEditorRef = ref(null);

const hasUnsavedChanges = computed(() => {
    const val = prosedurEditorRef.value?.hasUnsavedChanges;
    if (val && typeof val === 'object' && 'value' in val) {
        return val.value;
    }
    return !!val;
});

const saveAll = () => {
    if (prosedurEditorRef.value?.saveAll) {
        return prosedurEditorRef.value.saveAll();
    }
    return Promise.resolve();
};

defineExpose({
    hasUnsavedChanges,
    saveAll,
});
</script>
