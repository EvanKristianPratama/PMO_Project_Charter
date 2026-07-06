<template>
    <ModulLayout title="Regulation">
        <div class="animate-fade-in-up space-y-6">
            <Deferred :data="['definitions', 'regulations']">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <DefinitionTable
                    :definitions="definitions"
                    :regulations="regulations"
                />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import DefinitionTable from '@/Components/modules/ITOM/Regulation/Definition/DefinitionTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    definitions: {
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
    animation: fadeInUp 0.4s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
