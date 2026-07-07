<template>
    <ModulLayout title="Resource Management">
        <div class="animate-fade-in-up space-y-3">
            <Deferred :data="['resources', 'organizations']">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <ResourceMangementTable :resources="resources" :organizations="organizations" />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import { Deferred } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import ResourceMangementTable from '@/Components/modules/ITOM/Resourcemanagement/ResourceMangementTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';

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
