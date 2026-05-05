<template>
    <UserLayout title="Business Strategy">
        <div class="w-full max-w-full px-0">
            <nav class="mb-4">
                <ul class="flex space-x-2">
                    <li>
                        <button
                            type="button"
                            :class="['px-3 py-2 rounded', activeTab === defaultTab ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']"
                            @click="() => activeTab = defaultTab"
                        >
                            Prioritas Initiative Strategis Perusahaan 2025 -2029
                        </button>
                    </li>
                </ul>
            </nav>

            <div>
                <h2 class="text-lg font-semibold mb-3">{{ activeTab }}</h2>
                <BusinessStrategy :goals="goals" :strategies="strategies" :active-tab="activeTab" />
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BusinessStrategy from '@/Components/ProgramPlanning/BusinessStrategy/BusinessStrategy.vue';
import { ref } from 'vue';

const props = defineProps({
    goals: { type: Array, default: () => [] },
    strategies: { type: Object, default: () => ({}) },
});

const defaultTab = 'Prioritas Initiative Strategis Perusahaan 2025 -2029';
const activeTab = ref(defaultTab);
</script>

<style scoped>
/* minimal styling */
</style>
