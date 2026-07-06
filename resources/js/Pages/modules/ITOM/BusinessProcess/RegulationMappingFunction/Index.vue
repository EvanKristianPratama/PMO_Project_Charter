<template>
    <ModulLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <Deferred :data="['functions', 'regulations']">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <RegulationMap
                    :functions="functions"
                    :regulations="regulations"
                />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import RegulationMap from '@/Components/modules/ITOM/BusinessProcess/RegulationMap/RegulationMap.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    regulations: {
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
