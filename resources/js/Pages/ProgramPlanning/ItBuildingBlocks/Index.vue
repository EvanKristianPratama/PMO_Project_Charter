<template>
    <UserLayout title="IT Building Blocks">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">IT Enabler</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Kelola mapping IT Building Blocks dan Center of Excellence.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
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

            <div class="relative min-h-[400px]">
                <ItBuildingBlocksMatrix
                    ref="matrixRef"
                    :groups="groups"
                    :editable="isEditMode"
                    :coe-options="coeOptions"
                    :initiative-options="initiativeOptions"
                    @cancel-add-mapping="isEditMode = false"
                />
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import ItBuildingBlocksMatrix from '@/Components/ItBuildingBlocks/ItBuildingBlocksMatrix.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const isEditMode = ref(false);
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
});
</script>

