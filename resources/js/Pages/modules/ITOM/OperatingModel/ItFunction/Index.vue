<template>
    <ModulLayout title="Operating Model - IT Function">
        <div class="animate-fade-in-up space-y-2">
            <Deferred data="functions">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <FunctionTable
                    :functions="functions"
                    :company-options="companyOptions"
                    :bod-options="bodOptions"
                    :regulations="regulations"
                />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import FunctionTable from '@/Components/modules/ITOM/BusinessProcess/Function/FunctionTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    bodOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});
</script>