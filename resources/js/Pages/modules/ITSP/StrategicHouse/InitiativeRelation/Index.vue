<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <Link
                    :href="initiativeRelationCreatePath"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                >
                    Tambah Relation
                </Link>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5"
                    @click="isEditMode = !isEditMode"
                >
                    <svg v-if="isEditMode" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ isEditMode ? 'Batal Edit Posisi' : 'Edit Posisi' }}
                </button>
            </div>

            <InitiativeRelationDependency
                :mst-initiatives="mstInitiatives"
                :model-relation-options="modelRelationOptions"
                :can-edit-positions="isEditMode"
                @edit-relation="goToEdit"
            />

        </div>
    </component>
</template>

<script setup>
import { computed, ref } from 'vue';
import InitiativeRelationDependency from '@/Components/modules/ITSP/InitiativeRelation/InitiativeRelationDependency.vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
    initiativeRelations: {
        type: Array,
        default: () => [],
    },
    modelRelationOptions: {
        type: Array,
        default: () => [],
    },
});

const initiativeRelationCreatePath = route('itsp.initiative-relations.create');
const initiativeRelationEditPath = (initiativeRelationId) => route('itsp.initiative-relations.edit', initiativeRelationId);
const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Initiative Relation' }));

const isEditMode = ref(false);

function goToEdit({ relation }) {
    const id = relation?.id;
    if (id != null) {
        router.visit(initiativeRelationEditPath(id));
    }
}
</script>
