<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="space-y-6 p-6">
                <!-- Work Package 1 Section -->
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#131313]">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[120px_1fr] sm:items-end">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Tipe
                                Initiative</label>
                            <select v-model.number="selectedRowType"
                                class="h-10 w-full rounded-md border-slate-300 bg-white px-3 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                                <option :value="null">Semua</option>
                                <option :value="1">Digital</option>
                                <option :value="2">IT</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">{{
                                resolvedLabels.rowSelectLabel }}</label>
                            <select v-model="form.initiative_code_row"
                                class="h-10 w-full rounded-md border-slate-300 bg-white px-4 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                                <option value="">{{ resolvedLabels.rowSelectPlaceholder }}</option>
                                <option :value="null">—</option>
                                <option v-for="initiative in filteredRowInitiatives" :key="initiative.id"
                                    :value="initiative.id">
                                    {{ initiativeLabel(initiative) }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <p v-if="form.errors.initiative_code_row" class="mt-2 text-xs text-red-500">{{
                        form.errors.initiative_code_row }}</p>
                </div>

                <!-- Work Package 1 Details -->
                <InitiativeDetailsWithRelations v-if="selectedInitiativeRow" :initiative="selectedInitiativeRow"
                    :relations="selectedRowRelations" variant="emerald" :status-label="resolvedLabels.statusHeader"
                    :relations-title="resolvedLabels.relationsTitle" column-a-label="Initiative A"
                    column-b-label="Initiative B" />
            </div>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="space-y-6 p-6">
                <!-- Work Package 2 Section -->
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#131313]">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[120px_1fr] sm:items-end">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Tipe
                                Initiative</label>
                            <select v-model.number="selectedColumnType"
                                class="h-10 w-full rounded-md border-slate-300 bg-white px-3 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                                <option :value="null">Semua</option>
                                <option :value="1">Digital</option>
                                <option :value="2">IT</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">{{
                                resolvedLabels.columnSelectLabel }}</label>
                            <select v-model="form.initiative_code_column"
                                class="h-10 w-full rounded-md border-slate-300 bg-white px-4 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                                <option value="">{{ resolvedLabels.columnSelectPlaceholder }}</option>
                                <option :value="null">—</option>
                                <option v-for="initiative in filteredColumnInitiatives" :key="initiative.id"
                                    :value="initiative.id">
                                    {{ initiativeLabel(initiative) }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <p v-if="form.errors.initiative_code_column" class="mt-2 text-xs text-red-500">{{
                        form.errors.initiative_code_column }}</p>
                </div>

                <!-- Work Package 2 Details -->
                <InitiativeDetailsWithRelations v-if="selectedInitiativeColumn" :initiative="selectedInitiativeColumn"
                    :relations="selectedColumnRelations" variant="blue" :status-label="resolvedLabels.statusHeader"
                    :relations-title="resolvedLabels.relationsTitle" column-a-label="Initiative A"
                    column-b-label="Initiative B" />
            </div>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div v-if="resolvedLabels.cardTitle" class="border-b border-slate-200 px-6 py-4 dark:border-white/10">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ resolvedLabels.cardTitle }}</h2>
                <p v-if="resolvedLabels.cardSubtitle" class="text-sm text-slate-500 dark:text-slate-400">{{
                    resolvedLabels.cardSubtitle }}</p>
            </div>

            <div class="space-y-6 p-6">
                <!-- Relation Type and Display Row -->
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#131313]">
                    <div class="grid grid-cols-1 gap-4 items-end sm:grid-cols-[1fr_160px_1fr]">
                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">{{
                                resolvedLabels.rowSelectedLabel }}</label>
                            <div
                                class="flex items-center justify-center h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-300">
                                {{ getSelectedLabel('row') || '-' }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tipe
                                Relasi</label>
                            <select v-model.number="form.type_relation"
                                class="h-10 w-full rounded-md border-slate-300 bg-white px-4 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                                <option :value="null">Pilih</option>
                                <option v-for="option in typeRelationOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.type_relation" class="mt-1 text-xs text-red-500">{{
                                form.errors.type_relation }}</p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">{{
                                resolvedLabels.columnSelectedLabel }}</label>
                            <div
                                class="flex items-center justify-center h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-300">
                                {{ getSelectedLabel('column') || '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relation Message -->
                <div v-if="relationMessage"
                    class="rounded-lg border-2 border-blue-300 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ relationMessage }}</p>
                </div>

                <!-- Justifikasi -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Justifikasi</label>
                    <textarea v-model="form.justifikasi" rows="4"
                        class="w-full rounded-md border-slate-300 bg-slate-50 px-4 py-3 shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                        placeholder="Tambahkan justifikasi relasi..."></textarea>
                    <p v-if="form.errors.justifikasi" class="mt-1 text-xs text-red-500">{{ form.errors.justifikasi }}
                    </p>
                </div>

                <!-- Model Relasi -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Model
                        Relasi</label>
                    <input v-model="form.model_relasi" list="model-relasi-options" type="text"
                        class="h-12 w-full rounded-md border-slate-300 bg-slate-50 px-4 text-base shadow-sm focus:border-slate-400 focus:ring-slate-300 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        placeholder="e.g. initiative" />
                    <datalist id="model-relasi-options">
                        <option v-for="option in modelRelationOptions" :key="option" :value="option" />
                    </datalist>
                    <p v-if="form.errors.model_relasi" class="mt-1 text-xs text-red-500">{{ form.errors.model_relasi }}
                    </p>
                </div>


                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-white/5">
                    <Link :href="initiativeRelationIndexPath"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 disabled:opacity-50 dark:bg-white dark:text-slate-900">
                        {{ resolvedSubmitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import { computed, ref, toRef, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import InitiativeDetailsWithRelations from './InitiativeDetailsWithRelations.vue';

const initiativeRelationIndexPath = '/program-planning/initiative-relation';

const props = defineProps({
    form: {
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
    submitLabel: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'create',
    },
    labels: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['submit']);

const form = toRef(props, 'form');
const selectedRowType = ref(null);
const selectedColumnType = ref(null);

const labelPresets = {
    create: {
        cardTitle: '',
        cardSubtitle: '',
        rowSelectLabel: 'Pilih Initiative A',
        rowSelectPlaceholder: 'Pilih Initiative',
        columnSelectLabel: 'Pilih Initiative B',
        columnSelectPlaceholder: 'Pilih Initiative',
        rowSelectedLabel: 'Initiative A Terpilih',
        columnSelectedLabel: 'Initiative B Terpilih',
        relationsTitle: 'Initiatives Relations Existing',
        statusHeader: 'Source',
    },
    edit: {
        cardTitle: 'Detail Relation',
        cardSubtitle: 'Perbarui data relasi di bawah.',
        rowSelectLabel: 'Work Package 1',
        rowSelectPlaceholder: 'Pilih work package',
        columnSelectLabel: 'Work Package 2',
        columnSelectPlaceholder: 'Pilih work package',
        rowSelectedLabel: 'Work Package 1 Terpilih',
        columnSelectedLabel: 'Work Package 2 Terpilih',
        relationsTitle: 'Relasi Initiative',
        statusHeader: 'Status',
    },
};

const resolvedLabels = computed(() => {
    const preset = labelPresets[props.variant] || labelPresets.create;
    return {
        ...preset,
        ...props.labels,
    };
});

const resolvedSubmitLabel = computed(() => {
    if (props.submitLabel) return props.submitLabel;
    return props.variant === 'edit' ? 'Save Changes' : 'Create Relation';
});

const selectedInitiativeRow = computed(() => {
    if (!form.value.initiative_code_row) return null;
    return props.initiativeOptions.find(opt => opt.id == form.value.initiative_code_row);
});

const selectedInitiativeColumn = computed(() => {
    if (!form.value.initiative_code_column) return null;
    return props.initiativeOptions.find(opt => opt.id == form.value.initiative_code_column);
});

const filterInitiativesByType = (initiatives, typeValue) => {
    if (!typeValue) return initiatives;
    return initiatives.filter((initiative) => Number(initiative?.tipe_initiative) === Number(typeValue));
};

const filteredRowInitiatives = computed(() => (
    filterInitiativesByType(props.initiativeOptions, selectedRowType.value)
));

const filteredColumnInitiatives = computed(() => (
    filterInitiativesByType(props.initiativeOptions, selectedColumnType.value)
));

watch(selectedRowType, (nextType) => {
    if (!form.value.initiative_code_row) return;
    const selected = props.initiativeOptions.find(opt => opt.id == form.value.initiative_code_row);
    if (!selected) return;
    if (nextType && Number(selected?.tipe_initiative) !== Number(nextType)) {
        form.value.initiative_code_row = '';
    }
});

watch(selectedColumnType, (nextType) => {
    if (!form.value.initiative_code_column) return;
    const selected = props.initiativeOptions.find(opt => opt.id == form.value.initiative_code_column);
    if (!selected) return;
    if (nextType && Number(selected?.tipe_initiative) !== Number(nextType)) {
        form.value.initiative_code_column = '';
    }
});

watch(selectedInitiativeRow, (initiative) => {
    if (selectedRowType.value != null) return;
    const typeValue = initiative?.tipe_initiative;
    if (typeValue != null) {
        selectedRowType.value = Number(typeValue);
    }
}, { immediate: true });

watch(selectedInitiativeColumn, (initiative) => {
    if (selectedColumnType.value != null) return;
    const typeValue = initiative?.tipe_initiative;
    if (typeValue != null) {
        selectedColumnType.value = Number(typeValue);
    }
}, { immediate: true });

const initiativeLabel = (initiative) => {
    const code = initiative?.code ?? initiative?.id ?? '-';
    const name = initiative?.name ?? '';
    const baseLabel = name ? `${code} - ${name}` : code;
    const coeName = initiative?.coe_name ?? initiative?.coe?.name ?? '';
    return coeName ? `${baseLabel} (CoE: ${coeName})` : baseLabel;
};

const buildRelations = (initiativeId) => {
    if (!initiativeId) return [];
    const id = Number(initiativeId);

    return props.initiativeRelations
        .filter(relation => (
            Number(relation.initiative_code_row) === id
            || Number(relation.initiative_code_column) === id
        ))
        .map((relation) => {
            const rowInitiative = relation.initiative_row;
            const columnInitiative = relation.initiative_column;
            const rowFallback = relation.initiative_code_row;
            const columnFallback = relation.initiative_code_column;
            const justifikasi = relation.justifikasi ?? '-';

            return {
                id: relation.id ?? `${relation.initiative_code_row}-${relation.initiative_code_column}-${relation.type_relation}-${relation.model_relasi}`,
                predecessorLabel: rowInitiative ? initiativeLabel(rowInitiative) : (rowFallback ?? '-'),
                successorLabel: columnInitiative ? initiativeLabel(columnInitiative) : (columnFallback ?? '-'),
                type_relation: relation.type_relation ?? null,
                modelRelasi: relation.model_relasi ?? '-',
                justifikasi,
            };
        });
};

const selectedRowRelations = computed(() => buildRelations(form.value.initiative_code_row));
const selectedColumnRelations = computed(() => buildRelations(form.value.initiative_code_column));

const getSelectedLabel = (type) => {
    const initiativeId = type === 'row' ? form.value.initiative_code_row : form.value.initiative_code_column;
    if (!initiativeId) return null;
    const initiative = props.initiativeOptions.find(opt => opt.id == initiativeId);
    return initiative ? initiativeLabel(initiative) : null;
};

const formatInitiativeType = (typeValue) => {
    const typeMap = {
        1: 'Digital',
        2: 'IT',
    };
    return typeMap[typeValue] || '-';
};

const relationMessage = computed(() => {
    const rowLabel = getSelectedLabel('row') || 'Initiative A';
    const columnLabel = getSelectedLabel('column') || 'Initiative B';
    const typeRelation = form.value.type_relation;

    if (!typeRelation || !form.value.initiative_code_row || !form.value.initiative_code_column) {
        return null;
    }

    if (typeRelation === 1) {
        return `${rowLabel} mempengaruhi ${columnLabel} sebagai predecessor.`;
    } else if (typeRelation === 2) {
        return `${rowLabel} dipengaruhi oleh ${columnLabel} sebagai successor.`;
    }

    return null;
});

const handleSubmit = () => {
    emit('submit');
};
</script>
