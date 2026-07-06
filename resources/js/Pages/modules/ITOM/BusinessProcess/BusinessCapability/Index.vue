<template>
    <ModulLayout title="Master Data - Business Capability">
        <div class="animate-fade-in-up space-y-3">
            <Deferred data="businessCapabilities">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <BusinessCapabilityTable :business-capabilities="businessCapabilities" />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import BusinessCapabilityTable from '@/Components/modules/ITOM/BusinessProcess/BusinessCapability/BusinessCapabilityTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    businessCapabilities: { type: Array, default: () => [] },
});
</script>
