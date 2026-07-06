<template>
    <ModulLayout title="Operating Model - IT Governance">
        <div class="animate-fade-in-up space-y-2">
            <!-- Diagram Card -->
            <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50">
                <div class="overflow-hidden">
                    <Deferred :data="['steeringRows', 'organizationOptions']">
                        <template #fallback>
                            <TableSkeleton />
                        </template>
                        <ITSteeringComittee :steering-rows="steeringRows" :organization-options="organizationOptions" />
                    </Deferred>
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import ITSteeringComittee from '@/Components/modules/ITOM/OperatingModel/ItGovarnance/ITSteeringComittee.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    steeringRows: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});
</script>
