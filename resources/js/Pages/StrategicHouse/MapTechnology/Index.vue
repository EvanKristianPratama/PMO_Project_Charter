<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="!isEditMode"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="isEditMode = true"
                    >
                        Edit Mapping
                    </button>
                    <button
                        v-else
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="isEditMode = false"
                    >
                        Selesai Edit
                    </button>
                </div>
            </div>

            <div class="relative min-h-[400px]">
                <MapTechnology 
                    :map-technologies="mapTechnologies" 
                    :editable="isEditMode"
                    :coe-options="coeOptions"
                    :initiative-options="initiativeOptions"
                    :status-periods="statusPeriods"
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

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Map Technology' }));
</script>
