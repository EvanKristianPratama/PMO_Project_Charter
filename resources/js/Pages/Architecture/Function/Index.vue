<template>
    <UserLayout title="Function">
        <div class="animate-fade-in-up space-y-3">
            <FunctionTable :functions="functions" :groubOptions="groubOptions" :regulations="regulations" />
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import FunctionTable from '@/Components/Architecture/Function/FunctionTable.vue';

defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});
</script>
