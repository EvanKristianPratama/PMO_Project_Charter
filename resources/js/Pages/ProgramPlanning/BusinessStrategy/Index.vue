<template>
    <UserLayout title="Business Strategy">
        <div class="w-full max-w-full px-0">
            <BusinessStrategy :goals="goals" :strategies="strategies" />
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BusinessStrategy from '@/Components/ProgramPlanning/BusinessStrategy/BusinessStrategy.vue';

const props = defineProps({
    goals: { type: Array, default: () => [] },
    strategies: { type: Object, default: () => ({}) },
});
</script>

<style scoped>
/* minimal styling */
</style>
