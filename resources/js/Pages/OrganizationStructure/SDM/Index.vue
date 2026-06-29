<template>
    <ModulLayout title="Resource Management">
        <div class="animate-fade-in-up space-y-3">
            <ResourceMangementTable :resources="resources" :organizations="organizations" />
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import ResourceMangementTable from '@/Components/Resourcemanagement/ResourceMangementTable.vue';

defineProps({
    resources: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
});
</script>
