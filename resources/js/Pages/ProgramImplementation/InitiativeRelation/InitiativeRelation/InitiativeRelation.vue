<template>
    <UserLayout title="Initiative Relation">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Initiatives Relations</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Kelola dependensi antar initiative.</p>
                </div>
                <Link
                    href="/program-implementation/initiative-relation/create"
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
    </UserLayout>
</template>

<script setup>
defineProps({
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

import InitiativeRelationDependency from '@/Components/InitiativeRelation/InitiativeRelationDependency.vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

function goToEdit({ relation }) {
    const id = relation?.id;
    if (id != null) {
        router.visit(`/program-implementation/initiative-relation/${id}/edit`);
    }
}
</script>
