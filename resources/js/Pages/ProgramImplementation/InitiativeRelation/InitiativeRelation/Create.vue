<template>
    <UserLayout title="Create Initiative Relation">
        <div class="mx-auto max-w-5xl space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">

                <Link
                    href="/program-implementation/initiative-relation"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    Kembali
                </Link>
            </div>

            <InitiativeRelationFormCard
                variant="create"
                :form="form"
                :initiative-options="initiativeOptions"
                :initiative-relations="initiativeRelations"
                :type-relation-options="typeRelationOptions"
                :model-relation-options="modelRelationOptions"
                @submit="submit"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import InitiativeRelationFormCard from '@/Components/InitiativeRelation/InitiativeRelationFormCard.vue';

const props = defineProps({
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeRelations: {
        type: Array,
        default: () => [],
    },
    typeRelationOptions: {
        type: Array,
        default: () => [],
    },
    modelRelationOptions: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    model_relasi: '',
    type_relation: null,
    initiative_code_row: '',
    initiative_code_column: '',
    justifikasi: '',
});

const submit = () => {
    form.post('/program-implementation/initiative-relation');
};
</script>
