<template>
    <UserLayout title="Edit Initiative Relation">
        <div class="mx-auto max-w-[1200px] space-y-6 animate-fade-in-up">
            <div
                class="inline-flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs shadow-sm dark:border-white/10 dark:bg-[#1f1f1f]">

                <Link :href="initiativeRelationIndexPath"
                    class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 hover:text-slate-900 dark:text-slate-200 dark:hover:text-white">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>
                <span class="text-slate-300 dark:text-slate-600">|</span>
                <div class="flex flex-wrap items-center gap-2">
                    <label for="tipe-initiative" class="text-xs font-medium text-slate-700 dark:text-slate-200">Tipe
                        Initiative</label>
                    <select v-model="selectedTipeInitiative"
                        class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option :value="null">Semua</option>
                        <option :value="1">Digital</option>
                        <option :value="2">IT</option>
                    </select>
                    <span class="text-slate-300 dark:text-slate-600">|</span>
                    <label for="initiative"
                        class="text-xs font-medium text-slate-700 dark:text-slate-200">Initiative</label>
                    <select v-if="filteredInitiatives.length" v-model="selectedInitiativeId"
                        class="max-w-xs rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option value="">Initiative</option>
                        <option v-for="opt in filteredInitiatives" :key="opt.id" :value="String(opt.id)">
                            {{ initiativeLabel(opt) }}
                        </option>
                    </select>
                </div>
            </div>

            <InitiativeRelationFormCard variant="edit" submit-label="Save Changes" :form="form"
                :initiative-options="initiativeOptions" :initiative-relations="initiativeRelations"
                :type-relation-options="typeRelationOptions" :model-relation-options="modelRelationOptions"
                @submit="submit" />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import InitiativeRelationFormCard from '@/Components/modules/ITSP/InitiativeRelation/InitiativeRelationFormCard.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const route = useRouteHelper();
const initiativeRelationIndexPath = route('itsp.initiative-relations.index');
const initiativeRelationEditPath = (initiativeRelationId) => route('itsp.initiative-relations.edit', initiativeRelationId);

const props = defineProps({
    initiativeRelation: {
        type: Object,
        required: true,
    },
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

const selectedTipeInitiative = ref(null);

const filteredInitiatives = computed(() => {
    if (selectedTipeInitiative.value == null) return props.initiativeOptions;
    const type = Number(selectedTipeInitiative.value);
    return props.initiativeOptions.filter(
        (opt) => Number(opt?.tipe_initiative) === type,
    );
});

const findRelationByInitiative = (initiativeId) =>
    props.initiativeRelations.find(
        (r) =>
            Number(r.initiative_code_row) === Number(initiativeId) ||
            Number(r.initiative_code_column) === Number(initiativeId),
    );

watch(
    () => props.initiativeRelation,
    (r) => {
        if (!r) return;
        const opt = props.initiativeOptions.find(
            (o) =>
                Number(o.id) === Number(r.initiative_code_row) ||
                Number(o.id) === Number(r.initiative_code_column),
        );
        if (selectedTipeInitiative.value == null && opt?.tipe_initiative != null) {
            selectedTipeInitiative.value = Number(opt.tipe_initiative);
        }
    },
    { immediate: true },
);

const selectedInitiativeId = computed({
    get: () => {
        const r = props.initiativeRelation;
        if (!r) return '';
        const rowId = Number(r.initiative_code_row);
        const colId = Number(r.initiative_code_column);
        const filtered = filteredInitiatives.value;
        if (filtered.some((o) => Number(o.id) === rowId)) return String(rowId);
        if (filtered.some((o) => Number(o.id) === colId)) return String(colId);
        return '';
    },
    set: (value) => {
        if (!value) return;
        const rel = findRelationByInitiative(value);
        if (!rel || Number(rel.id) === Number(props.initiativeRelation?.id)) return;
        router.visit(initiativeRelationEditPath(rel.id));
    },
});

const initiativeLabel = (opt) => {
    const code = opt?.code ?? opt?.id ?? '-';
    const name = opt?.name ?? '';
    return name ? `${code} - ${name}` : String(code);
};

const form = useForm({
    model_relasi: props.initiativeRelation?.model_relasi ?? '',
    type_relation: props.initiativeRelation?.type_relation ?? null,
    initiative_code_row: props.initiativeRelation?.initiative_code_row != null
        ? Number(props.initiativeRelation.initiative_code_row)
        : '',
    initiative_code_column: props.initiativeRelation?.initiative_code_column != null
        ? Number(props.initiativeRelation.initiative_code_column)
        : '',
    justifikasi: props.initiativeRelation?.justifikasi ?? '',
});

const submit = () => {
    form.put(route('itsp.initiative-relations.update', props.initiativeRelation.id));
};
</script>
