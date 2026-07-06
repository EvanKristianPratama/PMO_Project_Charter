<template>
    <ModulLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <Deferred :data="['kpiList', 'companyOptions']">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <KpiTable
                    :kpi-list="kpiList"
                    :company-options="companyOptions"
                />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import KpiTable from '@/Components/modules/ITOM/BusinessProcess/Kpi/KpiTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    kpiList: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
