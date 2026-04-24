<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-col gap-4">
                <!-- Action Button Row -->
                <div class="flex items-center">
                    <button
                        v-if="!isEditMode"
                        type="button"
                        class="bu-toggle-btn"
                        @click="isEditMode = true"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Mapping</span>
                    </button>
                    <button
                        v-else
                        type="button"
                        class="bu-toggle-btn bu-toggle-btn--active"
                        @click="isEditMode = false"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Selesai Edit</span>
                    </button>
                </div>

                <!-- CoE Filter Row -->
                <div class="flex items-center gap-3">
                    <label for="coe-filter" class="text-xs font-bold text-slate-500 dark:text-slate-400">
                        Filter CoE:
                    </label>
                    <select
                        id="coe-filter"
                        v-model="selectedCoeId"
                        class="initiative-view-select"
                    >
                        <option value="">Semua CoE</option>
                        <option v-for="coe in coeOptions" :key="coe.id" :value="coe.id">
                            {{ coe.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="relative min-h-[400px]">
                <MapTechnology 
                    :map-technologies="mapTechnologies" 
                    :editable="isEditMode"
                    :coe-options="coeOptions"
                    :initiative-options="initiativeOptions"
                    :status-periods="statusPeriods"
                    :selected-coe-id="selectedCoeId"
                />
            </div>
        </div>
    </component>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import MapTechnology from '@/Components/StrategicHouse/MapTechnology/MapTechnology.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    mapTechnologies: {
        type: [Array, Object],
        default: () => ({}),
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const isEditMode = ref(false);
const selectedCoeId = ref('');

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Map Technology' }));
</script>

<style scoped>
.initiative-view-select {
    appearance: none;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 24px 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.initiative-view-select:hover {
    border-color: #1C75BC;
    color: #1C75BC;
}

.initiative-view-select:focus {
    outline: none;
    border-color: #1C75BC;
    box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
}

.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    transition: all 0.15s ease;
    cursor: pointer;
    flex-shrink: 0;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.bu-toggle-btn:hover {
    border-color: #1C75BC;
    background: #f8fafc;
    color: #1C75BC;
}

.bu-toggle-btn--active {
    background: #1C75BC;
    border-color: #1C75BC;
    color: #ffffff;
}

.bu-toggle-btn--active:hover {
    background: #155a96;
    border-color: #155a96;
    color: #ffffff;
}

:deep(.dark) .initiative-view-select {
    background-color: #0f172a;
    border-color: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
}

:deep(.dark) .bu-toggle-btn {
    background-color: transparent;
    border-color: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
}
</style>
