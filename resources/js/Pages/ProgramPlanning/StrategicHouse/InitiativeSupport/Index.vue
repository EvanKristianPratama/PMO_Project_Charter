<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="!isEditMode"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                        @click="openAddSupport"
                    >
                        Tambah Support
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

            <InitiativeSupport
                ref="initiativeSupportRef"
                :groups="processedGroups"
                :editable="isEditMode"
                :digital-options="digitalInitiativeOptions"
                :it-options="itInitiativeOptions"
                @cancel-add-support="isEditMode = false"
            />
        </div>
    </component>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue';
import InitiativeSupport from '@/Components/InitiativeSupport/InitiativeSupport.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    groups: {
        type: Array,
        default: () => [],
    },
    digitalInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    itInitiativeOptions: {
        type: Array,
        default: () => [],
    },
});

const isEditMode = ref(false);
const initiativeSupportRef = ref(null);

/**
 * Mengelompokkan dan mengurutkan data berdasarkan urutan asli database (insertion order).
 */
const processedGroups = computed(() => {
    if (!props.groups || props.groups.length === 0) return [];
    
    // Data dari backend sudah diurutkan berdasarkan urutan asli database
    return props.groups;
});

const openAddSupport = async () => {
    if (!isEditMode.value) {
        isEditMode.value = true;
        await nextTick();
    }

    initiativeSupportRef.value?.openAddSupportModal?.({
        exitEditOnCancel: true,
    });
};

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Initiative Support' }));
</script>
