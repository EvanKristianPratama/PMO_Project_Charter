<template>
    <ModulLayout title="Operating Model - IT Management">
        <div class="animate-fade-in-up space-y-2">
            <!-- Diagram Card -->
            <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50">
                <div class="overflow-hidden">
                    <Deferred data="organizationStructureRows">
                        <template #fallback>
                            <TableSkeleton />
                        </template>
                        <EITOrganization :organization-structure-rows="organizationStructureRows" />
                    </Deferred>
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import EITOrganization from '@/Components/modules/ITOM/OperatingModel/ItManagement/EITOrganization.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});
</script>
