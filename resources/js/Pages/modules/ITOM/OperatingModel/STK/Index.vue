<template>
    <ModulLayout title="Operating Model - STK">
        <div class="animate-fade-in-up space-y-2">
            <Deferred data="prosesBisnisV2">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <BusinessProcessV2
                    :proses-bisnis-v2="prosesBisnisV2"
                    :company-options="companyOptions"
                    :kpi-list="kpiList"
                    :regulations="regulations"
                />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import BusinessProcessV2 from '@/Components/modules/ITOM/BusinessProcess/BusinessProcessV2/BusinessProcessV2.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    prosesBisnisV2: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    kpiList: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});
</script>