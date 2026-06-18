<template>
    <UserLayout title="Resource Management">
        <div class="animate-fade-in-up space-y-3">
            <ResourceMangementTable :resources="resources" :organizations="organizations" />
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
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
