<template>
    <UserLayout title="IT Building Blocks">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-col gap-2">                    
                    <!-- View Mode Switcher -->
                    <div class="inline-flex items-center gap-1 rounded-xl bg-slate-200/50 p-1 dark:bg-white/5 w-fit">
                        <button
                            type="button"
                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                            :class="viewMode === 'mapping' 
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            @click="viewMode = 'mapping'"
                        >
                            IT Enabler
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                            :class="viewMode === 'digital-block' 
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            @click="viewMode = 'digital-block'"
                        >
                            Center of Excellence
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 text-[10px] font-bold tracking-wider rounded-lg transition-all"
                            :class="viewMode === 'block' 
                                ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-white/10 dark:text-white' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            @click="viewMode = 'block'"
                        >
                            IT Building Blocks
                        </button>
                    </div>
                </div>

                <div v-if="viewMode === 'mapping'" class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        v-if="!isEditMode"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                        @click="openAddMapping"
                    >
                        Tambah Primary
                    </button>
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

            <!-- Conditional View Rendering -->
            <div class="relative min-h-[400px]">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform opacity-0 translate-y-4"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-4"
                    mode="out-in"
                >
                    <ItBuildingBlocksMatrix
                        v-if="viewMode === 'mapping'"
                        ref="matrixRef"
                        :groups="groups"
                        :editable="isEditMode"
                        :coe-options="coeOptions"
                        :initiative-options="initiativeOptions"
                        @cancel-add-mapping="isEditMode = false"
                    />
                    <ItBuildingBlockViewBlockMode
                        v-else-if="viewMode === 'block'"
                        :items="initiativeOptions"
                        :coe-options="coeOptions"
                    />
                    <DigitalBuildingBlock
                        v-else-if="viewMode === 'digital-block'"
                        :items="digitalInitiativeOptions"
                        :coe-options="coeOptions"
                    />
                </Transition>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import ItBuildingBlocksMatrix from '@/Components/ItBuildingBlocks/ItBuildingBlocksMatrix.vue';
import ItBuildingBlockViewBlockMode from '@/Components/ItBuildingBlocks/ItBuildingBlockViewBlockMode.vue';
import DigitalBuildingBlock from '@/Components/ItBuildingBlocks/DigitalBuildingBlock.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const isEditMode = ref(false);
const viewMode = ref('mapping');
const matrixRef = ref(null);

const openAddMapping = async () => {
    if (!isEditMode.value) {
        isEditMode.value = true;
        await nextTick();
    }

    matrixRef.value?.openAddMappingModal?.({
        exitEditOnCancel: true,
    });
};

defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    digitalInitiativeOptions: {
        type: Array,
        default: () => [],
    },
});
</script>
