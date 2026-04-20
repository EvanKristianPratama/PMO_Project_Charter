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
            </div>

            <InitiativeRelationDependency
                :mst-initiatives="mstInitiatives"
                :model-relation-options="modelRelationOptions"
                @edit-relation="goToEdit"
            />

        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import InitiativeRelationDependency from '@/Components/InitiativeRelation/InitiativeRelationDependency.vue';
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

const initiativeRelationCreatePath = route('initiative-relations.create');
const initiativeRelationEditPath = (initiativeRelationId) => route('initiative-relations.edit', initiativeRelationId);
const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Initiative Relation' }));

function goToEdit({ relation }) {
    const id = relation?.id;
    if (id != null) {
        router.visit(initiativeRelationEditPath(id));
    }
}
</script>
